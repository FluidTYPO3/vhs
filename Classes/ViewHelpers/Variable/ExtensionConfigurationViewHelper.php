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
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * ### ExtConf ViewHelper
 *
 * Reads settings from ext_conf_template.txt
 *
 * ### Examples
 *
 * {v:variable.extensionConfiguration(extensionKey:'foo',path:'bar.baz')}
 *
 * Returns setting 'bar.baz' from extension 'foo' located in ext_conf_template.txt
 *
 * @author Harry Glatz <glatz@analog.de>
 * @author Cedric Ziel <cedric@cedric-ziel.com>
 * @author Stefan Neufeind <info@speedpartner.de>
 * @package Vhs
 * @subpackage ViewHelpers
 */
class ExtensionConfigurationViewHelper extends AbstractViewHelper {

	/**
	 * @param array $source TypoScript-array with dots: $source['foo.']['bar.']['baz']
	 * @param string $path
	 * @return mixed
	 */
	protected function extractFromArrayByPath($source, $path) {
		$result = $source;
		$pathParts = explode('.', $path);
		$pathParts = array_diff($pathParts, array(''));
		foreach ($pathParts as $part) {
			if (array_key_exists($part . '.', $result)) {
				$result = $result[$part . '.'];
			} elseif (array_key_exists($part, $result)) {
				$result = $result[$part];
			} else {
				return NULL;
			}
		}
		return $result;
	}

	/**
	 * @param string $path
	 * @param string $extensionKey
	 * @param string $name (deprecated, just use $path instead)
	 * @return string
	 * @throws Exception
	 */
	public function render($path = NULL, $extensionKey = NULL, $name = NULL) {
		if (NULL !== $path) {
			$pathToExtract = $path;
		} elseif (NULL !== $name) {
			$pathToExtract = $name;
			GeneralUtility::deprecationLog('v:variable.extensionConfiguration was called with parameter "name" which is deprecated. Use "path" instead.');
		} else {
			throw new Exception('v:variable.extensionConfiguration requires the "path" attribute to be filled.', 1446998437);
		}

		if (NULL === $extensionKey) {
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
			$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
		}

		if (FALSE === array_key_exists($extensionKey, $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'])) {
			return NULL;
		}

		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]);

		return $this->extractFromArrayByPath($extConf, $path);
	}
}
