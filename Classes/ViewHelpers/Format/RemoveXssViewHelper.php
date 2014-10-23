<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Removes XSS from $string
 *
 * Class RemoveXssViewHelper
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class RemoveXssViewHelper extends AbstractViewHelper {

	/**
	 * Removes XSS from string
	 *
	 * @param string $string
	 * @return string
	 */
	public function render($string = NULL) {
		if (NULL === $string) {
			$string = $this->renderChildren();
		}
		return GeneralUtility::removeXSS($string);
	}

}
