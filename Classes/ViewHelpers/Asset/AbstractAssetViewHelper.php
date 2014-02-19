<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 * Base class for ViewHelpers capable of registering assets
 * which will be included when rendering the page.
 *
 * Note: building of all Assets takes place in the class
 * Tx_Vhs_ViewHelpers_AssetViewHelper with two reasons:
 *
 * - A "buildAll" method should never be possible to call
 *   from any Asset ViewHelper; it should only be possible
 *   from a single class.
 * - The method but must be public and non-static and thus
 *   cannot be hidden from access by subclasses if placed
 *   in this class.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Asset
 */
abstract class Tx_Vhs_ViewHelpers_Asset_AbstractAssetViewHelper
	extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
	implements Tx_Vhs_ViewHelpers_Asset_AssetInterface {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Vhs_Service_AssetService
	 */
	protected $assetService;

	/**
	 * @var array
	 */
	private static $settingsCache = NULL;

	/**
	 * @var array
	 */
	private $assetSettingsCache;

	/**
	 * @var array
	 */
	protected $localSettings;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder
	 */
	protected $tagBuilder;

	/**
	 * Example types: raw, meta, css, js.
	 *
	 * If a LESS stylesheet is being compiled the "type"
	 * would be "css" because this will group the compiled
	 * LESS stylesheet with other CSS - allowing it to be
	 * merged with other CSS.
	 *
	 * @var string
	 */
	protected $type = 'raw';


	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param Tx_Vhs_Service_AssetService $assetService
	 * @return void
	 */
	public function injectAssetService(Tx_Vhs_Service_AssetService $assetService) {
		$this->assetService = $assetService;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
		$this->tagBuilder = $this->objectManager->get('TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder');
	}

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('content', 'string', 'Content to insert in header/footer', FALSE, NULL);
		$this->registerArgument('path', 'string', 'If not using tag content, specify path to file here', FALSE, NULL);
		$this->registerArgument('external', 'boolean', 'If TRUE and standalone, includes the file as raw URL. If TRUE and not standalone then downloads the file and merges it when building Assets', FALSE, FALSE);
		$this->registerArgument('name', 'string', 'Optional name of the content. If multiple occurrences of the same name happens, behavior is defined by argument "overwrite"');
		$this->registerArgument('overwrite', 'boolean', 'If set to FALSE and a relocated string with "name" already exists, does not overwrite the existing relocated string. Default behavior is to overwrite.', FALSE, TRUE);
		$this->registerArgument('dependencies', 'string', 'CSV list of other named assets upon which this asset depends. When included, this asset will always load after its dependencies');
		$this->registerArgument('group', 'string', 'Optional name of a logical group (created dynamically just by using the name) to which this particular asset belongs.', FALSE, 'fluid');
		$this->registerArgument('debug', 'boolean', 'If TRUE, outputs information about this ViewHelper when the tag is used. Two master debug switches exist in TypoScript; see documentation about Page / Asset ViewHelper');
		$this->registerArgument('standalone', 'boolean', 'If TRUE, excludes this Asset from any concatenation which may be applied');
		$this->registerArgument('rewrite', 'boolean', 'If FALSE, this Asset will be included as is without any processing of contained urls', FALSE, TRUE);
		$this->registerArgument('fluid', 'boolean', 'If TRUE, renders this (standalone or external) Asset as if it were a Fluid template, passing along values of the "arguments" attribute or every available template variable if "arguments" not specified', FALSE, FALSE);
		$this->registerArgument('arguments', 'mixed', 'An optional array of arguments which you use inside the Asset, be it standalon or inline. Use this argument to ensure your Asset filenames are only reused when all variables used in the Asset are the same', FALSE, FALSE);
		$this->registerArgument('allowMoveToFooter', 'boolean', 'If TRUE, allows this Asset to be included in the document footer rather than the header. Should never be allowed for CSS.', FALSE, TRUE);
		$this->registerArgument('trim', 'boolean', 'DEPRECATED. Trim is no longer supported. Setting this to TRUE doesn\'t do anything.', FALSE, FALSE);
		$this->registerArgument('namedChunks', 'boolean', 'If FALSE, hides the comment containing the name of each of Assets which is merged in a merged file. Disable to avoid a bit more output at the cost of transparency', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->build();
	}

	/**
	 * Render method
	 *
	 * @return void
	 */
	public function render() {
		$this->finalize();
	}

	/**
	 * Build this asset. Override this method in the specific
	 * implementation of an Asset in order to:
	 *
	 * - if necessary compile the Asset (LESS, SASS, CoffeeScript etc)
	 * - make a final rendering decision based on arguments
	 *
	 * Note that within this function the ViewHelper and TemplateVariable
	 * Containers are not dependable, you cannot use the ControllerContext
	 * and RenderingContext and you should therefore also never call
	 * renderChildren from within this function. Anything else goes; CLI
	 * commands to build, caching implementations - you name it.
	 *
	 * @return mixed
	 */
	public function build() {
		if (FALSE === isset($this->arguments['path']) || TRUE === empty($this->arguments['path'])) {
			return $this->getContent();
		}
		if (TRUE === isset($this->arguments['external']) && TRUE === (boolean) $this->arguments['external']) {
			$path = $this->arguments['path'];
		} else {
			$path = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->arguments['path']);
		}
		$content = file_get_contents($path);
		return $content;
	}

	/**
	 * Saves this Asset or perhaps discards it if overriding is
	 * disabled and an identically named Asset already exists.
	 *
	 * Performed from every Asset's render() for it to work.
	 *
	 * @return void
	 */
	protected function finalize() {
		$this->assetSettingsCache = NULL;
		$this->localSettings = NULL;
		if (FALSE === isset($GLOBALS['VhsAssets'])) {
			$GLOBALS['VhsAssets'] = array();
		}
		$name = $this->getName();
		$overwrite = $this->getOverwrite();
		$slotFree = FALSE === isset($GLOBALS['VhsAssets'][$name]);
		if (FALSE === ($overwrite || $slotFree)) {
			return;
		}
		$this->content = $this->getContent();
		$this->tagBuilder->setContent($this->content);
		$this->debug();
		$GLOBALS['VhsAssets'][$name] = clone $this;
	}

	/**
	 * @return mixed
	 */
	protected function debug() {
		$settings = $this->getSettings();
		$debugOutputEnabled = $this->assertDebugEnabled();
		$useDebugUtility = FALSE === isset($settings['useDebugUtility']) || (isset($settings['useDebugUtility']) && $settings['useDebugUtility'] > 0);
		$debugInformation = $this->getDebugInformation();
		if (TRUE === $debugOutputEnabled) {
			if (TRUE === $useDebugUtility) {
				Tx_Extbase_Utility_Debugger::var_dump($debugInformation);
			} else {
				return var_export($debugInformation, TRUE);
			}
		}
	}

	/**
	 * @return array
	 */
	public function getDependencies() {
		$assetSettings = $this->getAssetSettings();
		if (TRUE === isset($assetSettings['dependencies'])) {
			return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $assetSettings['dependencies'], TRUE);
		}
		return array();
	}

	/**
	 * @return boolean
	 */
	protected function getOverwrite() {
		$assetSettings = $this->getAssetSettings();
		return (TRUE === isset($assetSettings['overwrite']) && $assetSettings['overwrite'] > 0);
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getName() {
		$assetSettings = $this->getAssetSettings();
		if (TRUE === isset($assetSettings['name']) && FALSE === empty($assetSettings['name'])) {
			$name = $assetSettings['name'];
		} else {
			$name = md5(serialize($assetSettings));
		}
		$variables = $this->getVariables();
		if (0 < count($variables)) {
			$name .= '-' . md5(serialize($variables));
		}
		return $name;
	}

	/**
	 * @return string
	 */
	public function getGroup() {
		$assetSettings = $this->getAssetSettings();
		return $assetSettings['group'];
	}

	/**
	 * @return string
	 */
	protected function getContent() {
		$assetSettings = $this->getAssetSettings();
		if (TRUE === isset($assetSettings['content']) && FALSE === empty($assetSettings['content'])) {
			$content = $assetSettings['content'];
		} else {
			$content = $this->renderChildren();
		}
		return $content;
	}

	/**
	 * @return string
	 */
	protected function getTagWithContent() {
		return $this->tagBuilder->render();
	}

	/**
	 * @return array
	 */
	public function getVariables() {
		$assetSettings = $this->getAssetSettings();
		if (TRUE === (isset($assetSettings['arguments']) && is_array($assetSettings['arguments']))) {
			return $assetSettings['arguments'];
		}
		return array();
	}

	/**
	 * Returns the settings used by this particular Asset
	 * during inclusion. Public access allows later inspection
	 * of the TypoScript values which were applied to the Asset.
	 *
	 * @return array
	 */
	public function getSettings() {
		if (TRUE === is_null(self::$settingsCache)) {
			$allTypoScript = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
			$settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			if (FALSE === $settingsExist) {
					// no settings exist, but don't allow a NULL value. This prevents cache clobbering.
				self::$settingsCache = array();
			} else {
				self::$settingsCache = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			}
		}
		$settings = self::$settingsCache;
		if (TRUE === is_array($this->localSettings)) {
			$settings = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($settings, $this->localSettings);
		}
		return $settings;
	}

	/**
	 * @param array|ArrayAccess $settings
	 */
	public function setSettings($settings) {
		if (TRUE === is_array($settings) || TRUE === $settings instanceof ArrayAccess) {
			$this->localSettings = $settings;
		}
	}

	/**
	 * @return array
	 */
	public function getAssetSettings() {
		if (TRUE === is_array($this->assetSettingsCache)) {
			return $this->assetSettingsCache;
		}
			// Note: name and group are taken directly from arguments; if they are changed through
			// TypoScript the changed values will be returned from this function.
		$name = $this->arguments['name'];
		$groupName = $this->arguments['group'];
		$settings = $this->getSettings();
		$assetSettings = $this->arguments;
		$assetSettings['type'] = $this->getType();
		if (TRUE === isset($settings['asset']) && TRUE === is_array($settings['asset'])) {
			$assetSettings = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($assetSettings, $settings['asset']);
		}
		if (TRUE === (isset($settings['assetGroup'][$groupName]) && is_array($settings['assetGroup'][$groupName]))) {
			$assetSettings = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($assetSettings, $settings['assetGroup'][$groupName]);
		}
		if (TRUE === (isset($settings['asset'][$name]) && is_array($settings['asset'][$name]))) {
			$assetSettings = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($assetSettings, $settings['asset'][$name]);
		}
		if (FALSE === empty($assetSettings['path']) && FALSE === (boolean) $assetSettings['external']) {
			$assetSettings['path'] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($assetSettings['path']);
		}
		$assetSettings['name'] = $name;
		$this->assetSettingsCache = $assetSettings;
		return $assetSettings;
	}

	/**
	 * Allows public access to debug this particular Asset
	 * instance later, when including the Asset in the page.
	 *
	 * @return array
	 */
	public function getDebugInformation() {
		return array(
			'class' => get_class($this),
			'settings' => $this->getAssetSettings()
		);
	}

	/**
	 * Returns TRUE of settings specify that the source of this
	 * Asset should be rendered as if it were a Fluid template,
	 * using variables from the "arguments" attribute
	 *
	 * @return boolean
	 */
	public function assertFluidEnabled() {
		$settings = $this->getAssetSettings();
		if (TRUE === (isset($settings['fluid']) && $settings['fluid'] > 0)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Returns TRUE if settings specify that the name of each Asset
	 * should be placed above the built content when placed in merged
	 * Asset cache files.
	 *
	 * @return boolean
	 */
	public function assertAddNameCommentWithChunk() {
		$settings = $this->getAssetSettings();
		if (TRUE === (isset($settings['namedChunks']) && 0 < $settings['namedChunks']) || FALSE === isset($settings['namedChunks'])) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Returns TRUE if the current Asset should be debugged as commanded
	 * by settings in TypoScript an/ord ViewHelper attributes.
	 *
	 * @return boolean
	 */
	public function assertDebugEnabled() {
		$settings = $this->getSettings();
		if (TRUE === (isset($settings['debug']) && $settings['debug'] > 0)) {
			return TRUE;
		}
		$settings = $this->getAssetSettings();
		if (TRUE === (isset($settings['asset']['debug']) && $settings['asset']['debug'] > 0)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @return boolean
	 */
	public function assertAllowedInFooter() {
		$settings = $this->getAssetSettings();
		if (TRUE === (isset($settings['allowMoveToFooter']) && $settings['allowMoveToFooter'] < 1)) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * @return boolean
	 */
	public function assertHasBeenRemoved() {
		$groupName = $this->arguments['group'];
		$settings = $this->getSettings();
		$dependencies = $this->getDependencies();
		array_push($dependencies, $this->getName());
		foreach ($dependencies as $name) {
			if (TRUE === isset($settings['asset'][$name]['remove']) && $settings['asset'][$name]['remove'] > 0) {
				return TRUE;
			}
		}
		if (TRUE === isset($settings['assetGroup'][$groupName]['remove']) && $settings['assetGroup'][$groupName]['remove'] > 0) {
			return TRUE;
		}
		return FALSE;
	}

}
