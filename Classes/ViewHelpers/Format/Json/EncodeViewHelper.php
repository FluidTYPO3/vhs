<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Returns a string containing the JSON representation of the argument
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Format\Json
 */
class Tx_Vhs_ViewHelpers_Format_Json_EncodeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param mixed $value Array or Traversable
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 * @return string
	 */
	public function render($value = NULL, $useTraversableKeys = FALSE) {
		if (NULL === $value) {
			$value = $this->renderChildren();
			if (NULL === $value) {
				return '{}';
			}
		}

		if ($value instanceof Traversable) {
			$value = iterator_to_array($value, $useTraversableKeys);
		}

		$json = json_encode($value);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('The provided argument cannot be converted into JSON.', 1358440181); 
		}

		return $json;
	}
}