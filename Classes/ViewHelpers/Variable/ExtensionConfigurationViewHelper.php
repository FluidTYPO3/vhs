<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### ExtConf ViewHelper
 *
 * Reads settings from ext_conf_template.txt
 *
 * ### Examples
 *
 * {v:variable.extensionConfiguration(name:'foo',extensionKey:'bar')}
 *
 * Returns setting 'foo' from extension 'bar' located in ext_conf_template.txt
 *
 * @author Harry Glatz <glatz@analog.de>
 * @author Jochen Greiner <greiner@analog.de>
 * @author Cedric Ziel <cedric@cedric-ziel.com>
 * @package Vhs
 * @subpackage ViewHelpers
 */
class ExtensionConfigurationViewHelper extends AbstractViewHelper {

	/**
	 * caches unserialized config in runtime
	 * @var array
	 */
	protected static $EXT_CONF = array();

	/**
	 * @param string $name
	 * @param string $extensionKey
	 * @return string|array|NULL
	 */
	public function render($name = NULL, $extensionKey = NULL) {

		if (NULL === $extensionKey) {
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
			$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
		}
		if (FALSE === array_key_exists($extensionKey, $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'])) {
			return NULL;
		}

		if (empty(self::$EXT_CONF[$extensionKey])) {
			self::$EXT_CONF[$extensionKey] = GeneralUtility::removeDotsFromTS(unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]));
		}
		if (!$name) {
			return self::$EXT_CONF[$extensionKey];
		}

		if (TRUE === array_key_exists($name, self::$EXT_CONF[$extensionKey])) {
			$res = self::$EXT_CONF[$extensionKey][$name];
			if (is_array($res)) {
				return $res;
			}
			return trim($res);
		} else {
			return NULL;
		}
	}
}
