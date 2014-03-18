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
 * Base class for Assets which must be compiled by an
 * AssetCompiler.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Asset
 */
abstract class Tx_Vhs_ViewHelpers_Asset_Compilable_AbstractCompilableAssetViewHelper
	extends Tx_Vhs_ViewHelpers_Asset_AbstractAssetViewHelper
	implements Tx_Vhs_ViewHelpers_Asset_Compilable_CompilableAssetInterface {

	/**
	 * Target compiler class name. Override this with
	 * a class or interface name in order to connect
	 * your custom CompilableAsset implementation to a
	 * specific AssetCompiler class (which must be
	 * registered in the template as well).
	 *
	 * @var string
	 */
	protected $compilerClassName = NULL;

	/**
	 * @return string
	 */
	public function getCompilerClassName() {
		return $this->compilerClassName;
	}

}
