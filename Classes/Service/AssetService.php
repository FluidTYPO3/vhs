<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Asset Handling Service
 *
 * Inject this Service in your class to access VHS Asset
 * features - include assets etc.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage Service
 */
class Tx_Vhs_Service_AssetService implements t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
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
	private static $buildComplete = FALSE;

	/**
	 * @var boolean
	 */
	private static $cacheCleared = FALSE;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @param boolean $cached If TRUE, treats this inclusion as happening in a cached context
	 * @return void
	 */
	public function buildAll(array $parameters, $caller, $cached = TRUE) {
		if (TRUE === self::$buildComplete) {
			return;
		}
		if (FALSE === $this->objectManager instanceof Tx_Extbase_Object_ObjectManager) {
			$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
			$this->configurationManager = $this->objectManager->get('Tx_Extbase_Configuration_ConfigurationManagerInterface');
		}
		$settings = $this->getSettings();
		$cached = (boolean) $cached;
		if (TRUE === $cached && TRUE === isset($settings['asset']) && TRUE === is_array($settings['asset'])) {
			foreach ($settings['asset'] as $name => $typoScriptAsset) {
				if (FALSE === isset($GLOBALS['VhsAssets'][$name]) && TRUE === is_array($typoScriptAsset)) {
					if (FALSE === isset($typoScriptAsset['name'])) {
						$typoScriptAsset['name'] = $name;
					}
					Tx_Vhs_Asset::createFromSettings($typoScriptAsset);
				}
			}
		}
		if (FALSE === isset($GLOBALS['VhsAssets']) || FALSE === is_array($GLOBALS['VhsAssets'])) {
			return;
		}
		$assets = $GLOBALS['VhsAssets'];
		$assets = $this->sortAssetsByDependency($assets);
		$assets = $this->manipulateAssetsByTypoScriptSetttings($assets);
		$buildDebugRequested = (isset($settings['asset']['debugBuild']) && $settings['asset']['debugBuild'] > 0);
		$assetDebugRequested = (isset($settings['asset']['debug']) && $settings['asset']['debug'] > 0);
		$useDebugUtility = (isset($settings['asset']['useDebugUtility']) && $settings['asset']['useDebugUtility'] > 0) || FALSE === isset($settings['asset']['useDebugUtility']);
		if (TRUE === ($buildDebugRequested || $assetDebugRequested)) {
			$this->debug();
			if (TRUE === $useDebugUtility) {
				Tx_Extbase_Utility_Debugger::var_dump($assets);
			} else {
				echo var_export($assets, TRUE);
			}
		}
		$this->placeAssetsInHeaderAndFooter($assets, $cached);
		self::$buildComplete = TRUE;
	}

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @return void
	 */
	public function buildAllUncached(array $parameters, $caller) {
		self::$buildComplete = FALSE;
		$content = $GLOBALS['TSFE']->content;
		$matches = array();
		preg_match_all('/\<\![\-]+\ VhsAssetsDependenciesLoaded ([^ ]+) [\-]+\!\>/i', $content, $matches);
		foreach ($matches[0] as $key => $match) {
			$extractedDependencies = explode(',', $matches[1][$key]);
			self::$cachedDependencies = array_merge(self::$cachedDependencies, $extractedDependencies);
			$content = str_replace($matches[0][$key], '', $content);
		}
		$GLOBALS['TSFE']->content = $content;
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
			$allTypoScript = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
			$settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			if (FALSE === $settingsExist) {
				// no settings exist, but don't allow a NULL value. This prevents cache clobbering.
				self::$settingsCache = array();
			} else {
				self::$settingsCache = t3lib_div::removeDotsFromTS($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			}
		}
		$settings = (array) self::$settingsCache;
		return $settings;
	}

	/**
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface[] $assets
	 * @param boolean $cached
	 * @return void
	 */
	private function placeAssetsInHeaderAndFooter($assets, $cached) {
		$settings = $this->getSettings();
		$header = array();
		$footer = array();
		$footerRelocationEnabled = (TRUE === isset($settings['enableFooterRelocation']) && $settings['relocateToFooter'] > 0) || FALSE === isset($settings['enableFooterRelocation']);
		foreach ($assets as $name => $asset) {
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
		if (FALSE === strpos($GLOBALS['TSFE']->content, '<!-- VhsAssets' . $markerName . ' -->')) {
			$assetMarker = '<!-- VhsAssets' . $markerName . ' -->';
			$inFooter = FALSE !== strpos($markerName, 'Footer');
			$tag = TRUE === $inFooter ? '</body>' : '</head>';
			$GLOBALS['TSFE']->content = str_replace($tag, $assetMarker . LF . $tag, $GLOBALS['TSFE']->content);
		}
		if (TRUE === is_array($assets)) {
			$chunk = $this->buildAssetsChunk($assets);
		} else {
			$chunk = $assets;
		}
		$GLOBALS['TSFE']->content = str_replace('<!-- VhsAssets' . $markerName . ' -->', $chunk, $GLOBALS['TSFE']->content);
	}

	/**
	 * @param array $assets
	 * @throws RuntimeException
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
			/** @var $spooledAssets Tx_Vhs_ViewHelpers_Asset_AssetInterface[] */
			foreach ($spooledAssets as $name => $asset) {
				$assetSettings = $this->extractAssetSettings($asset);
				$standalone = (TRUE === (boolean) $assetSettings['standalone']);
				if (TRUE === $standalone) {
					if (0 < count($chunk)) {
						$mergedFileTag = $this->writeCachedMergedFileAndReturnTag($chunk, $type);
						$chunk = array();
						array_push($chunks, $mergedFileTag);
					}
					if (TRUE === isset($assetSettings['path']) && FALSE === empty($assetSettings['path'])) {
						$fileRelativePathAndFilename = $assetSettings['path'];
						if (FALSE === (isset($assetSettings['external']) && $assetSettings['external'] > 0)) {
							$absolutePathAndFilename = t3lib_div::getFileAbsFileName($fileRelativePathAndFilename);
							if (FALSE === file_exists($absolutePathAndFilename)) {
								throw new RuntimeException('Asset "' . $absolutePathAndFilename . '" does not exist.');
							}
							$fileRelativePathAndFilename = substr($absolutePathAndFilename, strlen(constant('PATH_site')));
							$fileRelativePathAndFilename .= $this->appendModificationTime($fileRelativePathAndFilename);
							$fileRelativePathAndFilename = $this->prefixPath($fileRelativePathAndFilename);
						}
						$chunks[] = $this->generateTagForAssetType($type, NULL, $fileRelativePathAndFilename);
					} else {
						$chunks[] = $this->generateTagForAssetType($type, $this->extractAssetContent($asset));
					}
				} else {
					$chunk[$name] = $asset;
				}
			}
			if (0 < count($chunk)) {
				$mergedFileTag = $this->writeCachedMergedFileAndReturnTag($chunk, $type);
				$chunk = array($mergedFileTag);
			}
			$content = implode(LF, $chunk);
			array_push($chunks, $content);
		}
		return implode(LF, $chunks);
	}

	/**
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface[] $assets
	 * @param string $type
	 * @return string
	 */
	private function writeCachedMergedFileAndReturnTag($assets, $type) {
		$ttl = (TRUE === isset($GLOBALS['TSFE']->tmpl->setup['config.']['cache_period']) && $GLOBALS['TSFE']->tmpl->setup['config.']['cache_period'] !== 0)
			? $GLOBALS['TSFE']->tmpl->setup['config.']['cache_period'] : 10800;
		$source = '';
		$assetName = implode('-', array_keys($assets));
		if (TRUE === isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename'])) {
			if (TRUE === (boolean) $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename']) {
				$assetName = md5($assetName);
			}
		}
		$fileRelativePathAndFilename = 'typo3temp/vhs-assets-' . $assetName . '.'.  $type;
		$fileAbsolutePathAndFilename = t3lib_div::getFileAbsFileName($fileRelativePathAndFilename);
		if (FALSE === file_exists($fileAbsolutePathAndFilename) || filectime($fileAbsolutePathAndFilename) < time() - $ttl) {
			foreach ($assets as $name => $asset) {
				$settings = $this->extractAssetSettings($asset);
				if (TRUE === (isset($settings['namedChunks']) && 0 < $settings['namedChunks']) || FALSE === isset($settings['namedChunks'])) {
					$source .= '/* ' . $name . ' */' . LF;
				}
				$source .= $this->extractAssetContent($asset) . LF;
				// Put a return carriage between assets preventing broken content.
				$source .= "\n";
			}
			file_put_contents($fileAbsolutePathAndFilename, $source);
		}
		$fileRelativePathAndFilename .= $this->appendModificationTime($fileRelativePathAndFilename);
		$fileRelativePathAndFilename = $this->prefixPath($fileRelativePathAndFilename);
		return $this->generateTagForAssetType($type, NULL, $fileRelativePathAndFilename);
	}

	/**
	 * @param string $type
	 * @param string $content
	 * @param string $file
	 * @return string
	 * @throws RuntimeException
	 */
	private function generateTagForAssetType($type, $content, $file = NULL) {
		/** @var $tagBuilder Tx_Fluid_Core_ViewHelper_TagBuilder */
		$tagBuilder = $this->objectManager->get('Tx_Fluid_Core_ViewHelper_TagBuilder');
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
				if ($file === NULL) {
					return $content;
				} else {
					throw new RuntimeException('Attempt to include file based asset with unknown type ("' . $type . '")', 1358645219);
				}
				break;
		}
		return $tagBuilder->render();
	}

	/**
	 * @param array $assets
	 * @return array
	 * @throws RuntimeException
	 */
	private function manipulateAssetsByTypoScriptSetttings($assets) {
		$settings = $this->getSettings();
		if (FALSE === (isset($settings['asset']) || isset($settings['assetGroup']))) {
			return $assets;
		}
		$filtered = array();
		foreach ($assets as $name => $asset) {
			$assetSettings = $this->extractAssetSettings($asset);
			$groupName = $assetSettings['group'];
			$removed = (TRUE === isset($assetSettings['removed']) ? $assetSettings['removed'] : FALSE);
			if (TRUE === $removed) {
				continue;
			}
			$localSettings = $assetSettings;
			if (TRUE === isset($settings['asset'][$name])) {
				$localSettings = t3lib_div::array_merge_recursive_overrule($localSettings, (array) $settings['asset'][$name]);
			}
			if (TRUE === isset($settings['assetGroup'][$groupName])) {
				$localSettings = t3lib_div::array_merge_recursive_overrule($localSettings, (array) $settings['assetGroup'][$groupName]);
			}
			if (TRUE === $asset instanceof Tx_Vhs_ViewHelpers_Asset_AssetInterface) {
				$asset->setSettings($localSettings);
				$filtered[$name] = $asset;
			} else {
				$filtered[$name] = $assetSettings;
			}
		}
		return $filtered;
	}

	/**
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface[] $assets
	 * @return Tx_Vhs_ViewHelpers_Asset_AssetInterface[]
	 * @throws RuntimeException
	 */
	private function sortAssetsByDependency($assets) {
		$placed = array();
		$compilables = array();
		$assetNames = (0 < count($assets)) ? array_combine(array_keys($assets), array_keys($assets)) : array();
		while ($asset = array_shift($assets)) {
			$postpone = FALSE;
			/** @var $asset Tx_Vhs_ViewHelpers_Asset_AssetInterface */
			$assetSettings = $this->extractAssetSettings($asset);
			$name = array_shift($assetNames);
			$dependencies = $assetSettings['dependencies'];
			if (FALSE === is_array($dependencies)) {
				$dependencies = t3lib_div::trimExplode(',', $assetSettings['dependencies'], TRUE);
			}
			foreach ($dependencies as $dependency) {
				if (FALSE === isset($placed[$dependency]) && FALSE === in_array($dependency, self::$cachedDependencies)) {
					// shove the Asset back to the end of the queue, the dependency has
					// not yet been encountered and moving this item to the back of the
					// queue ensures it will be encountered before re-encountering this
					// specific Asset
					if (0 === count($assets)) {
						throw new RuntimeException('Asset "' . $name . '" depends on "' . $dependency . '" but "' . $dependency . '" was not found', 1358603979);
					}
					$assets[$name] = $asset;
					$assetNames[$name] = $name;
					$postpone = TRUE;
				}
			}
			if (FALSE === $postpone) {
				if (TRUE === $asset instanceof Tx_Vhs_ViewHelpers_Asset_Compilable_CompilableAssetInterface) {
					$compilerClassName = $asset->getCompilerClassName();
					if (FALSE === isset($compilables[$compilerClassName])) {
						$compilables[$compilerClassName] = array();
					}
					array_push($compilables[$compilerClassName], $asset);
				} else {
					$placed[$name] = $asset;
				}
			}
		}
		if (0 < count($compilables)) {
			// loop once more, this time assigning compilable assets to their compilers
			foreach ($placed as $asset) {
				if (TRUE === $asset instanceof Tx_Vhs_ViewHelpers_Asset_Compilable_AssetCompilerInterface) {
					/** @var $asset Tx_Vhs_ViewHelpers_Asset_Compilable_AssetCompilerInterface */
					$compilerClassName = get_class($asset);
					$compilerTopInterfaceName = array_shift(class_implements($compilerClassName));
					if ('Tx_Vhs_ViewHelpers_Asset_Compilable_AssetCompilerInterface' !== $compilerTopInterfaceName) {
						$compilerIdentity = $compilerTopInterfaceName;
					} else {
						$compilerIdentity = $compilerClassName;
					}
					if (TRUE === isset($compilables[$compilerIdentity])) {
						foreach ($compilables[$compilerIdentity] as $compilableAsset) {
							$asset->addAsset($compilableAsset);
						}
						unset($compilables[$compilerIdentity]);
					}
				}
			}
			if (0 < count($compilables)) {
				throw new RuntimeException('Compilable Assets used without appropriate Compiler Assets: "' .
					implode(', ', array_keys($compilables)) . '"', 1360502808);
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
		$templateReference = $settings['path'];
		$variables = (TRUE === (isset($settings['arguments']) && is_array($settings['arguments'])) ? $settings['arguments'] : array());
		$isExternal = (TRUE === (isset($settings['external']) && $settings['external'] > 0));
		if (TRUE === $isExternal) {
			$fileContents = file_get_contents($templateReference);
		} else {
			$templatePathAndFilename = t3lib_div::getFileAbsFileName($templateReference);
			$fileContents = file_get_contents($templatePathAndFilename);
		}
		/** @var $view Tx_Fluid_View_StandaloneView */
		$view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		$view->setTemplateSource($fileContents);
		$view->assignMultiple($variables);
		$content = $view->render();
		return $content;
	}

	/**
	 * Append last modification time to the file preventing un-wanted caching of the file by the browser.
	 *
	 * @param string $fileRelativePathAndFilename
	 * @return string
	 */
	protected function appendModificationTime($fileRelativePathAndFilename) {
		if (file_exists($fileRelativePathAndFilename)) {
			$fileRelativePathAndFilename = '?' . filemtime($fileRelativePathAndFilename);
		}
		return $fileRelativePathAndFilename;
	}

	/**
	 * Prefix a path according to "absRefPrefix" TS configuration.
	 *
	 * @param string $fileRelativePathAndFilename
	 * @return string
	 */
	protected function prefixPath($fileRelativePathAndFilename) {
		if (FALSE === empty($GLOBALS['TSFE']->tmpl->setup['config.']['absRefPrefix'])) {
			$fileRelativePathAndFilename = $GLOBALS['TSFE']->tmpl->setup['config.']['absRefPrefix'] . $fileRelativePathAndFilename;
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
				$checksum = md5($match);
				if (preg_match('/([^\?#]+)(.+)?/', $match, $items)) {
					list(,$path, $suffix) = $items;
				} else {
					$path = $match;
					$suffix = '';
				}
				$newPath = basename($path);
				$extension = pathinfo($newPath, PATHINFO_EXTENSION);
				$temporaryFileName = 'vhs-assets-css-' . $checksum . '.' . $extension;
				$temporaryFile = constant('PATH_site') . 'typo3temp/' . $temporaryFileName;
				$rawPath = t3lib_div::getFileAbsFileName($originalDirectory . (TRUE === empty($originalDirectory) ? '' : '/')) . $path;
				$realPath = realpath($rawPath);
				if (FALSE === $realPath) {
					t3lib_div::sysLog('Asset at path "'. $rawPath .'" not found. Processing skipped.', t3lib_div::SYSLOG_SEVERITY_WARNING);
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
		if (TRUE === $asset instanceof Tx_Vhs_ViewHelpers_Asset_AssetInterface) {
			return $asset->assertAllowedInFooter();
		}
		return (boolean) (TRUE === isset($asset['allowMoveToFooter']) ? $asset['allowMoveToFooter'] : TRUE);
	}

	/**
	 * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
	 * @return array
	 */
	protected function extractAssetSettings($asset) {
		if (TRUE === $asset instanceof Tx_Vhs_ViewHelpers_Asset_AssetInterface) {
			return $asset->getAssetSettings();
		}
		return $asset;
	}

	/**
	 * @param mixed $asset An Asset ViewHelper instance or an array containing an Asset definition
	 * @return string
	 */
	protected function buildAsset($asset) {
		if (TRUE === $asset instanceof Tx_Vhs_ViewHelpers_Asset_AssetInterface) {
			return $asset->build();
		}
		if (FALSE === isset($asset['path']) || TRUE === empty($asset['path'])) {
			return (TRUE === isset($asset['content']) ? $asset['content'] : NULL);
		}
		$absolutePathAndFilename = t3lib_div::getFileAbsFileName($asset['path']);
		$content = file_get_contents($absolutePathAndFilename);
		return $content;
	}

	/**
	 * @param mixed $asset
	 * @throws RuntimeException
	 * @return string
	 */
	private function extractAssetContent($asset) {
		$assetSettings = $this->extractAssetSettings($asset);
		$fileRelativePathAndFilename = $assetSettings['path'];
		$fileRelativePath = dirname($assetSettings['path']);
		$absolutePathAndFilename = t3lib_div::getFileAbsFileName($fileRelativePathAndFilename);
		$isExternal = (TRUE === (isset($assetSettings['external']) && $assetSettings['external'] > 0));
		$isFluidTemplate = (TRUE === (isset($assetSettings['fluid']) && $assetSettings['fluid'] > 0));
		if (FALSE === empty($fileRelativePathAndFilename)) {
			if (FALSE === $isExternal && FALSE === file_exists($absolutePathAndFilename)) {
				throw new RuntimeException('Asset "' . $absolutePathAndFilename . '" does not exist.');
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
		$assetCacheFiles = glob(t3lib_div::getFileAbsFileName('typo3temp/vhs-assets-*'));
		foreach ($assetCacheFiles as $assetCacheFile) {
			unlink($assetCacheFile);
		}
		self::$cacheCleared = TRUE;
	}

}
