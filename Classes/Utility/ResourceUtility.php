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
 * ViewHelper Utility
 *
 * Contains helper methods used in resources ViewHelpers
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage Utility
 */
class Tx_Vhs_Utility_ResourceUtility {

	/**
	 * Fixes a bug in TYPO3 6.2.0 that the properties metadata is not overlayed on localization.
	 *
	 * @param \TYPO3\CMS\Core\Resource\File $file
	 * @return array
	 */
	public static function getFileArray(\TYPO3\CMS\Core\Resource\File $file) {
		$properties = $file->getProperties();
		$stat = $file->getStorage()->getFileInfo($file);
		$array = $file->toArray();

		foreach ($properties as $key => $value) {
			$array[$key] = $value;
		}
		foreach ($stat as $key => $value) {
			$array[$key] = $value;
		}

		return $array;
	}

}
