<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable\Register;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Stefan Neufeind <info (at) speedpartner.de>
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
 * ### Variable\Register: Get
 *
 * ViewHelper used to read the value of a TSFE-register
 * Can be used to read names of variables which contain dynamic parts:
 *
 *     <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
 *     {v:variable.register.get(name: 'dynamic{variableName}')}
 *
 * @author Stefan Neufeind <info (at) speedpartner.de>
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

class GetViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of register', TRUE);
	}

	/**
	 * @return string
	 */
	public function render() {
		if (FALSE === $GLOBALS['TSFE'] instanceof \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController) {
			return NULL;
		}
		$name = $this->arguments['name'];
		$value = NULL;
		if (TRUE === isset($GLOBALS['TSFE']->register[$name])) {
			$value = $GLOBALS['TSFE']->register[$name];
		}
		return $value;
	}

}
