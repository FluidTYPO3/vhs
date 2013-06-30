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
 * ### Asset
 *
 * Class to create Assets in PHP.
 *
 * ### Examples:
 *
 *     $asset = $this->objectManager->get('Tx_Vhs_Asset');
 *     // OR you can use the static factory method which works anywhere
 *     // including outside of Extbase.
 *     $asset = Tx_Vhs_Asset::getInstance();
 *     $asset->setPath('fileadmin/test.js')->setStandalone(TRUE)->finalize();
 *
 * Or simply:
 *
 *     $this->objectManager->get('Tx_Vhs_Asset')->setPath('...')->finalize();
 *
 * And you can create clean instances:
 *
 *
 * Depending on how you need to access the Asset afterwards, you will
 * want wo switch between these methods.
 *
 * Or if you have all settings in an array (with members named according to
 * the properties on this class:
 *
 *     Tx_Vhs_Asset::createFromSettings($settings)->finalize();
 *
 * Finally, if your Asset is file based, VHS can perform a few detections to
 * set initial attributes like standalone, external (if file contains protocol),
 * type (based on extension) and name (base name of file).
 *
 *     Tx_Vhs_Asset::createFromFile($filePathAndFilename);
 *     Tx_Vhs_Asset::createFromUrl($urlToExternalFile);
 *
 * You can chain all setters on the class before finally calling finalize() to
 * register the Asset (you can still modify the Asset afterwards, but an Asset
 * that has not been finalize()'ed will never show up or be processed - which
 * is a lot friendlier than requiring you to use remove() on unwanted Assets).
 *
 * > Note: the "createFrom..." suite of methods automatically calls finalize()
 * > on your Asset just before returning it. You can of course keep modifying
 * > the instance after it is returned - but when using a "createFrom"... method
 * > VHS assumes you always want your Asset included in the output.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 */
class Tx_Vhs_Asset implements Tx_Vhs_ViewHelpers_Asset_AssetInterface {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var array
	 */
	protected $dependencies = array();

	/**
	 * @var string
	 */
	protected $type = NULL;

	/**
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * @var string
	 */
	protected $content = NULL;

	/**
	 * @var string
	 */
	protected $path = NULL;

	/**
	 * @var boolean
	 */
	protected $namedChunks = FALSE;

	/**
	 * @var boolean
	 */
	protected $movable = TRUE;

	/**
	 * @var boolean
	 */
	protected $removed = FALSE;

	/**
	 * @var boolean
	 */
	protected $fluid = FALSE;

	/**
	 * @var array
	 */
	protected $variables = array();

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @var boolean
	 */
	protected $external = FALSE;

	/**
	 * @var boolean
	 */
	protected $standalone = FALSE;

	/**
	 * @var array
	 */
	private static $settingsCache = NULL;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @return Tx_Vhs_Asset
	 */
	public static function getInstance() {
		/** @var $asset Tx_Vhs_Asset */
		$asset = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager')->get('Tx_Vhs_Asset');
		return $asset;
	}

	/**
	 * @param array $settings
	 * @return Tx_Vhs_Asset
	 */
	public static function createFromSettings(array $settings) {
		$asset = self::getInstance();
		foreach ($settings as $propertyName => $value) {
			Tx_Extbase_Reflection_ObjectAccess::setProperty($asset, $propertyName, $value);
		}
		return $asset->finalize();
	}

	/**
	 * @param string $filePathAndFilename
	 * @return Tx_Vhs_Asset
	 */
	public static function createFromFile($filePathAndFilename) {
		$asset = self::getInstance();
		$asset->setExternal(FALSE);
		$asset->setPath($filePathAndFilename);
		return $asset->finalize();
	}

	/**
	 * @param string $content
	 * @return Tx_Vhs_Asset
	 */
	public static function createFromContent($content) {
		$asset = self::getInstance();
		$asset->setContent($content);
		$asset->setName(md5($content));
		return $asset->finalize();
	}

	/**
	 * @param string $url
	 * @return Tx_Vhs_Asset
	 */
	public static function createFromUrl($url) {
		$asset = self::getInstance();
		$asset->setStandalone(TRUE);
		$asset->setExternal(TRUE);
		$asset->setPath($url);
		return $asset->finalize();
	}

	/**
	 * Render method
	 *
	 * @return void
	 */
	public function render() {
		return $this->build();
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
		$path = $this->getPath();
		if (TRUE === empty($path)) {
			return $this->getContent();
		}
		$content = file_get_contents($path);
		return $content;
	}

	/**
	 * @return void
	 */
	public function finalize() {
		$name = $this->getName();
		if (TRUE === empty($name)) {
			$name = md5(spl_object_hash($this));
		}
		if (FALSE === isset($GLOBALS['VhsAssets']) || FALSE === is_array($GLOBALS['VhsAssets'])) {
			$GLOBALS['VhsAssets'] = array();
		}
		$GLOBALS['VhsAssets'][$name] = $this;
		return $this;
	}

	/**
	 * @return Tx_Vhs_Asset
	 */
	public function remove() {
		return $this->setRemoved(TRUE);
	}

	/**
	 * @return array
	 */
	public function getDependencies() {
		return $this->dependencies;
	}

	/**
	 * @param array $dependencies
	 * @return Tx_Vhs_Asset
	 */
	public function setDependencies($dependencies) {
		$this->dependencies = $dependencies;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return Tx_Vhs_Asset
	 */
	public function setType($type) {
		$this->type = $type;
		if ('css' == strtolower($type)) {
			$this->setMovable(FALSE);
		}
		return $this;
	}

	/**
	 * @param boolean $external
	 * @return Tx_Vhs_Asset
	 */
	public function setExternal($external) {
		$this->external = $external;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getExternal() {
		return $this->external;
	}

	/**
	 * @param boolean $standalone
	 * @return Tx_Vhs_Asset
	 */
	public function setStandalone($standalone) {
		$this->standalone = $standalone;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getStandalone() {
		return $this->standalone;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Tx_Vhs_Asset
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		if (TRUE === empty($this->content) && NULL !== $this->path && file_exists(t3lib_div::getFileAbsFileName($this->path))) {
			return file_get_contents(t3lib_div::getFileAbsFileName($this->path));
		}
		return $this->content;
	}

	/**
	 * @param string $content
	 * @return Tx_Vhs_Asset
	 */
	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return Tx_Vhs_Asset
	 */
	public function setPath($path) {
		if (NULL === $path) {
			$this->path = NULL;
			return $this;
		}
		if (FALSE === strpos($path, '://')) {
			$path = t3lib_div::getFileAbsFileName($path);
		}
		if (NULL === $this->type) {
			$this->setType(pathinfo($path, PATHINFO_EXTENSION));
		}
		if (NULL === $this->name) {
			$this->setName(pathinfo($path, PATHINFO_FILENAME));
		}
		$this->path = $path;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getNamedChunks() {
		return $this->namedChunks;
	}

	/**
	 * @param boolean $namedChunks
	 * @return Tx_Vhs_Asset
	 */
	public function setNamedChunks($namedChunks) {
		$this->namedChunks = $namedChunks;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getFluid() {
		return $this->fluid;
	}

	/**
	 * @param boolean $fluid
	 * @return Tx_Vhs_Asset
	 */
	public function setFluid($fluid) {
		$this->fluid = $fluid;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getVariables() {
		return $this->variables;
	}

	/**
	 * @param array $variables
	 * @return Tx_Vhs_Asset
	 */
	public function setVariables($variables) {
		$this->variables = $variables;
		return $this;
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
			if (TRUE === $settingsExist) {
				self::$settingsCache = t3lib_div::removeDotsFromTS($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			}
		}
		$settings = (array) self::$settingsCache;
		$properties = get_class_vars(get_class($this));
		foreach (array_keys($properties) as $propertyName) {
			$properties[$propertyName] = $this->$propertyName;
		}
		$settings = t3lib_div::array_merge_recursive_overrule($settings, $this->settings);
		$settings = t3lib_div::array_merge_recursive_overrule($settings, $properties);
		return $settings;
	}

	/**
	 * @param array $settings
	 * @return Tx_Vhs_Assets
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAssetSettings() {
		return $this->getSettings();
	}

	/**
	 * @return boolean
	 */
	public function getMovable() {
		return $this->movable;
	}

	/**
	 * @param boolean $movable
	 * @return $this
	 */
	public function setMovable($movable) {
		$this->movable = $movable;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getRemoved() {
		return $this->removed;
	}

	/**
	 * @param boolean $removed
	 * @return Tx_Vhs_Asset
	 */
	public function setRemoved($removed) {
		$this->removed = $removed;
		return $this;
	}

	/**
	 * Allows public access to debug this particular Asset
	 * instance later, when including the Asset in the page.
	 *
	 * @return array
	 */
	public function getDebugInformation() {
		return $this->getSettings();
	}

	/**
	 * Returns TRUE of settings specify that the source of this
	 * Asset should be rendered as if it were a Fluid template,
	 * using variables from the "arguments" attribute
	 *
	 * @return boolean
	 */
	public function assertFluidEnabled() {
		return $this->getFluid();
	}

	/**
	 * Returns TRUE if settings specify that the name of each Asset
	 * should be placed above the built content when placed in merged
	 * Asset cache files.
	 *
	 * @return boolean
	 */
	public function assertAddNameCommentWithChunk() {
		return $this->getNamedChunks();
	}

	/**
	 * Returns TRUE if the current Asset should be debugged as commanded
	 * by settings in TypoScript an/ord ViewHelper attributes.
	 *
	 * @return boolean
	 */
	public function assertDebugEnabled() {
		$settings = $this->getSettings();
		$enabled = (TRUE === isset($settings['debug']) ? (boolean) $settings['debug'] : FALSE);
		return $enabled;
	}

	/**
	 * @return boolean
	 */
	public function assertAllowedInFooter() {
		return $this->getMovable();
	}

	/**
	 * @return boolean
	 */
	public function assertHasBeenRemoved() {
		return $this->getRemoved();
	}

}
