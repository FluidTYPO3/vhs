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
 * Gets values from an iterator, removing current keys (if any exist)
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ValuesViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $subject
	 * @param integer $start
	 * @param integer $length
	 * @return array
	 */
	public function render($subject = NULL, $start = 0, $length = NULL) {
		if ($subject === NULL) {
			$subject = $this->renderChildren();
		}
		if ($subject instanceof Iterator) {
			$subject = iterator_to_array($subject, TRUE);
		} elseif (is_array($subject) !== TRUE) {
			throw new Exception('Cannot get values of unsupported type: ' . gettype($subject), 1357098192);
		}
		$output = array_values($subject);
		if ($this->arguments['as']) {
			if ($this->templateVariableContainer->exists($this->arguments['as'])) {
				$this->templateVariableContainer->remove($this->arguments['as']);
			}
			$this->templateVariableContainer->add($this->arguments['as'], $output);
			$output = $this->renderChildren();
			$this->templateVariableContainer->remove($this->arguments['as']);
		}
		return $output;
	}

}
