<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Asset;
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
 * ### Basic Style ViewHelper
 *
 * Allows inserting a `<link>` or `<style>` Asset. Settings
 * specify where to insert the Asset and how to treat it.
 *
 * @package Vhs
 * @subpackage ViewHelpers\Asset
 */
class StyleViewHelper extends AbstractAssetViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->overrideArgument('allowMoveToFooter', 'boolean', 'If TRUE, allows this Asset to be included in the document footer rather than the header. Should never be allowed for CSS.', FALSE, FALSE);
	}

	/**
	 * @var string
	 */
	protected $type = 'css';

}
