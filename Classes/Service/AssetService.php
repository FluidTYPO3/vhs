<?php
namespace FluidTYPO3\Vhs\Service;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Asset;
use FluidTYPO3\Vhs\Utility\CoreUtility;
use FluidTYPO3\Vhs\ViewHelpers\Asset\AssetInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Asset Handling Service
 *
 * Inject this Service in your class to access VHS Asset
 * features - include assets etc.
 */
class AssetService implements SingletonInterface
{
    /**
     * @var string
     */
    const ASSET_SIGNAL = 'writeAssetFile';

    /**
     * @var boolean
     */
    protected static $typoScriptAssetsBuilt = false;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected static $settingsCache = null;

    /**
     * @var array
     */
    protected static $cachedDependencies = [];

    /**
     * @var boolean
     */
    protected static $cacheCleared = false;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param object $caller
     * @param boolean $shouldUsePageCache
     * @return boolean
     */
    public function usePageCache($caller, $shouldUsePageCache)
    {
        $this->buildAll([], $caller);
        return $shouldUsePageCache;
    }

    /**
     * @param array $parameters
     * @param object $caller
     * @param boolean $cached If TRUE, treats this inclusion as happening in a cached context
     * @param string|null $content
     * @return void
     */
    public function buildAll(array $parameters, $caller, $cached = true, &$content = null)
    {
        if ($content === null) {
            $content = &$caller->content;
        }

        $settings = $this->getSettings();
        $cached = (boolean) $cached;
        $buildTypoScriptAssets = (!static::$typoScriptAssetsBuilt && ($cached || $GLOBALS['TSFE']->no_cache));
        if ($buildTypoScriptAssets && isset($settings['asset']) && is_array($settings['asset'])) {
            foreach ($settings['asset'] as $name => $typoScriptAsset) {
                if (!isset($GLOBALS['VhsAssets'][$name]) && is_array($typoScriptAsset)) {
                    if (!isset($typoScriptAsset['name'])) {
                        $typoScriptAsset['name'] = $name;
                    }
                    Asset::createFromSettings($typoScriptAsset);
                }
            }
            static::$typoScriptAssetsBuilt = true;
        }
        if (empty($GLOBALS['VhsAssets']) || !is_array($GLOBALS['VhsAssets'])) {
            return;
        }
        $assets = $GLOBALS['VhsAssets'];
        $assets = $this->sortAssetsByDependency($assets);
        $assets = $this->manipulateAssetsByTypoScriptSettings($assets);
        $buildDebugRequested = (isset($settings['asset']['debugBuild']) && $settings['asset']['debugBuild'] > 0);
        $assetDebugRequested = (isset($settings['asset']['debug']) && $settings['asset']['debug'] > 0);
        $useDebugUtility = (isset($settings['asset']['useDebugUtility']) && $settings['asset']['useDebugUtility'] > 0)
            || false === isset($settings['asset']['useDebugUtility']);
        if (true === ($buildDebugRequested || $assetDebugRequested)) {
            if (true === $useDebugUtility) {
                DebuggerUtility::var_dump($assets);
            } else {
                echo var_export($assets, true);
            }
        }
        $this->placeAssetsInHeaderAndFooter($assets, $cached, $content);
    }

    /**
     * @param array $parameters
     * @param object $caller
     * @param string|null $content
     * @return void
     */
    public function buildAllUncached(array $parameters, $caller, ?string &$content = null)
    {
        if ($content === null) {
            $content = &$caller->content;
        }
        $matches = [];
        preg_match_all('/\<\![\-]+\ VhsAssetsDependenciesLoaded ([^ ]+) [\-]+\>/i', $content, $matches);
        foreach ($matches[1] as $key => $match) {
            $extractedDependencies = explode(',', $matches[1][$key]);
            static::$cachedDependencies = array_merge(static::$cachedDependencies, $extractedDependencies);
        }

        $this->buildAll($parameters, $caller, false, $content);
    }

    /**
     * Returns the settings used by this particular Asset
     * during inclusion. Public access allows later inspection
     * of the TypoScript values which were applied to the Asset.
     *
     * @return array
     */
    public function getSettings()
    {
        if (null === static::$settingsCache) {
            $allTypoScript = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            $settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
            static::$settingsCache = GeneralUtility::removeDotsFromTS(
                $allTypoScript['plugin.']['tx_vhs.']['settings.'] ?? []
            );
        }
        $settings = (array) static::$settingsCache;
        return $settings;
    }

    /**
     * @param AssetInterface[]|array[] $assets
     * @param boolean $cached
     * @param string $content
     * @return void
     */
    protected function placeAssetsInHeaderAndFooter($assets, $cached, &$content)
    {
        $settings = $this->getSettings();
        $header = [];
        $footer = [];
        $footerRelocationEnabled = (isset($settings['enableFooterRelocation']) && $settings['relocateToFooter'] > 0)
            || !isset($settings['enableFooterRelocation']);
        foreach ($assets as $name => $asset) {
            if ($asset instanceof AssetInterface) {
                $variables = $asset->getVariables();
            } else {
                $variables = $asset['variables'] ?? [];
            }

            if (0 < count($variables)) {
                $name .= '-' . md5(serialize($variables));
            }
            if (true === ($this->assertAssetAllowedInFooter($asset) && $footerRelocationEnabled)) {
                $footer[$name] = $asset;
            } else {
                $header[$name] = $asset;
            }
        }
        if (false === $cached) {
            $uncachedSuffix = 'Uncached';
        } else {
            $uncachedSuffix = '';
            $dependenciesString = '<!-- VhsAssetsDependenciesLoaded ' . implode(',', array_keys($assets)) . ' -->';
            $this->insertAssetsAtMarker('DependenciesLoaded', $dependenciesString, $content);
        }
        $this->insertAssetsAtMarker('Header' . $uncachedSuffix, $header, $content);
        $this->insertAssetsAtMarker('Footer' . $uncachedSuffix, $footer, $content);
        $GLOBALS['VhsAssets'] = [];
    }

    /**
     * @param string $markerName
     * @param mixed $assets
     * @param string $content
     * @return void
     */
    protected function insertAssetsAtMarker($markerName, $assets, &$content)
    {
        $assetMarker = '<!-- VhsAssets' . $markerName . ' -->';

        if (true === is_array($assets)) {
            $chunk = $this->buildAssetsChunk($assets);
        } else {
            $chunk = $assets;
        }

        if (false === strpos($content, $assetMarker)) {
            $inFooter = (boolean) (false !== strpos($markerName, 'Footer'));
            $tag = true === $inFooter ? '</body>' : '</head>';
            $position = strrpos($content, $tag);

            if ($position) {
                $content = substr_replace($content, LF . $chunk, $position, 0);
            }
        } else {
            $content = str_replace($assetMarker, $assetMarker . LF . $chunk, $content);
        }
    }

    /**
     * @param array $assets
     * @throws \RuntimeException
     * @return string
     */
    protected function buildAssetsChunk($assets)
    {
        $spool = [];
        foreach ($assets as $name => $asset) {
            $assetSettings = $this->extractAssetSettings($asset);
            $type = $assetSettings['type'];
            if (false === isset($spool[$type])) {
                $spool[$type] = [];
            }
            $spool[$type][$name] = $asset;
        }
        $chunks = [];
        /**
         * @var string $type
         * @var AssetInterface[] $spooledAssets
         */
        foreach ($spool as $type => $spooledAssets) {
            $chunk = [];
            foreach ($spooledAssets as $name => $asset) {
                $assetSettings = $this->extractAssetSettings($asset);
                $standalone = (boolean) $assetSettings['standalone'];
                $external = (boolean) $assetSettings['external'];
                $rewrite = (boolean) $assetSettings['rewrite'];
                $path = $assetSettings['path'];
                if (false === $standalone) {
                    $chunk[$name] = $asset;
                } else {
                    if (0 < count($chunk)) {
                        $mergedFileTag = $this->writeCachedMergedFileAndReturnTag($chunk, $type);
                        $chunks[] = $mergedFileTag;
                        $chunk = [];
                    }
                    if (true === empty($path)) {
                        $assetContent = $this->extractAssetContent($asset);
                        $chunks[] = $this->generateTagForAssetType($type, $assetContent, null, null, $assetSettings);
                    } else {
                        if (true === $external) {
                            $chunks[] = $this->generateTagForAssetType($type, null, $path, null, $assetSettings);
                        } else {
                            if (true === $rewrite) {
                                $chunks[] = $this->writeCachedMergedFileAndReturnTag([$name => $asset], $type);
                            } else {
                                $integrity = $this->getFileIntegrity($path);
                                $path = mb_substr($path, mb_strlen(CoreUtility::getSitePath()));
                                $path = $this->prefixPath($path);
                                $chunks[] = $this->generateTagForAssetType(
                                    $type,
                                    null,
                                    $path,
                                    $integrity,
                                    $assetSettings
                                );
                            }
                        }
                    }
                }
                unset($integrity);
            }
            if (0 < count($chunk)) {
                $mergedFileTag = $this->writeCachedMergedFileAndReturnTag($chunk, $type);
                $chunks[] = $mergedFileTag;
            }
        }
        return implode(LF, $chunks);
    }

    /**
     * @param AssetInterface[] $assets
     * @param string $type
     * @return string|null
     */
    protected function writeCachedMergedFileAndReturnTag($assets, $type)
    {
        $source = '';
        $keys = array_keys($assets);
        sort($keys);
        $assetName = implode('-', $keys);
        unset($keys);
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename'])) {
            if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename']) {
                $assetName = md5($assetName);
            }
        }
        $fileRelativePathAndFilename = $this->getTempPath() . 'vhs-assets-' . $assetName . '.' . $type;
        $fileAbsolutePathAndFilename = $this->resolveAbsolutePathForFile($fileRelativePathAndFilename);
        if (!file_exists($fileAbsolutePathAndFilename)
            || 0 === filemtime($fileAbsolutePathAndFilename)
            || isset($GLOBALS['BE_USER'])
            || (boolean) ($GLOBALS['TSFE']->no_cache ?? false)
            || (boolean) ($GLOBALS['TSFE']->page['no_cache'] ?? false)
        ) {
            foreach ($assets as $name => $asset) {
                $assetSettings = $this->extractAssetSettings($asset);
                if ((isset($assetSettings['namedChunks']) && 0 < $assetSettings['namedChunks']) ||
                    !isset($assetSettings['namedChunks'])) {
                    $source .= '/* ' . $name . ' */' . LF;
                }
                $source .= $this->extractAssetContent($asset) . LF;
                // Put a return carriage between assets preventing broken content.
                $source .= "\n";
            }
            $this->writeFile($fileAbsolutePathAndFilename, $source);
        }
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'])) {
            $timestampMode = $GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'];
            if (file_exists($fileRelativePathAndFilename)) {
                $lastModificationTime = filemtime($fileRelativePathAndFilename);
                if ('querystring' === $timestampMode) {
                    $fileRelativePathAndFilename .= '?' . $lastModificationTime;
                } elseif ('embed' === $timestampMode) {
                    $fileRelativePathAndFilename = substr_replace(
                        $fileRelativePathAndFilename,
                        '.' . $lastModificationTime,
                        (int) strrpos($fileRelativePathAndFilename, '.'),
                        0
                    );
                }
            }
        }
        $fileRelativePathAndFilename = $this->prefixPath($fileRelativePathAndFilename);
        $integrity = $this->getFileIntegrity($fileAbsolutePathAndFilename);

        $assetSettings = null;
        if (count($assets) === 1) {
            $extractedAssetSettings = $this->extractAssetSettings($assets[array_keys($assets)[0]]);
            if ($extractedAssetSettings['standalone']) {
                $assetSettings = $extractedAssetSettings;
            }
        }

        return $this->generateTagForAssetType($type, null, $fileRelativePathAndFilename, $integrity, $assetSettings);
    }

    /**
     * @param string $type
     * @param string|null $content
     * @param string $file
     * @param string $integrity
     * @param array|null $standaloneAssetSettings
     * @throws \RuntimeException
     * @return string|null
     */
    protected function generateTagForAssetType(
        $type,
        $content,
        $file = null,
        $integrity = null,
        array $standaloneAssetSettings = null
    ) {
        /** @var TagBuilder $tagBuilder */
        $tagBuilder = GeneralUtility::makeInstance(TagBuilder::class);
        if (null === $file && true === empty($content)) {
            $content = '<!-- Empty tag content -->';
        }
        switch ($type) {
            case 'js':
                $tagBuilder->setTagName('script');
                $tagBuilder->forceClosingTag(true);
                $tagBuilder->addAttribute('type', 'text/javascript');
                if (null === $file) {
                    $tagBuilder->setContent((string) $content);
                } else {
                    $tagBuilder->addAttribute('src', (string) $file);
                }
                if (null !== $integrity && !empty($integrity)) {
                    if (!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
                        $tagBuilder->addAttribute('crossorigin', 'anonymous');
                    }
                    $tagBuilder->addAttribute('integrity', $integrity);
                }
                if ($standaloneAssetSettings) {
                    // using async and defer simultaneously does not make sense technically, but do not enforce
                    if ($standaloneAssetSettings['async']) {
                        $tagBuilder->addAttribute('async', 'async');
                    }
                    if ($standaloneAssetSettings['defer']) {
                        $tagBuilder->addAttribute('defer', 'defer');
                    }
                }
                break;
            case 'css':
                if (null === $file) {
                    $tagBuilder->setTagName('style');
                    $tagBuilder->forceClosingTag(true);
                    $tagBuilder->addAttribute('type', 'text/css');
                    $tagBuilder->setContent((string) $content);
                } else {
                    $tagBuilder->forceClosingTag(false);
                    $tagBuilder->setTagName('link');
                    $tagBuilder->addAttribute('rel', 'stylesheet');
                    $tagBuilder->addAttribute('href', $file);
                }
                if (null !== $integrity && !empty($integrity)) {
                    if (!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['settings.']['prependPath'])) {
                        $tagBuilder->addAttribute('crossorigin', 'anonymous');
                    }
                    $tagBuilder->addAttribute('integrity', $integrity);
                }
                break;
            case 'meta':
                $tagBuilder->forceClosingTag(false);
                $tagBuilder->setTagName('meta');
                break;
            default:
                if (null === $file) {
                    return $content;
                }
                throw new \RuntimeException(
                    'Attempt to include file based asset with unknown type ("' . $type . '")',
                    1358645219
                );
        }
        return $tagBuilder->render();
    }

    /**
     * @param AssetInterface[]|array[] $assets
     * @return AssetInterface[]|array[]
     * @throws \RuntimeException
     */
    protected function manipulateAssetsByTypoScriptSettings($assets)
    {
        $settings = $this->getSettings();
        if (false === (isset($settings['asset']) || isset($settings['assetGroup']))) {
            return $assets;
        }
        $filtered = [];
        foreach ($assets as $name => $asset) {
            $assetSettings = $this->extractAssetSettings($asset);
            $groupName = $assetSettings['group'];
            $removed = (boolean) (true === isset($assetSettings['removed']) ? $assetSettings['removed'] : false);
            if ($removed) {
                continue;
            }
            $localSettings = (array) $assetSettings;
            if (isset($settings['asset'])) {
                $localSettings = $this->mergeArrays($localSettings, (array) $settings['asset']);
            }
            if (isset($settings['asset'][$name])) {
                $localSettings = $this->mergeArrays($localSettings, (array) $settings['asset'][$name]);
            }
            if (isset($settings['assetGroup'][$groupName])) {
                $localSettings = $this->mergeArrays($localSettings, (array) $settings['assetGroup'][$groupName]);
            }
            if ($asset instanceof Asset) {
                $asset->setSettings($localSettings);
                $filtered[$name] = $asset;
            } else {
                $filtered[$name] = $assetSettings;
            }
        }
        return $filtered;
    }

    /**
     * @param AssetInterface[] $assets
     * @throws \RuntimeException
     * @return AssetInterface[]
     */
    protected function sortAssetsByDependency($assets)
    {
        $placed = [];
        $assetNames = (0 < count($assets)) ? array_combine(array_keys($assets), array_keys($assets)) : [];
        while ($asset = array_shift($assets)) {
            $postpone = false;
            /** @var AssetInterface $asset */
            $assetSettings = $this->extractAssetSettings($asset);
            $name = array_shift($assetNames);
            $dependencies = $assetSettings['dependencies'];
            if (false === is_array($dependencies)) {
                $dependencies = GeneralUtility::trimExplode(',', $assetSettings['dependencies'], true);
            }
            foreach ($dependencies as $dependency) {
                if (true === array_key_exists($dependency, $assets)
                    && false === isset($placed[$dependency])
                    && false === in_array($dependency, static::$cachedDependencies)
                ) {
                    // shove the Asset back to the end of the queue, the dependency has
                    // not yet been encountered and moving this item to the back of the
                    // queue ensures it will be encountered before re-encountering this
                    // specific Asset
                    if (0 === count($assets)) {
                        throw new \RuntimeException(
                            sprintf(
                                'Asset "%s" depends on "%s" but "%s" was not found',
                                $name,
                                $dependency,
                                $dependency
                            ),
                            1358603979
                        );
                    }
                    $assets[$name] = $asset;
                    $assetNames[$name] = $name;
                    $postpone = true;
                }
            }
            if (false === $postpone) {
                $placed[$name] = $asset;
            }
        }
        return $placed;
    }

    /**
     * @param AssetInterface|array $asset
     * @return string
     */
    protected function renderAssetAsFluidTemplate($asset)
    {
        $settings = $this->extractAssetSettings($asset);
        if (isset($settings['variables']) && is_array($settings['variables'])) {
            $variables =  $settings['variables'];
        } else {
            $variables = [];
        }
        $contents = $this->buildAsset($asset);
        if ($contents === null) {
            return '';
        }
        $variables = GeneralUtility::removeDotsFromTS($variables);
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateSource($contents);
        $view->assignMultiple($variables);
        $content = $view->render();
        return $content;
    }

    /**
     * Prefix a path according to "absRefPrefix" TS configuration.
     *
     * @param string $fileRelativePathAndFilename
     * @return string
     */
    protected function prefixPath($fileRelativePathAndFilename)
    {
        $settings = $this->getSettings();
        $prefixPath = $settings['prependPath'] ?? '';
        if (!empty($GLOBALS['TSFE']->absRefPrefix) && empty($prefixPath)) {
            $fileRelativePathAndFilename = $GLOBALS['TSFE']->absRefPrefix . $fileRelativePathAndFilename;
        } elseif (!empty($prefixPath)) {
            $fileRelativePathAndFilename = $prefixPath . $fileRelativePathAndFilename;
        }
        return $fileRelativePathAndFilename;
    }

    /**
     * Fixes the relative paths inside of url() references in CSS files
     *
     * @param string $contents Data to process
     * @param string $originalDirectory Original location of file
     * @return string Processed data
     */
    protected function detectAndCopyFileReferences($contents, $originalDirectory)
    {
        if (false !== stripos($contents, 'url')) {
            $regex = '/url(\\(\\s*["\']?(?!\\/)([^"\']+)["\']?\\s*\\))/iU';
            $contents = $this->copyReferencedFilesAndReplacePaths($contents, $regex, $originalDirectory, '(\'|\')');
        }
        if (false !== stripos($contents, '@import')) {
            $regex = '/@import\\s*(["\']?(?!\\/)([^"\']+)["\']?)/i';
            $contents = $this->copyReferencedFilesAndReplacePaths($contents, $regex, $originalDirectory, '"|"');
        }
        return $contents;
    }

    /**
     * Finds and replaces all URLs by using a given regex
     *
     * @param string $contents Data to process
     * @param string $regex Regex used to find URLs in content
     * @param string $originalDirectory Original location to CSS file, if file based.
     * @param string $wrap Wrap around replaced values
     * @return string Processed data
     */
    protected function copyReferencedFilesAndReplacePaths($contents, $regex, $originalDirectory, $wrap = '|')
    {
        $matches = [];
        $replacements = [];
        $wrap = explode('|', $wrap);
        preg_match_all($regex, $contents, $matches);
        $logger = null;
        if (class_exists(LogManager::class)) {
            /** @var LogManager $logManager */
            $logManager = GeneralUtility::makeInstance(LogManager::class);
            $logger = $logManager->getLogger(__CLASS__);
        }
        foreach ($matches[2] as $matchCount => $match) {
            $match = trim($match, '\'" ');
            if (false === strpos($match, ':') && !preg_match('/url\\s*\\(/i', $match)) {
                $checksum = md5($originalDirectory . $match);
                if (0 < preg_match('/([^\?#]+)(.+)?/', $match, $items)) {
                    list(, $path, $suffix) = $items;
                } else {
                    $path = $match;
                    $suffix = '';
                }
                $newPath = basename($path);
                $extension = pathinfo($newPath, PATHINFO_EXTENSION);
                $temporaryFileName = 'vhs-assets-css-' . $checksum . '.' . $extension;
                $temporaryFile = CoreUtility::getSitePath() . $this->getTempPath() . $temporaryFileName;
                $rawPath = GeneralUtility::getFileAbsFileName(
                    $originalDirectory . (empty($originalDirectory) ? '' : '/')
                ) . $path;
                $realPath = realpath($rawPath);
                if (false === $realPath) {
                    $message = 'Asset at path "' . $rawPath . '" not found. Processing skipped.';
                    if ($logger instanceof LoggerInterface) {
                        $logger->warning($message, ['rawPath' => $rawPath]);
                    } else {
                        GeneralUtility::sysLog($message, 'vhs', GeneralUtility::SYSLOG_SEVERITY_WARNING);
                    }
                } else {
                    if (false === file_exists($temporaryFile)) {
                        copy($realPath, $temporaryFile);
                        GeneralUtility::fixPermissions($temporaryFile);
                    }
                    $replacements[$matches[1][$matchCount]] = $wrap[0] . $temporaryFileName . $suffix . $wrap[1];
                }
            }
        }
        if (false === empty($replacements)) {
            $contents = str_replace(array_keys($replacements), array_values($replacements), $contents);
        }
        return $contents;
    }

    /**
     * @param AssetInterface|array $asset An Asset ViewHelper instance or an array containing an Asset definition
     * @return boolean
     */
    protected function assertAssetAllowedInFooter($asset)
    {
        if ($asset instanceof AssetInterface) {
            return $asset->assertAllowedInFooter();
        }
        return (boolean) ($asset['movable'] ?? true);
    }

    /**
     * @param AssetInterface|array $asset An Asset ViewHelper instance or an array containing an Asset definition
     * @return array
     */
    protected function extractAssetSettings($asset)
    {
        if ($asset instanceof AssetInterface) {
            return $asset->getAssetSettings();
        }
        return $asset;
    }

    /**
     * @param AssetInterface|array $asset An Asset ViewHelper instance or an array containing an Asset definition
     * @return string|null
     */
    protected function buildAsset($asset)
    {
        if ($asset instanceof AssetInterface) {
            return $asset->build();
        }
        if (false === isset($asset['path']) || true === empty($asset['path'])) {
            return (true === isset($asset['content']) ? $asset['content'] : null);
        }
        if (true === isset($asset['external']) && true === (boolean) $asset['external']) {
            $path = $asset['path'];
        } else {
            $path = GeneralUtility::getFileAbsFileName($asset['path']);
        }
        $content = file_get_contents($path);
        return $content ?: null;
    }

    /**
     * @param AssetInterface|array $asset
     * @throws \RuntimeException
     * @return string|null
     */
    protected function extractAssetContent($asset)
    {
        $assetSettings = $this->extractAssetSettings($asset);
        $fileRelativePathAndFilename = $assetSettings['path'];
        $fileRelativePath = dirname($assetSettings['path']);
        $absolutePathAndFilename = GeneralUtility::getFileAbsFileName($fileRelativePathAndFilename);
        $isExternal = true === isset($assetSettings['external']) && true === (boolean) $assetSettings['external'];
        $isFluidTemplate = true === isset($assetSettings['fluid']) && true === (boolean) $assetSettings['fluid'];
        if (!empty($fileRelativePathAndFilename)) {
            if (!$isExternal && !file_exists($absolutePathAndFilename)) {
                throw new \RuntimeException('Asset "' . $absolutePathAndFilename . '" does not exist.');
            }
            if (true === $isFluidTemplate) {
                $content = $this->renderAssetAsFluidTemplate($asset);
            } else {
                $content = $this->buildAsset($asset);
            }
        } else {
            $content = $this->buildAsset($asset);
        }
        if ($content !== null && 'css' === $assetSettings['type'] && ($assetSettings['rewrite'] ?? false)) {
            $content = $this->detectAndCopyFileReferences($content, $fileRelativePath);
        }
        return $content;
    }

    /**
     * @param array $parameters
     * @return void
     */
    public function clearCacheCommand($parameters)
    {
        if (true === static::$cacheCleared) {
            return;
        }
        if ('all' !== ($parameters['cacheCmd'] ?? '')) {
            return;
        }
        $assetCacheFiles = glob(GeneralUtility::getFileAbsFileName($this->getTempPath() . 'vhs-assets-*'));
        if (false === $assetCacheFiles) {
            return;
        }
        foreach ($assetCacheFiles as $assetCacheFile) {
            if (!@touch($assetCacheFile, 0)) {
                $content = (string) file_get_contents($assetCacheFile);
                $temporaryAssetCacheFile = (string) tempnam(dirname($assetCacheFile), basename($assetCacheFile) . '.');
                $this->writeFile($temporaryAssetCacheFile, $content);
                rename($temporaryAssetCacheFile, $assetCacheFile);
                touch($assetCacheFile, 0);
            }
        }
        static::$cacheCleared = true;
    }

    /**
     * @param string $file
     * @param string $contents
     * @return void
     */
    protected function writeFile($file, $contents)
    {
        ///** @var Dispatcher $signalSlotDispatcher */
        /*
        $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $signalSlotDispatcher->dispatch(__CLASS__, static::ASSET_SIGNAL, [&$file, &$contents]);
        */

        GeneralUtility::writeFile($file, $contents, true);
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function mergeArrays($array1, $array2)
    {
        ArrayUtility::mergeRecursiveWithOverrule($array1, $array2);
        return $array1;
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getFileIntegrity($file)
    {
        $typoScript = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.'] ?? null;
        if (isset($typoScript['assets.']['tagsAddSubresourceIntegrity'])) {
            // Note: 3 predefined hashing strategies (the ones suggestes in the rfc sheet)
            if (0 < $typoScript['assets.']['tagsAddSubresourceIntegrity']
                && $typoScript['assets.']['tagsAddSubresourceIntegrity'] < 4
            ) {
                if (false === file_exists($file)) {
                    return '';
                }

                /** @var TypoScriptFrontendController $typoScriptFrontendController */
                $typoScriptFrontendController = $GLOBALS['TSFE'];

                $integrity = null;
                $integrityMethod = ['sha256','sha384','sha512'][
                    $typoScript['assets.']['tagsAddSubresourceIntegrity'] - 1
                ];
                $integrityFile = sprintf(
                    $this->getTempPath() . 'vhs-assets-%s.%s',
                    str_replace('vhs-assets-', '', pathinfo($file, PATHINFO_BASENAME)),
                    $integrityMethod
                );

                if (false === file_exists($integrityFile)
                    || 0 === filemtime($integrityFile)
                    || true === isset($GLOBALS['BE_USER'])
                    || true === (boolean) $typoScriptFrontendController->no_cache
                    || true === (boolean) $typoScriptFrontendController->page['no_cache']
                ) {
                    if (extension_loaded('hash') && function_exists('hash_file')) {
                        $integrity = base64_encode((string) hash_file($integrityMethod, $file, true));
                    } elseif (extension_loaded('openssl') && function_exists('openssl_digest')) {
                        $integrity = base64_encode(
                            (string) openssl_digest((string) file_get_contents($file), $integrityMethod, true)
                        );
                    } else {
                        return ''; // Sadly, no integrity generation possible
                    }
                    $this->writeFile($integrityFile, $integrity);
                }
                return sprintf('%s-%s', $integrityMethod, $integrity ?: (string) file_get_contents($integrityFile));
            }
        }
        return '';
    }

    /**
     * Returns the typo3temp path name.
     *
     * Since TYPO3 8.0 publicly accessible files should be written to typo3temp/assets/.
     *
     * @return string
     */
    private function getTempPath()
    {
        return 'typo3temp/assets/';
    }

    protected function resolveAbsolutePathForFile(string $filename): string
    {
        return GeneralUtility::getFileAbsFileName($filename);
    }
}
