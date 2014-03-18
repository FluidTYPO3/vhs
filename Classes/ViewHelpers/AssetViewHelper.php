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
 * ### Basic Asset ViewHelper
 *
 * Places the contents of the asset (the tag body) directly
 * in the additional header content of the page. This most
 * basic possible version of an Asset has only the core
 * features shared by every Asset type:
 *
 * - a "name" attribute which is required, identifying the Asset
 *   by a lowerCamelCase or lowercase_underscored value, your
 *   preference (but lowerCamelCase recommended for consistency).
 * - a "dependencies" attribute with a CSV list of other named
 *   Assets upon which the current Asset depends. When used, this
 *   Asset will be included after every asset listed as dependency.
 * - a "group" attribute which is optional and is used ty further
 *   identify the Asset as belonging to a particular group which
 *   can be suppressed or manipulated through TypoScript. For
 *   example, the default value is "fluid" and if TypoScript is
 *   used to exclude the group "fluid" then any Asset in that
 *   group will simply not be loaded.
 * - an "overwrite" attribute which if enabled causes any existing
 *   asset with the same name to be overwritten with the current
 *   Asset instead. If rendered in a loop only the last instance
 *   is actually used (this allows Assets in Partials which are
 *   rendered in an f:for loop).
 * - a "debug" property which enables output of the information
 *   used by the current Asset, with an option to force debug
 *   mode through TypoScript.
 * - additional properties which affect how the Asset is processed.
 *   For a full list see the argument descriptions; the same
 *   settings can be applied through TypoScript per-Asset, globally
 *   or per-Asset-group.
 *
 * > Note: there are no static TypoScript templates for VHS but
 * > you will find a complete list in the README.md file in the
 * > root of the extension folder.
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
class Tx_Vhs_ViewHelpers_AssetViewHelper extends Tx_Vhs_ViewHelpers_Asset_AbstractAssetViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->overrideArgument('standalone', 'boolean', 'If TRUE, excludes this Asset from any concatenation which may be applied', FALSE, TRUE);
	}

}
