<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Condition ViewHelper. Renders the then-child if Iterator/array
 * haystack contains needle value.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ContainsViewHelper extends Tx_Vhs_ViewHelpers_Iterator_AbstractIteratorViewHelper {

	/**
	 * @var mixed
	 */
	protected $evaluation = FALSE;

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('needle', 'mixed', 'Needle to search for in haystack', TRUE);
		$this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', TRUE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$haystack = $this->arguments['haystack'];
		$needle = $this->arguments['needle'];

		if (is_array($haystack)) {
			$this->evaluation = $this->assertHaystackIsArrayAndHasNeedle($haystack, $needle);
		} else if (is_string($haystack)) {
			$this->evaluation = $this->assertHaystackIsStringAndHasNeedle($haystack, $needle);
		} else if ($haystack instanceof Tx_Extbase_Persistence_QueryResultInterface) {
			$this->evaluation = $this->assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
		} else if ($haystack instanceof Tx_Extbase_Persistence_ObjectStorage) {
			$this->evaluation = $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} else if ($haystack instanceof Tx_Extbase_Persistence_LazyObjectStorage) {
			$this->evaluation = $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} else {
			$this->evaluation = FALSE;
		}

		if ($this->evaluation !== FALSE) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}


}
