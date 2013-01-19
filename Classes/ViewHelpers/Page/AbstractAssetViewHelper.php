<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
abstract class Tx_Vhs_ViewHelpers_Page_AbstractAssetViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

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
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('content', 'string', 'Content to relocate', FALSE, NULL);
		$this->registerArgument('name', 'string', 'Optional name of the content. If multiple occurrences of the same name happens, behavior is defined by argument "overwrite"');
		$this->registerArgument('overwrite', 'boolean', 'If set to FALSE and a relocated string with "name" already exists, does not overwrite the existing relocated string. Default behavior is to overwrite.', FALSE, TRUE);
		$this->registerArgument('dependencies', 'string', 'CSV list of other named assets upon which this asset depends. When included, this asset will always load after its dependencies');
		$this->registerArgument('group', 'string', 'Optional name of a logical group (created dynamically just by using the name) to which this particular asset belongs.', FALSE, 'fluid');
		$this->registerArgument('debug', 'boolean', 'If TRUE, outputs information about this ViewHelper when the tag is used. Two master debug switches exist in TypoScript; see documentation about Page / Asset ViewHelper');
	}

	/**
	 * @return array
	 */
	public function getDependencies() {
		return t3lib_div::trimExplode(',', $this->arguments['dependencies'], TRUE);
	}

	/**
	 * @return boolean
	 */
	protected function getOverwrite() {
		return (boolean) $this->arguments['overwrite'];
	}

	/**
	 * @return string
	 */
	protected function getName() {
		if (TRUE === isset($this->arguments['name'])) {
			$name = $this->arguments['name'];
		} else {
			$content = $this->getContent();
			$name = md5($content);
		}
		return $name;
	}

	/**
	 * @return string
	 */
	protected function getContent() {
		if (FALSE === isset($this->arguments['content'])) {
			$content = $this->renderChildren();
		} else {
			$content = $this->arguments['content'];
		}
		return $content;
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
			$allTypoScript = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
			$settingsExist = isset($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			if (FALSE === $settingsExist) {
					// no settings exist, but don't allow a NULL value. This prevents cache clobbering.
				self::$settingsCache = array();
			} else {
				self::$settingsCache = $this->dotSuffixArrayToPlainArray($allTypoScript['plugin.']['tx_vhs.']['settings.']);
			}
		}
		return self::$settingsCache;
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
			'arguments' => $this->arguments,
			'settings' => $this->getSettings(),
			'content' => $this->getContent()
		);
	}

	/**
	 * Returns TRUE if the current Asset should be debugged as commanded
	 * by settings in TypoScript an/ord ViewHelper attributes.
	 *
	 * @return boolean
	 */
	public function assertDebugEnabled() {
		$settings = $this->getSettings();
		return isset($settings['each']['debug']) && $settings['each']['debug'] > 0 && $this->arguments['debug'] > 0;
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
		return NULL;
	}

	/**
	 * @return mixed
	 */
	protected function debug() {
		$settings = $this->getSettings();
		$debugOutputEnabled = $this->assertDebugEnabled();
		$useDebugUtility = isset($settings['useDebugUtility']) && $settings['useDebugUtility'] > 0;
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
	 * @return mixed
	 */
	protected function bypass() {
		if ($this->arguments['debug']) {
			return $this->debug();
		}
		return NULL;
	}

	/**
	 * @param array $array
	 * @return array
	 */
	protected function dotSuffixArrayToPlainArray($array) {
		$dotFreeArray = array();
		foreach ($array as $key => $value) {
			if (substr($key, -1) === '.') {
				$key = substr($key, -1);
			}
			if (TRUE === is_array($value)) {
				$value = $this->dotSuffixArrayToPlainArray($value);
			}
			$dotFreeArray[$key] = $value;
		}
		return $dotFreeArray;
	}

}
