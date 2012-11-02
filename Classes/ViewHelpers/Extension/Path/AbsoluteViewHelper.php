<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Absolute path to Extension
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Extension\Path
 */
class Tx_Vhs_ViewHelpers_Extension_Path_AbsoluteViewHelper extends Tx_Vhs_ViewHelpers_Extension_AbstractExtensionViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('path', 'string', 'Optional path to append, second argument when calling t3libextMgm::extPath');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$extensionKey = $this->getExtensionKey();
		return t3lib_extMgm::extPath($extensionKey, isset($this->arguments['path']) === TRUE ? $this->arguments['path'] : NULL);
	}

}
