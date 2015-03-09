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
 * ### Variable\Register: Set
 *
 * Sets a single register in the TSFE-register.
 *
 * Using as `{value -> v:variable.register.set(name: 'myVar')}` makes $GLOBALS["TSFE"]->register['myVar']
 * contain `{value}`.
 *
 * @author Stefan Neufeind <info (at) speedpartner.de>
 * @package Vhs
 * @subpackage ViewHelpers\Var
 */
class SetViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('value', 'mixed', 'Value to set', FALSE, NULL);
		$this->registerArgument('name', 'string', 'Name of register', TRUE);
	}

	/**
	 * Set (override) the value in register $name.
	 */
	public function render() {
		if (FALSE === $GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
			return NULL;
		}
		$name = $this->arguments['name'];
		$value = $this->arguments['value'];
		if (NULL === $value) {
			$value = $this->renderChildren();
		}
		$GLOBALS['TSFE']->register[$name] = $value;
		return NULL;
	}

}
