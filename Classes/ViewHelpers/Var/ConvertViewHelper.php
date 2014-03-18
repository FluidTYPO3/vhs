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
 ***************************************************************/

/**
 * ### Convert ViewHelper
 *
 * Converts $value to $type which can be one of 'string', 'integer',
 * 'float', 'boolean', 'array' or 'ObjectStorage'. If $value is NULL
 * sensible defaults are assigned or $default which obviously has to
 * be of $type as well.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class Tx_Vhs_ViewHelpers_Var_ConvertViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('value', 'mixed', 'Value to convert into a different type', FALSE, NULL);
		$this->registerArgument('type', 'string', 'Data type to convert the value into. Can be one of "string", "integer", "float", "boolean", "array" or "ObjectStorage".', TRUE);
		$this->registerArgument('default', 'mixed', 'Optional default value to assign to the converted variable in case it is NULL.', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws Exception
	 * @return mixed
	 */
	public function render() {
		if (TRUE === isset($this->arguments['value'])) {
			$value = $this->arguments['value'];
		} else {
			$value = $this->renderChildren();
		}
		$type = $this->arguments['type'];
		if (gettype($value) === $type) {
			return $value;
		}
		if (NULL !== $value) {
			if ('ObjectStorage' === $type && 'array' === gettype($value)) {
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
				$storage = $objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
				foreach ($value as $item) {
					$storage->attach($item);
				}
				$value = $storage;
			} elseif ('array' === $type && TRUE === $value instanceof Traversable) {
				$value = iterator_to_array($value, FALSE);
			} elseif ('array' === $type) {
				$value = array($value);
			} else {
				settype($value, $type);
			}
		} else {
			if (TRUE === isset($this->arguments['default'])) {
				$default = $this->arguments['default'];
				if (gettype($default) !== $type) {
					throw new RuntimeException('Supplied argument "default" is not of the type "' . $type .'"', 1364542576);
				}
				$value = $default;
			} else {
				switch ($type) {
					case 'string':
						$value = '';
						break;
					case 'integer':
						$value = 0;
						break;
					case 'boolean':
						$value = FALSE;
						break;
					case 'float':
						$value = 0.0;
						break;
					case 'array':
						$value = array();
						break;
					case 'ObjectStorage':
						$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
						$value = $objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
						break;
					default:
						throw new RuntimeException('Provided argument "type" is not valid', 1364542884);
				}
			}
		}
		return $value;
	}
}
