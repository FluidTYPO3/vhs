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
 * Returns the first element of $haystack
 *
 * @author Claus Due, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_FirstViewHelper extends Tx_Vhs_ViewHelpers_If_Iterator_ContainsViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws Exception
	 * @return mixed|NULL
	 */
	public function render() {
		$haystack = $this->arguments['haystack'];
		if (NULL === $haystack) {
			$haystack = $this->renderChildren();
		}
		if (is_array($haystack) === FALSE && $haystack instanceof Iterator === FALSE && is_null($haystack) === FALSE) {
			throw new Exception('Invalid argument supplied to Iterator/FirstViewHelper - expected array, Iterator or NULL but got ' .
				gettype($haystack), 1351958398);
		}
		if (NULL === $haystack) {
			return NULL;
		}
		foreach ($haystack as $needle) {
			return $needle;
		}
		return NULL;
	}

}
