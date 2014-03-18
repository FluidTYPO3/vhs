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
 * ### Path: Relative Extension Resource Path
 *
 * Site Relative path to Extension Resources/Public folder.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Extension\Path
 */
class Tx_Vhs_ViewHelpers_Extension_Path_ResourcesViewHelper extends Tx_Vhs_ViewHelpers_Extension_AbstractExtensionViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('path', 'string', 'Optional path to append after output of \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$extensionKey = $this->getExtensionKey();
		$path = TRUE === empty($this->arguments['path']) ? '' : $this->arguments['path'];
		return \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extensionKey) . 'Resources/Public/' . $path;
	}

}
