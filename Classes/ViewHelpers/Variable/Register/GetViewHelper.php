<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
		if (FALSE === $GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
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
