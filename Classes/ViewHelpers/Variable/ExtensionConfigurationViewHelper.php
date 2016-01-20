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
 * @author Jochen Greiner <greiner@analog.de>
 * @author Cedric Ziel <cedric@cedric-ziel.com>
 * @author Stefan Neufeind <info@speedpartner.de>
 * @package Vhs
 * @subpackage ViewHelpers
 */
class ExtensionConfigurationViewHelper extends AbstractViewHelper
{

	/**
	 * caches unserialized config in runtime
	 * @var array
	 */
	protected static $EXT_CONF = array();

	/**
	 * @param string $path
	 * @param string $extensionKey
	 * @param string $name (deprecated, just use $path instead)
	 * @return string|array|NULL
	 * @throws Exception
	 */
	public function render($path = null, $extensionKey = null, $name = null)
	{
		if (null !== $path) {
			$pathToExtract = $path;
		} elseif (null !== $name) {
			$pathToExtract = $name;
			GeneralUtility::deprecationLog('v:variable.extensionConfiguration was called with parameter "name" which is deprecated. Use "path" instead.');
		} else {
			throw new Exception('v:variable.extensionConfiguration requires the "path" attribute to be filled.',
				1446998437);
		}

		$cacheKey = $this->getCacheKey($pathToExtract, $extensionKey);

		if (array_key_exists($cacheKey, static::$EXT_CONF)) {
			return static::$EXT_CONF[$cacheKey];
		}

		if (null === $extensionKey) {
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
			$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
		}

		if (false === array_key_exists($extensionKey, $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'])) {
			return null;
		}

		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]);

		return static::$EXT_CONF[$cacheKey] = $this->extractFromArrayByPath($extConf, $pathToExtract);
	}

	/**
	 * Generates the cache key from a simple hash
	 *
	 * @param string $path
	 * @param string $extensionKey
	 * @return string
	 */
	protected function getCacheKey($path = null, $extensionKey = null)
	{
		return md5((string)$path . '-' . (string)$extensionKey);
	}

	/**
	 * @param array $source TypoScript-array with dots: $source['foo.']['bar.']['baz']
	 * @param string $path
	 * @return mixed
	 */
	protected function extractFromArrayByPath($source, $path)
	{
		$result = $source;
		$pathParts = explode('.', $path);
		$pathParts = array_diff($pathParts, array(''));
		foreach ($pathParts as $part) {
			if (array_key_exists($part . '.', $result)) {
				$result = $result[$part . '.'];
			} elseif (array_key_exists($part, $result)) {
				$result = $result[$part];
			} else {
				return null;
			}
		}
		return $result;
	}
}
