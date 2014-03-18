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
 * Base class for ViewHelpers capable of compiling Assets,
 * aka. AssetCompilers. Contains a few base methods to handle
 * CompilableAssets - but there are
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Asset
 */
abstract class Tx_Vhs_ViewHelpers_Asset_Compilable_AbstractAssetCompilerViewHelper
	extends Tx_Vhs_ViewHelpers_Asset_AbstractAssetViewHelper
	implements Tx_Vhs_ViewHelpers_Asset_Compilable_AssetCompilerInterface {

	/**
	 * @var array
	 */
	protected $assets = array();

	/**
	 * @param Tx_Vhs_ViewHelpers_Asset_AssetInterface $asset
	 * @return void
	 */
	public function addAsset(Tx_Vhs_ViewHelpers_Asset_AssetInterface $asset) {
		$name = $asset->getName();
		$this->assets[$name] = $asset;
	}

	/**
	 * @return Tx_Vhs_ViewHelpers_Asset_AssetInterface[]
	 */
	public function getAssets() {
		return $this->assets;
	}

	/**
	 * @return string
	 */
	public function build() {
		return implode("\n", $this->assets);
	}

}
