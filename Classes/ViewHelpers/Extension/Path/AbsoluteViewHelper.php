<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension\Path;
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
use FluidTYPO3\Vhs\ViewHelpers\Extension\AbstractExtensionViewHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * ### Path: Absolute Extension Folder Path
 *
 * Returns the absolute path to an extension folder.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Extension\Path
 */
class AbsoluteViewHelper extends AbstractExtensionViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('path', 'string', 'Optional path to append, second argument when calling \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$extensionKey = $this->getExtensionKey();
		return ExtensionManagementUtility::extPath($extensionKey, TRUE === isset($this->arguments['path']) ? $this->arguments['path'] : NULL);
	}

}
