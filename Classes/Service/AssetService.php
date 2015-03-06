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
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage Service
 */
class AssetService implements SingletonInterface {

	/**
	 * @var boolean
	 */
	protected static $typoScriptAssetsBuilt = FALSE;

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
	private static $settingsCache = NULL;

	/**
	 * @var array
	 */
	private static $cachedDependencies = array();

	/**
	 * @var boolean
	 */
	private static $cacheCleared = FALSE;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param object $caller
	 * @param boolean $shouldUsePageCache
	 * @return boolean
	 */
	public function usePageCache($caller, $shouldUsePageCache) {
		$this->buildAll(array(), $caller);
		return $shouldUsePageCache;
	}

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @param boolean $cached If TRUE, treats this inclusion as happening in a cached context
	 * @return void
	 */
	public function buildAll(array $parameters, $caller, $cached = TRUE) {
		if (FALSE === $this->objectManager instanceof ObjectManager) {
			$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
			$this->configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
		}
		$settings = $this->getSettings();
		$cached = (boolean) $cached;
		$buildTypoScriptAssets = (FALSE === self::$typoScriptAssetsBuilt && (TRUE === $cached || TRUE === (boolean) $GLOBALS['TSFE']->no_cache));
		if (TRUE === $buildTypoScriptAssets && TRUE === isset($settings['asset']) && TRUE === is_array($settings['asset'])) {
			foreach ($settings['asset'] as $name => $typoScriptAsset) {
				if (FALSE === isset($GLOBALS['VhsAssets'][$name]) && TRUE === is_array($typoScriptAsset)) {
					if (FALSE === isset($typoScriptAsset['name'])) {
						$typoScriptAsset['name'] = $name;
					}
					Asset::createFromSettings($typoScriptAsset);
				}
			}
			self::$typoScriptAssetsBuilt = TRUE;
		}
		if (FALSE === isset($GLOBALS['VhsAssets']) || FALSE === is_array($GLOBALS['VhsAssets'])) {
			return;
		}
		$assets = $GLOBALS['VhsAssets'];
		$assets = $this->sortAssetsByDependency($assets);
		$assets = $this->manipulateAssetsByTypoScriptSettings($assets);
		$buildDebugRequested = (isset($settings['asset']['debugBuild']) && $settings['asset']['debugBuild'] > 0);
		$assetDebugRequested = (isset($settings['asset']['debug']) && $settings['asset']['debug'] > 0);
		$useDebugUtility = (isset($settings['asset']['useDebugUtility']) && $settings['asset']['useDebugUtility'] > 0) || FALSE === isset($settings['asset']['useDebugUtility']);
		if (TRUE === ($buildDebugRequested || $assetDebugRequested)) {
			if (TRUE === $useDebugUtility) {
				DebuggerUtility::var_dump($assets);
			} else {
				echo var_export($assets, TRUE);
			}
		}
		$this->placeAssetsInHeaderAndFooter($assets, $cached);
	}

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @return void
	 */
	public function buildAllUncached(array $parameters, $caller) {
		$content = $caller->content;
		$matches = array();
		preg_match_all('/\<\![\-]+\ VhsAssetsDependenciesLoaded ([^ ]+) [\-]+\>/i', $content, $matches);
		foreach ($matches[1] as $key => $match) {
			$extractedDependencies = explode(',', $matches[1][$key]);
			self::$cachedDependencies = array_merge(self::$cachedDependencies, $extractedDependencies);
			$content = str_replace($matches[0][$key], '', $content);
		}
		$caller->content = $content;
		$this->buildAll($parameters, $caller, FALSE);
	}

	/**
	 * Returns the settings used by this particular Asset
	 * during inclusion. Public access allows later inspection
	 * of the TypoScript values which were applied to the Asset.
	 *
	 * @return array
	 */
	public function getSettings() {
		if (NULL === self::$settingsCache) {
			$allTypoScript = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
			$settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			if (FALSE === $settingsExist) {
				// no settings exist, but don't allow a NULL value. This prevents cache clobbering.
				self::$settingsCache = array();
			} else {
				self::$settingsCache = GeneralUtility::removeDotsFromTS($allTypoScript['plugin.']['tx_vhs.']['settings.']);
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
	private function placeAssetsInHeaderAndFooter($assets, $cached) {
		$settings = $this->getSettings();
		$header = array();
		$footer = array();
		$footerRelocationEnabled = (TRUE === isset($settings['enableFooterRelocation']) && $settings['relocateToFooter'] > 0) || FALSE === isset($settings['enableFooterRelocation']);
		foreach ($assets as $name => $asset) {
			$variables = $asset->getVariables();
			if (0 < count($variables)) {
				$name .= '-' . md5(serialize($variables));
			}
			if (TRUE === ($this->assertAssetAllowedInFooter($asset) && $footerRelocationEnabled)) {
				$footer[$name] = $asset;
			} else {
				$header[$name] = $asset;
			}
		}
		if (FALSE === $cached) {
			$uncachedSuffix = 'Uncached';
		} else {
			$uncachedSuffix = '';
			$dependenciesString = '<!-- VhsAssetsDependenciesLoaded ' . implode(',', array_keys($assets)) . ' -->';
			$this->insertAssetsAtMarker('DependenciesLoaded', $dependenciesString);
		}
		$this->insertAssetsAtMarker('Header' . $uncachedSuffix, $header);
		$this->insertAssetsAtMarker('Footer' . $uncachedSuffix, $footer);
		$GLOBALS['VhsAssets'] = array();
	}

	/**
	 * @param string $markerName
	 * @param mixed $assets
	 * @return void
	 */
	private function insertAssetsAtMarker($markerName, $assets) {
		$assetMarker = '<!-- VhsAssets' . $markerName . ' -->';
		if (FALSE === strpos($GLOBALS['TSFE']->content, $assetMarker)) {
			$inFooter = (boolean) (FALSE !== strpos($markerName, 'Footer'));
			$tag = TRUE === $inFooter ? '</body>' : '</head>';
			$GLOBALS['TSFE']->content = str_replace($tag, $assetMarker . LF . $tag, $GLOBALS['TSFE']->content);
		}
		if (TRUE === is_array($assets)) {
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
	private function buildAssetsChunk($assets) {
		$spool = array();
		foreach ($assets as $name => $asset) {
			$assetSettings = $this->extractAssetSettings($asset);
			$type = $assetSettings['type'];
			if (FALSE === isset($spool[$type])) {
				$spool[$type] = array();
			}
			$spool[$type][$name] = $asset;
		}
		$chunks = array();
		foreach ($spool as $type => $spooledAssets) {
			$chunk = array();
			/** @var AssetInterface[] $spooledAssets */
			foreach ($spooledAssets as $name => $asset) {
				$assetSettings = $this->extractAssetSettings($asset);
				$standalone = (boolean) $assetSettings['standalone'];
				$external = (boolean) $assetSettings['external'];
				$rewrite = (boolean) $assetSettings['rewrite'];
				$path = $assetSettings['path'];
				if (FALSE === $standalone) {
					$chunk[$name] = $asset;
				} else {
					if (0 < count($chunk)) {
						$mergedFileTag = $this->writeCachedMergedFileAndReturnTag($chunk, $type);
						array_push($chunks, $mergedFileTag);
						$chunk = array();
					}
					if (TRUE === empty($path)) {
						$assetContent = $this->extractAssetContent($asset);
						array_push($chunks, $this->generateTagForAssetType($type, $assetContent));
					} else {
						if (TRUE === $external) {
							array_push($chunks, $this->generateTagForAssetType($type, NULL, $path));
						} else {
							if (TRUE === $rewrite) {
								array_push($chunks, $this->writeCachedMergedFileAndReturnTag(array($name => $asset), $type));
							} else {
								$path = substr($path, strlen(PATH_site));
								$path = $this->prefixPath($path);
								array_push($chunks, $this->generateTagForAssetType($type, NULL, $path));
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
	private function writeCachedMergedFileAndReturnTag($assets, $type) {
		$source = '';
		$assetName = implode('-', array_keys($assets));
		if (TRUE === isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename'])) {
			if (TRUE === (boolean) $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename']) {
				$assetName = md5($assetName);
			}
		}
		$fileRelativePathAndFilename = 'typo3temp/vhs-assets-' . $assetName . '.' . $type;
		$fileAbsolutePathAndFilename = GeneralUtility::getFileAbsFileName($fileRelativePathAndFilename);
		if (
			FALSE === file_exists($fileAbsolutePathAndFilename)
			|| 0 === filemtime($fileAbsolutePathAndFilename)
			|| TRUE === isset($GLOBALS['BE_USER'])
			|| TRUE === (boolean) $GLOBALS['TSFE']->no_cache
			|| TRUE === (boolean) $GLOBALS['TSFE']->page['no_cache']
		) {
			foreach ($assets as $name => $asset) {
				$assetSettings = $this->extractAssetSettings($asset);
				if (TRUE === (isset($assetSettings['namedChunks']) && 0 < $assetSettings['namedChunks']) || FALSE === isset($assetSettings['namedChunks'])) {
					$source .= '/* ' . $name . ' */' . LF;
				}
				$source .= $this->extractAssetContent($asset) . LF;
				// Put a return carriage between assets preventing broken content.
				$source .= "\n";
			}
			$this->writeFile($fileAbsolutePathAndFilename, $source);
		}
		if (FALSE === empty($GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'])) {
			$timestampMode = $GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'];
			if (TRUE === file_exists($fileRelativePathAndFilename)) {
				$lastModificationTime = filemtime($fileRelativePathAndFilename);
				if ('querystring' === $timestampMode) {
					$fileRelativePathAndFilename .= '?' . $lastModificationTime;
				} elseif ('embed' === $timestampMode) {
					$fileRelativePathAndFilename = substr_replace($fileRelativePathAndFilename, '.' . $lastModificationTime, strrpos($fileRelativePathAndFilename, '.'), 0);
				}
			}
		}
		$fileRelativePathAndFilename = $this->prefixPath($fileRelativePathAndFilename);
		return $this->generateTagForAssetType($type, NULL, $fileRelativePathAndFilename);
	}

	/**
	 * @param string $type
	 * @param string $content
	 * @param string $file
	 * @throws \RuntimeException
	 * @return string
	 */
	private function generateTagForAssetType($type, $content, $file = NULL) {
		/** @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder $tagBuilder */
		$tagBuilder = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TagBuilder');
		switch ($type) {
			case 'js':
				$tagBuilder->setTagName('script');
				$tagBuilder->addAttribute('type', 'text/javascript');
				if (NULL === $file) {
					$tagBuilder->setContent($content);
				} else {
					$tagBuilder->addAttribute('src', $file);
					$tagBuilder->forceClosingTag(TRUE);
				}
				break;
			case 'css':
				if (NULL === $file) {
					$tagBuilder->setTagName('style');
					$tagBuilder->addAttribute('type', 'text/css');
					$tagBuilder->setContent($content);
				} else {
					$tagBuilder->setTagName('link');
					$tagBuilder->addAttribute('rel', 'stylesheet');
					$tagBuilder->addAttribute('href', $file);
				}
				break;
			case 'meta':
				$tagBuilder->setTagName('meta');
				break;
			default:
				if (NULL === $file) {
					return $content;
				} else {
					throw new \RuntimeException('Attempt to include file based asset with unknown type ("' . $type . '")', 1358645219);
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
	private function manipulateAssetsByTypoScriptSettings($assets) {
		$settings = $this->getSettings();
		if (FALSE === (isset($settings['asset']) || isset($settings['assetGroup']))) {
			return $assets;
		}
		$filtered = array();
		/** @var \FluidTYPO3\Vhs\Asset $asset */
		foreach ($assets as $name => $asset) {
			$assetSettings = $this->extractAssetSettings($asset);
			$groupName = $assetSettings['group'];
			$removed = (boolean) (TRUE === isset($assetSettings['removed']) ? $assetSettings['removed'] : FALSE);
			if (TRUE === $removed) {
				continue;
			}
			$localSettings = (array) $assetSettings;
			if (TRUE === isset($settings['asset'])) {
				$localSettings = $this->mergeArrays($localSettings, (array) $settings['asset']);
			}
			if (TRUE === isset($settings['asset'][$name])) {
				$localSettings = $this->mergeArrays($localSettings, (array) $settings['asset'][$name]);
			}
			if (TRUE === isset($settings['assetGroup'][$groupName])) {
				$localSettings = $this->mergeArrays($localSettings, (array) $settings['assetGroup'][$groupName]);
			}
			if (TRUE === $asset instanceof AssetInterface) {
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
	private function sortAssetsByDependency($assets) {
		$placed = array();
		$assetNames = (0 < count($assets)) ? array_combine(array_keys($assets), array_keys($assets)) : array();
		while ($asset = array_shift($assets)) {
			$postpone = FALSE;
			/** @var AssetInterface $asset */
			$assetSettings = $this->extractAssetSettings($asset);
			$name = array_shift($assetNames);
			$dependencies = $assetSettings['dependencies'];
			if (FALSE === is_array($dependencies)) {
				$dependencies = GeneralUtility::trimExplode(',', $assetSettings['dependencies'], TRUE);
			}
			foreach ($dependencies as $dependency) {
				if (
					TRUE === array_key_exists($dependency, $assets)
					&& FALSE === isset($placed[$dependency])
					&& FALSE === in_array($dependency, self::$cachedDependencies)
				) {
					// shove the Asset back to the end of the queue, the dependency has
					// not yet been encountered and moving this item to the back of the
					// queue ensures it will be encountered before re-encountering this
					// specific Asset
					if (0 === count($assets)) {
						throw new \RuntimeException('Asset "' . $name . '" depends on "' . $dependency . '" but "' . $dependency . '" was not found', 1358603979);
					}
					$assets[$name] = $asset;
					$assetNames[$name] = $name;
					$postpone = TRUE;
				}
			}
			if (FALSE === $postpone) {
				$placed[$name] = $asset;
			}
		}
		return $placed;
	}

	/**
	 * @param mixed $asset
	 * @return string
	 */
	private function renderAssetAsFluidTemplate($asset) {
		$settings = $this->extractAssetSettings($asset);
		$variables = (TRUE === (isset($settings['variables']) && is_array($settings['variables'])) ? $settings['variables'] : array());
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
	protected function prefixPath($fileRelativePathAndFilename) {
		$settings = $this->getSettings();
		$prefixPath = $settings['prependPath'];
		if (FALSE === empty($GLOBALS['TSFE']->absRefPrefix) && TRUE === empty($prefixPath)) {
			$fileRelativePathAndFilename = $GLOBALS['TSFE']->absRefPrefix . $fileRelativePathAndFilename;
		} elseif (FALSE === empty($prefixPath)) {
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
	protected function detectAndCopyFileReferences($contents, $originalDirectory) {
		if (FALSE !== stripos($contents, 'url')) {
			$regex = '/url(\\(\\s*["\']?(?!\\/)([^"\']+)["\']?\\s*\\))/iU';
			$contents = $this->copyReferencedFilesAndReplacePaths($contents, $regex, $originalDirectory, '(\'|\')');
		}
		if (FALSE !== stripos($contents, '@import')) {
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
	protected function copyReferencedFilesAndReplacePaths($contents, $regex, $originalDirectory, $wrap = '|') {
		$matches = array();
		$replacements = array();
		$wrap = explode('|', $wrap);
		preg_match_all($regex, $contents, $matches);
		foreach ($matches[2] as $matchCount => $match) {
			$match = trim($match, '\'" ');
			if (FALSE === strpos($match, ':') && !preg_match('/url\\s*\\(/i', $match)) {
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
				$rawPath = GeneralUtility::getFileAbsFileName($originalDirectory . (TRUE === empty($originalDirectory) ? '' : '/')) . $path;
				$realPath = realpath($rawPath);
				if (FALSE === $realPath) {
					GeneralUtility::sysLog('Asset at path "' . $rawPath . '" not found. Processing skipped.', GeneralUtility::SYSLOG_SEVERITY_WARNING);
				} else {
					if (FALSE === file_exists($temporaryFile)) {
						copy($realPath, $temporaryFile);
					}
					$replacements[$matches[1][$matchCount]] = $wrap[0] . $temporaryFileName . $suffix . $wrap[1];
				}
			}
		}
		if (FALSE === empty($replacements)) {
			$contents = str_replace(array_keys($replacements), array_values($replacements), $contents);
		}
		return $contents;
	}

	/**
	 * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
	 * @return boolean
	 */
	protected function assertAssetAllowedInFooter($asset) {
		if (TRUE === $asset instanceof AssetInterface) {
			return $asset->assertAllowedInFooter();
		}
		return (boolean) (TRUE === isset($asset['allowMoveToFooter']) ? $asset['allowMoveToFooter'] : TRUE);
	}

	/**
	 * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
	 * @return array
	 */
	protected function extractAssetSettings($asset) {
		if (TRUE === $asset instanceof AssetInterface) {
			return $asset->getAssetSettings();
		}
		return $asset;
	}

	/**
	 * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
	 * @return string
	 */
	protected function buildAsset($asset) {
		if (TRUE === $asset instanceof AssetInterface) {
			return $asset->build();
		}
		if (FALSE === isset($asset['path']) || TRUE === empty($asset['path'])) {
			return (TRUE === isset($asset['content']) ? $asset['content'] : NULL);
		}
		if (TRUE === isset($asset['external']) && TRUE === (boolean) $asset['external']) {
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
	private function extractAssetContent($asset) {
		$assetSettings = $this->extractAssetSettings($asset);
		$fileRelativePathAndFilename = $assetSettings['path'];
		$fileRelativePath = dirname($assetSettings['path']);
		$absolutePathAndFilename = GeneralUtility::getFileAbsFileName($fileRelativePathAndFilename);
		$isExternal = TRUE === isset($assetSettings['external']) && TRUE === (boolean) $assetSettings['external'];
		$isFluidTemplate = TRUE === isset($assetSettings['fluid']) && TRUE === (boolean) $assetSettings['fluid'];
		if (FALSE === empty($fileRelativePathAndFilename)) {
			if (FALSE === $isExternal && FALSE === file_exists($absolutePathAndFilename)) {
				throw new \RuntimeException('Asset "' . $absolutePathAndFilename . '" does not exist.');
			}
			if (TRUE === $isFluidTemplate) {
				$content = $this->renderAssetAsFluidTemplate($asset);
			} else {
				$content = $this->buildAsset($asset);
			}
		} else {
			$content = $this->buildAsset($asset);
		}
		if ('css' === $assetSettings['type']) {
			$content = $this->detectAndCopyFileReferences($content, $fileRelativePath);
		}
		return $content;
	}

	/**
	 * @param array $parameters
	 * @return void
	 */
	public function clearCacheCommand($parameters) {
		if (TRUE === self::$cacheCleared) {
			return;
		}
		if ('all' !== $parameters['cacheCmd']) {
			return;
		}
		$assetCacheFiles = glob(GeneralUtility::getFileAbsFileName('typo3temp/vhs-assets-*'));
		if (FALSE === $assetCacheFiles) {
			return;
		}
		foreach ($assetCacheFiles as $assetCacheFile) {
			touch($assetCacheFile, 0);
		}
		self::$cacheCleared = TRUE;
	}

	/**
	 * @param string $file
	 * @param string $contents
	 */
	protected function writeFile($file, $contents) {
		file_put_contents($file, $contents);
	}

	/**
	 * @param $array1
	 * @param $array2
	 * @return array
	 */
	protected function mergeArrays($array1, $array2) {
		if (6.2 <= (float) substr(TYPO3_version, 0, 3)) {
			ArrayUtility::mergeRecursiveWithOverrule($array1, $array2);
			return $array1;
		} else {
			return GeneralUtility::array_merge_recursive_overrule($array1, $array2);
		}
	}

}
