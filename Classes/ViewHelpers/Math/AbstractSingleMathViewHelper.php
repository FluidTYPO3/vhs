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
 * Base class: Math ViewHelpers operating on one number or an
 * array of numbers.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers
 */
abstract class Tx_Vhs_ViewHelpers_Math_AbstractSingleMathViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param mixed $subject
	 * @return boolean
	 */
	protected function assertIsArrayOrIterator($subject) {
		return (is_array($subject) === TRUE || $subject instanceof Iterator === TRUE);
	}

	/**
	 * @param $subject
	 * @return boolean
	 */
	protected function assertSupportsArrayAccess($subject) {
		return (is_array($subject) === TRUE || ($subject instanceof Iterator === TRUE && $subject instanceof ArrayAccess === TRUE));
	}

}