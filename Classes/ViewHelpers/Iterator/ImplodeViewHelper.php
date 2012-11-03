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
 * Implode ViewHelper
 *
 * Implodes an array or array-convertible object by $glue
 *
 * @author Claus Due, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ImplodeViewHelper extends Tx_Fluid_ViewHelpers_ExplodeViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
	}

	/**
	 * Render method
	 *
	 * @param mixed $content String or variable convertible to string which should be exploded
	 * @return mixed
	 */
	public function render($content = NULL) {
		if (!$content) {
			$content = $this->renderChildren();
		}
		$glue = $this->resolveGlue();
		$output = implode($glue, $content);
		if ($this->arguments['as']) {
			if ($this->templateVariableContainer->exists($this->arguments['as'])) {
				$this->templateVariableContainer->remove($this->arguments['as']);
			}
			$this->templateVariableContainer->add($this->arguments['as'], $output);
			return NULL;
		} else {
			return $output;
		}
	}

}
