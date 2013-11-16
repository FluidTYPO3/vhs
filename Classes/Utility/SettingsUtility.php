<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Erik Frister <erik.frister@aijko.de>, aijko
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
 * Settings Utility
 *
 * Utility for handling settings
 *
 * @author Erik Frister <erik.frister@aijko.de>, aijko
 * @package Vhs
 * @subpackage Utility
 */
class Tx_Vhs_Utility_SettingsUtility {

	/**
	 * Removes the dots from a settings array, with the option to keep the dots for specific properties
	 *
	 * @param array $settings TypoScript Settings with dots
	 * @param array $keepDotsForProperties array of properties to keep dots for
	 * @return array
	 */
	public static function removeDotsFromSettings($settings, $keepDotsForProperties) {
		$out = array();
		foreach ($settings as $key => $value) {
			if (is_array($value)) {
				$key = rtrim($key, '.');
				if (!in_array($key, $keepDotsForProperties)) {
					$out[$key] = self::removeDotsFromSettings($value, $keepDotsForProperties);
				} else {
					$out[$key] = $value;
				}
			} else {
				$out[$key] = $value;
			}
		}
		return $out;
	}

}