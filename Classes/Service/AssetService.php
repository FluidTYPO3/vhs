<?php
namespace FluidTYPO3\Vhs\Service;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Asset;
use FluidTYPO3\Vhs\ViewHelpers\Asset\AssetInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Asset Handling Service
 *
 * Inject this Service in your class to access VHS Asset
 * features - include assets etc.
 */
class AssetService implements SingletonInterface
{

    /**
     * @var boolean
     */
    protected static $typoScriptAssetsBuilt = false;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    private static $settingsCache = null;

    /**
     * @var array
     */
    private static $cachedDependencies = [];

    /**
     * @var boolean
     */
    private static $cacheCleared = false;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param object $caller
     * @param boolean $shouldUsePageCache
     * @return boolean
     */
    public function usePageCache($caller, $shouldUsePageCache)
    {
        $this->buildAll(array(), $caller);
        return $shouldUsePageCache;
    }

    /**
     * @param array $parameters
     * @param object $caller
     * @param boolean $cached If TRUE, treats this inclusion as happening in a cached context
     * @return void
     */
    public function buildAll(array $parameters, $caller, $cached = true)
    {
        if (false === $this->objectManager instanceof ObjectManager) {
            $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $this->configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        }
        $settings = $this->getSettings();
        $cached = (boolean) $cached;
        $buildTypoScriptAssets = (!self::$typoScriptAssetsBuilt && ($cached || $GLOBALS['TSFE']->no_cache));
        if ($buildTypoScriptAssets && isset($settings['asset']) && is_array($settings['asset'])) {
            foreach ($settings['asset'] as $name => $typoScriptAsset) {
                if (!isset($GLOBALS['VhsAssets'][$name]) && is_array($typoScriptAsset)) {
                    if (!isset($typoScriptAsset['name'])) {
                        $typoScriptAsset['name'] = $name;
                    }
                    Asset::createFromSettings($typoScriptAsset);
                }
            }
            self::$typoScriptAssetsBuilt = true;
        }
        if (!isset($GLOBALS['VhsAssets']) || !is_array($GLOBALS['VhsAssets'])) {
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
        $this->placeAssetsInHeaderAndFooter($assets, $cached);
    }

    /**
     * @param array $parameters
     * @param object $caller
     * @return void
     */
    public function buildAllUncached(array $parameters, $caller)
    {
        $content = $caller->content;
        $matches = [];
        preg_match_all('/\<\![\-]+\ VhsAssetsDependenciesLoaded ([^ ]+) [\-]+\>/i', $content, $matches);
        foreach ($matches[1] as $key => $match) {
            $extractedDependencies = explode(',', $matches[1][$key]);
            self::$cachedDependencies = array_merge(self::$cachedDependencies, $extractedDependencies);
            $content = str_replace($matches[0][$key], '', $content);
        }
        $caller->content = $content;
        $this->buildAll($parameters, $caller, false);
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
        if (null === self::$settingsCache) {
            $allTypoScript = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            $settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
            if (false === $settingsExist) {
                // no settings exist, but don't allow a NULL value. This prevents cache clobbering.
                self::$settingsCache = [];
            } else {
                self::$settingsCache = GeneralUtility::removeDotsFromTS(
                    $allTypoScript['plugin.']['tx_vhs.']['settings.']
                );
            }
        }
        $settings = (array) self::$settingsCache;
        return $settings;
    }

    /**
     * @param AssetInterface[] $assets
     * @param boolean $cached
     * @return void
     */
    protected function placeAssetsInHeaderAndFooter($assets, $cached)
    {
        $settings = $this->getSettings();
        $header = [];
        $footer = [];
        $footerRelocationEnabled = (isset($settings['enableFooterRelocation']) && $settings['relocateToFooter'] > 0)
            || !isset($settings['enableFooterRelocation']);
        foreach ($assets as $name => $asset) {
            $variables = $asset->getVariables();
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
            $this->insertAssetsAtMarker('DependenciesLoaded', $dependenciesString);
        }
        $this->insertAssetsAtMarker('Header' . $uncachedSuffix, $header);
        $this->insertAssetsAtMarker('Footer' . $uncachedSuffix, $footer);
        $GLOBALS['VhsAssets'] = [];
    }

    /**
     * @param string $markerName
     * @param mixed $assets
     * @return void
     */
    protected function insertAssetsAtMarker($markerName, $assets)
    {
        $assetMarker = '<!-- VhsAssets' . $markerName . ' -->';
        if (false === strpos($GLOBALS['TSFE']->content, $assetMarker)) {
            $inFooter = (boolean) (false !== strpos($markerName, 'Footer'));
            $tag = true === $inFooter ? '</body>' : '</head>';
            $GLOBALS['TSFE']->content = str_replace($tag, $assetMarker . LF . $tag, $GLOBALS['TSFE']->content);
        }
        if (true === is_array($assets)) {
            $chunk = $this->buildAssetsChunk($assets);
        } else {
            $chunk = $assets;
        }
        $GLOBALS['TSFE']->content = str_replace($assetMarker, $chunk, $GLOBALS['TSFE']->content);
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
        foreach ($spool as $type => $spooledAssets) {
            $chunk = [];
            /** @var AssetInterface[] $spooledAssets */
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
                        array_push($chunks, $mergedFileTag);
                        $chunk = [];
                    }
                    if (true === empty($path)) {
                        $assetContent = $this->extractAssetContent($asset);
                        array_push($chunks, $this->generateTagForAssetType($type, $assetContent));
                    } else {
                        if (true === $external) {
                            array_push($chunks, $this->generateTagForAssetType($type, null, $path));
                        } else {
                            if (true === $rewrite) {
                                array_push(
                                    $chunks,
                                    $this->writeCachedMergedFileAndReturnTag(array($name => $asset), $type)
                                );
                            } else {
                                $path = substr($path, strlen(PATH_site));
                                $path = $this->prefixPath($path);
                                array_push($chunks, $this->generateTagForAssetType($type, null, $path));
                            }
                        }
                    }
                }
            }
            if (0 < count($chunk)) {
                $mergedFileTag = $this->writeCachedMergedFileAndReturnTag($chunk, $type);
                array_push($chunks, $mergedFileTag);
            }
        }
        return implode(LF, $chunks);
    }

    /**
     * @param AssetInterface[] $assets
     * @param string $type
     * @return string
     */
    protected function writeCachedMergedFileAndReturnTag($assets, $type)
    {
        $source = '';
        $assetName = implode('-', array_keys($assets));
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename'])) {
            if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename']) {
                $assetName = md5($assetName);
            }
        }
        $fileRelativePathAndFilename = 'typo3temp/vhs-assets-' . $assetName . '.' . $type;
        $fileAbsolutePathAndFilename = GeneralUtility::getFileAbsFileName($fileRelativePathAndFilename);
        if (false === file_exists($fileAbsolutePathAndFilename)
            || 0 === filemtime($fileAbsolutePathAndFilename)
            || true === isset($GLOBALS['BE_USER'])
            || true === (boolean) $GLOBALS['TSFE']->no_cache
            || true === (boolean) $GLOBALS['TSFE']->page['no_cache']
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
        if (false === empty($GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'])) {
            $timestampMode = $GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'];
            if (true === file_exists($fileRelativePathAndFilename)) {
                $lastModificationTime = filemtime($fileRelativePathAndFilename);
                if ('querystring' === $timestampMode) {
                    $fileRelativePathAndFilename .= '?' . $lastModificationTime;
                } elseif ('embed' === $timestampMode) {
                    $fileRelativePathAndFilename = substr_replace(
                        $fileRelativePathAndFilename,
                        '.' . $lastModificationTime,
                        strrpos($fileRelativePathAndFilename, '.'),
                        0
                    );
                }
            }
        }
        $fileRelativePathAndFilename = $this->prefixPath($fileRelativePathAndFilename);
        return $this->generateTagForAssetType($type, null, $fileRelativePathAndFilename);
    }

    /**
     * @param string $type
     * @param string $content
     * @param string $file
     * @throws \RuntimeException
     * @return string
     */
    protected function generateTagForAssetType($type, $content, $file = null)
    {
        /** @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder $tagBuilder */
        $tagBuilder = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TagBuilder');
        if (null === $file && true === empty($content)) {
            $content = '<!-- Empty tag content -->';
        }
        switch ($type) {
            case 'js':
                $tagBuilder->setTagName('script');
                $tagBuilder->forceClosingTag(true);
                $tagBuilder->addAttribute('type', 'text/javascript');
                if (null === $file) {
                    $tagBuilder->setContent($content);
                } else {
                    $tagBuilder->addAttribute('src', $file);
                }
                break;
            case 'css':
                if (null === $file) {
                    $tagBuilder->setTagName('style');
                    $tagBuilder->forceClosingTag(true);
                    $tagBuilder->addAttribute('type', 'text/css');
                    $tagBuilder->setContent($content);
                } else {
                    $tagBuilder->forceClosingTag(false);
                    $tagBuilder->setTagName('link');
                    $tagBuilder->addAttribute('rel', 'stylesheet');
                    $tagBuilder->addAttribute('href', $file);
                }
                break;
            case 'meta':
                $tagBuilder->forceClosingTag(false);
                $tagBuilder->setTagName('meta');
                break;
            default:
                if (null === $file) {
                    return $content;
                } else {
                    throw new \RuntimeException(
                        'Attempt to include file based asset with unknown type ("' . $type . '")',
                        1358645219
                    );
                }
                break;
        }
        return $tagBuilder->render();
    }

    /**
     * @param array $assets
     * @return array
     * @throws \RuntimeException
     */
    protected function manipulateAssetsByTypoScriptSettings($assets)
    {
        $settings = $this->getSettings();
        if (false === (isset($settings['asset']) || isset($settings['assetGroup']))) {
            return $assets;
        }
        $filtered = [];
        /** @var \FluidTYPO3\Vhs\Asset $asset */
        foreach ($assets as $name => $asset) {
            $assetSettings = $this->extractAssetSettings($asset);
            $groupName = $assetSettings['group'];
            $removed = (boolean) (true === isset($assetSettings['removed']) ? $assetSettings['removed'] : false);
            if (true === $removed) {
                continue;
            }
            $localSettings = (array) $assetSettings;
            if (true === isset($settings['asset'])) {
                $localSettings = $this->mergeArrays($localSettings, (array) $settings['asset']);
            }
            if (true === isset($settings['asset'][$name])) {
                $localSettings = $this->mergeArrays($localSettings, (array) $settings['asset'][$name]);
            }
            if (true === isset($settings['assetGroup'][$groupName])) {
                $localSettings = $this->mergeArrays($localSettings, (array) $settings['assetGroup'][$groupName]);
            }
            if (true === $asset instanceof AssetInterface) {
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
                    && false === in_array($dependency, self::$cachedDependencies)
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
     * @param mixed $asset
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
        $variables = GeneralUtility::removeDotsFromTS($variables);
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
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
        $prefixPath = $settings['prependPath'];
        if (false === empty($GLOBALS['TSFE']->absRefPrefix) && true === empty($prefixPath)) {
            $fileRelativePathAndFilename = $GLOBALS['TSFE']->absRefPrefix . $fileRelativePathAndFilename;
        } elseif (false === empty($prefixPath)) {
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
                $temporaryFile = constant('PATH_site') . 'typo3temp/' . $temporaryFileName;
                $rawPath = GeneralUtility::getFileAbsFileName(
                    $originalDirectory . (empty($originalDirectory) ? '' : '/')
                ) . $path;
                $realPath = realpath($rawPath);
                if (false === $realPath) {
                    GeneralUtility::sysLog(
                        'Asset at path "' . $rawPath . '" not found. Processing skipped.',
                        'vhs',
                        GeneralUtility::SYSLOG_SEVERITY_WARNING
                    );
                } else {
                    if (false === file_exists($temporaryFile)) {
                        copy($realPath, $temporaryFile);
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
     * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
     * @return boolean
     */
    protected function assertAssetAllowedInFooter($asset)
    {
        if (true === $asset instanceof AssetInterface) {
            return $asset->assertAllowedInFooter();
        }
        return (boolean) (true === isset($asset['movable']) ? $asset['movable'] : true);
    }

    /**
     * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
     * @return array
     */
    protected function extractAssetSettings($asset)
    {
        if (true === $asset instanceof AssetInterface) {
            return $asset->getAssetSettings();
        }
        return $asset;
    }

    /**
     * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
     * @return string
     */
    protected function buildAsset($asset)
    {
        if (true === $asset instanceof AssetInterface) {
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
        return $content;
    }

    /**
     * @param mixed $asset
     * @throws \RuntimeException
     * @return string
     */
    protected function extractAssetContent($asset)
    {
        $assetSettings = $this->extractAssetSettings($asset);
        $fileRelativePathAndFilename = $assetSettings['path'];
        $fileRelativePath = dirname($assetSettings['path']);
        $absolutePathAndFilename = GeneralUtility::getFileAbsFileName($fileRelativePathAndFilename);
        $isExternal = true === isset($assetSettings['external']) && true === (boolean) $assetSettings['external'];
        $isFluidTemplate = true === isset($assetSettings['fluid']) && true === (boolean) $assetSettings['fluid'];
        if (false === empty($fileRelativePathAndFilename)) {
            if (false === $isExternal && false === file_exists($absolutePathAndFilename)) {
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
        if (('css' === $assetSettings['type']) && (true === (boolean) $assetSettings['rewrite'])) {
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
        if (true === self::$cacheCleared) {
            return;
        }
        if ('all' !== $parameters['cacheCmd']) {
            return;
        }
        $assetCacheFiles = glob(GeneralUtility::getFileAbsFileName('typo3temp/vhs-assets-*'));
        if (false === $assetCacheFiles) {
            return;
        }
        foreach ($assetCacheFiles as $assetCacheFile) {
            touch($assetCacheFile, 0);
        }
        self::$cacheCleared = true;
    }

    /**
     * @param string $file
     * @param string $contents
     */
    protected function writeFile($file, $contents)
    {
        file_put_contents($file, $contents);
    }

    /**
     * @param $array1
     * @param $array2
     * @return array
     */
    protected function mergeArrays($array1, $array2)
    {
        if (6.2 <= (float) substr(TYPO3_version, 0, 3)) {
            ArrayUtility::mergeRecursiveWithOverrule($array1, $array2);
            return $array1;
        } else {
            return GeneralUtility::array_merge_recursive_overrule($array1, $array2);
        }
    }
}
