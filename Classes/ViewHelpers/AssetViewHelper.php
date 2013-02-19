<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due, Wildside A/S <claus@wildside.dk>
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
 * ### Basic Asset ViewHelper
 *
 * Places the contents of the asset (the tag body) directly
 * in the additional header content of the page. This most
 * basic possible version of an Asset has only the core
 * features shared by every Asset type:
 *
 * - a "name" attribute which is required, identifying the Asset
 *   by a lowerCamelCase or lowercase_underscored value, your
 *   preference (but lowerCamelCase recommended for consistency).
 * - a "dependencies" attribute with a CSV list of other named
 *   Assets upon which the current Asset depends. When used, this
 *   Asset will be included after every asset listed as dependency.
 * - a "group" attribute which is optional and is used ty further
 *   identify the Asset as belonging to a particular group which
 *   can be suppressed or manipulated through TypoScript. For
 *   example, the default value is "fluid" and if TypoScript is
 *   used to exclude the group "fluid" then any Asset in that
 *   group will simply not be loaded.
 * - an "overwrite" attribute which if enabled causes any existing
 *   asset with the same name to be overwritten with the current
 *   Asset instead. If rendered in a loop only the last instance
 *   is actually used (this allows Assets in Partials which are
 *   rendered in an f:for loop).
 * - a "debug" property which enables output of the information
 *   used by the current Asset, with an option to force debug
 *   mode through TypoScript.
 * - additional properties which affect how the Asset is processed.
 *   For a full list see the argument descriptions; the same
 *   settings can be applied through TypoScript per-Asset, globally
 *   or per-Asset-group.
 *
 * > Note: there are no static TypoScript templates for VHS but
 * > you will find a complete list in the README.md file in the
 * > root of the extension folder.
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_AssetViewHelper extends Tx_Vhs_ViewHelpers_Asset_AbstractAssetViewHelper {

	/**
	 * @var array
	 */
	private static $cachedDependencies = array();

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @param boolean $cached If TRUE, treats this inclusion as happening in a cached context
	 * @return void
	 */
	public function buildAll(array $parameters, $caller, $cached = TRUE) {
		if (FALSE === isset($GLOBALS['VhsAssets'])) {
			return;
		}
		$cached = (boolean) $cached;
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$assets = $GLOBALS['VhsAssets'];
		$assets = $this->sortAssetsByDependency($assets);
		$assets = $this->manipulateAssetsByTypoScriptSetttings($assets);
		$settings = $this->getSettings();
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
	}

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @return void
	 */
	public function buildAllUncached(array $parameters, $caller) {
		$content = $GLOBALS['TSFE']->content;
		$matches = array();
		preg_match_all('/\<\!\-\-\-\- VhsAssetsDependenciesLoaded ([a-zA-Z0-9\-\,]{1,}) \-\-\-\-\!\>/i', $content, $matches);
		self::$cachedDependencies = explode(',', $matches[1][0]);
		$content = str_replace($matches[0][0], '', $content);
		$GLOBALS['TSFE']->content = $content;
		$this->buildAll($parameters, $caller, FALSE);
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
			if (TRUE === ($asset->assertAllowedInFooter() && $footerRelocationEnabled)) {
				$footer[$name] = $asset;
			} else {
				$header[$name] = $asset;
			}
		}
		if (FALSE === $cached) {
			$uncachedSuffix = 'Uncached';
		} else {
			$uncachedSuffix = '';
			$dependenciesString = '<!---- VhsAssetsDependenciesLoaded ' . implode(',', array_keys($assets)) . ' ----!>';
			$this->insertAssetsAtMarker('DependenciesLoaded', $dependenciesString);
		}
		$this->insertAssetsAtMarker('Header' . $uncachedSuffix, $header);
		$this->insertAssetsAtMarker('Footer' . $uncachedSuffix, $footer);
		$GLOBALS['VhsAssets'] = array();
	}

	/**
	 * @param string $markerName
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface[] $assets
	 * @return void
	 */
	private function insertAssetsAtMarker($markerName, $assets) {
		if (FALSE === strpos($GLOBALS['TSFE']->content, '<!---- VhsAssets' . $markerName . ' ----!>')) {
			$assetMarker = '<!---- VhsAssets' . $markerName . ' ----!>';
			$inFooter = FALSE !== strpos($markerName, 'Footer');
			$tag = TRUE === $inFooter ? '</body>' : '</head>';
			$GLOBALS['TSFE']->content = str_replace($tag, $assetMarker . LF . $tag, $GLOBALS['TSFE']->content);
		}
		if (TRUE === is_array($assets)) {
			$chunk = $this->buildAssetsChunk($assets);
		} else {
			$chunk = $assets;
		}
		$GLOBALS['TSFE']->content = str_replace('<!---- VhsAssets' . $markerName . ' ----!>', $chunk, $GLOBALS['TSFE']->content);
	}

	/**
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface[] $assets
	 * @return string
	 */
	private function buildAssetsChunk($assets) {
		$spool = array();
		foreach ($assets as $name => $asset) {
			$type = $asset->getType();
			if (FALSE === isset($spool[$type])) {
				$spool[$type] = array();
			}
			$spool[$type][$name] = $asset;
		}
		$chunks = array();
		foreach ($spool as $type => $spooledAssets) {
			$standalone = FALSE;
			$chunk = array();
			$source = '';
			/** @var $spooledAssets Tx_Vhs_ViewHelpers_Asset_AssetInterface[] */
			foreach ($spooledAssets as $name => $asset) {
				$assetSettings = $asset->getAssetSettings();
				$standalone = (TRUE === (boolean) $assetSettings['standalone']);
				if (TRUE === $standalone) {
					if (0 < count($chunk)) {
						$mergedFileTag = $this->writeCachedMergedFileAndReturnTag($chunk, $type);
						$chunk = array();
						array_push($chunks, $mergedFileTag);
					}
					if (TRUE === isset($assetSettings['path'])) {
						$fileRelativePathAndFilename = $assetSettings['path'];
						if (FALSE === (isset($assetSettings['external']) && $assetSettings['external'] > 0)) {
							$absolutePathAndFilename = t3lib_div::getFileAbsFileName($fileRelativePathAndFilename);
							if (FALSE === file_exists($absolutePathAndFilename)) {
								throw new RuntimeException('Asset "' . $absolutePathAndFilename . '" does not exist.');
							}
							$fileRelativePathAndFilename = substr($absolutePathAndFilename, strlen(PATH_site));
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
		// @todo Finish implementation. This line is currently not used. I guess the intention was to better handle cache time of a file (vs. writing the file every time)
		$ttl = (TRUE === isset($GLOBALS['TSFE']->tmpl->setup['config.']['cache_period']) && $GLOBALS['TSFE']->tmpl->setup['config.']['cache_period'] !== 0);
		$source = '';
		foreach ($assets as $name => $asset) {
			if (TRUE === is_string($asset)) {
				$source .= $asset;
				continue;
			}
			if (TRUE === $asset->assertAddNameCommentWithChunk()) {
				$source .= '/* ' . $name . ' */' . LF;
			}
			$assetSettings = $asset->getAssetSettings();
			if (FALSE === isset($assetSettings['path'])) {
				$source .= $this->extractAssetContent($asset) . LF;
			} else {
				$fileRelativePathAndFilename = $assetSettings['path'];
				$absolutePathAndFilename = t3lib_div::getFileAbsFileName($fileRelativePathAndFilename);
				$isExternal = (TRUE === (isset($assetSettings['external']) && $assetSettings['external'] > 0));
				$isFluidTemplate = $asset->assertFluidEnabled();
				if (FALSE === $isExternal && FALSE === file_exists($absolutePathAndFilename)) {
					throw new RuntimeException('Asset "' . $absolutePathAndFilename . '" does not exist.');
				}
				if (TRUE === $isFluidTemplate) {
					$source .= $this->renderAssetAsFluidTemplate($asset);
				} else {
					$source .= file_get_contents($absolutePathAndFilename);
				}
			}
			// Put a return carriage between assets preventing broken content.
			$source .= "\n";
		}
		$assetName = implode('-', array_keys($assets));
		if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['mergedAssetsUseHashedFilename']) {
			$assetName = md5($assetName);
		}
		$fileRelativePathAndFilename = 'typo3temp/vhs-assets-' . $assetName . '.'.  $type;
		file_put_contents(PATH_site . $fileRelativePathAndFilename, $source);
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
		$tagBuilder = $this->objectManager->create('Tx_Fluid_Core_ViewHelper_TagBuilder');
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
					$tagBuilder->forceClosingTag(TRUE);
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
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface[] $assets
	 * @param array $settings Optional array of settings which will override any automatically detected settings
	 * @return Tx_Vhs_ViewHelpers_Asset_AssetInterface[]
	 * @throws RuntimeException
	 */
	private function manipulateAssetsByTypoScriptSetttings($assets, $settings = NULL) {
		$settings = $this->getSettings();
		if (FALSE === (isset($settings['asset']) || isset($settings['assetGroup']))) {
			return $assets;
		}
		$filtered = array();
		foreach ($assets as $name => $asset) {
			$groupName = $asset->getGroup();
			$removed = $asset->assertHasBeenRemoved();
			if (TRUE === $removed) {
				continue;
			}
			$localSettings = array();
			if (TRUE === isset($settings['asset'][$name])) {
				$localSettings = t3lib_div::array_merge_recursive_overrule($localSettings, (array) $settings['asset'][$name]);
			}
			if (TRUE === isset($settings['assetGroup'][$groupName])) {
				$localSettings = t3lib_div::array_merge_recursive_overrule($localSettings, (array) $settings['assetGroup'][$groupName]);
			}
			$asset->setSettings($localSettings);
			$filtered[$name] = $asset;
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
		while ($asset = array_shift($assets)) {
			$postpone = FALSE;
			/** @var $asset Tx_Vhs_ViewHelpers_Asset_AssetInterface */
			$name = $asset->getName();
			$dependencies = $asset->getDependencies();
			foreach ($dependencies as $dependency) {
				if (FALSE === isset($placed[$dependency]) && FALSE === in_array($dependency, self::$cachedDependencies)) {
					// shove the Asset back to the end of the queue, the dependency has
					// not yet been encountered and moving this item to the back of the
					// queue ensures it will be encountered before re-encountering this
					// specific Asset
					if (0 === count($assets)) {
						throw new RuntimeException('Asset "' . $name . '" depends on "' . $dependency . '" but "' . $dependency . '" was not found', 1358603979);
						break;
					}
					$assets[$name] = $asset;
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
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface $asset
	 * @return string
	 */
	private function extractAssetContent(Tx_Vhs_ViewHelpers_Asset_AssetInterface $asset) {
		$isFluidTemplate = TRUE === $asset->assertFluidEnabled();
		if (TRUE === $isFluidTemplate) {
			return $this->renderAssetAsFluidTemplate($asset);
		}
		return $asset->build();
	}

	/**
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface $asset
	 * @return string
	 */
	private function renderAssetAsFluidTemplate(Tx_Vhs_ViewHelpers_Asset_AssetInterface $asset) {
		$settings = $asset->getAssetSettings();
		$templateReference = $settings['path'];
		$variables = $asset->getVariables();
		$isExternal = (TRUE === (isset($settings['external']) && $settings['external'] > 0));
		if (TRUE === $isExternal) {
			$fileContents = file_get_contents($templateReference);
		} else {
			$templatePathAndFilename = t3lib_div::getFileAbsFileName($templateReference);
			$fileContents = file_get_contents($templatePathAndFilename);
		}
		/** @var $view Tx_Fluid_View_StandaloneView */
		$view = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
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
		if (!empty($GLOBALS['TSFE']->tmpl->setup['config.']['absRefPrefix'])) {
			$fileRelativePathAndFilename = $GLOBALS['TSFE']->tmpl->setup['config.']['absRefPrefix'] . $fileRelativePathAndFilename;
		}
		return $fileRelativePathAndFilename;
	}

}
