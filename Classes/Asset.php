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
 * Asset Handling API
 *
 * Shortcut methods for using the AssetService, in case
 * this is preferred over injecting an instance.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage Service
 */
class Tx_Vhs_Asset  {

	/**
	 * @var null
	 */
	private static $objectManager = NULL;

	/**
	 * @return Tx_Vhs_Asset
	 */
	public static function getInstance() {
		if (NULL === self::$objectManager) {
			self::$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		}
		return self::$objectManager->get('Tx_Vhs_Service_AssetService');
	}

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @param boolean $cached If TRUE, treats this inclusion as happening in a cached context
	 * @return void
	 */
	public static function buildAll(array $parameters, $caller, $cached = TRUE) {
		self::getInstance()->buildAll($parameters, $caller, $cached);
	}

	/**
	 * @param array $parameters
	 * @param object $caller
	 * @return void
	 */
	public static function buildAllUncached(array $parameters, $caller) {
		self::getInstance()->buildAllUncached($parameters, $caller);
	}

}
