<?php
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

/**
 * ### Iterator Reversal ViewHelper
 *
 * Reverses the order of every member of an Iterator/Array,
 * preserving the original keys.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ReverseViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Which variable to update in the TemplateVariableContainer. If left out, returns reversed data instead of updating the variable (i.e. reference or copy)');
	}

	/**
	 * "Render" method - sorts a target list-type target. Either $array or
	 * $objectStorage must be specified. If both are, ObjectStorage takes precedence.
	 *
	 * Returns the same type as $subject. Ignores NULL values which would be
	 * OK to use in an f:for (empty loop as result)
	 *
	 * @param array|Iterator $subject An array or Iterator implementation to sort
	 * @throws Exception
	 * @return mixed
	 */
	public function render($subject = NULL) {
		$as = $this->arguments['as'];
		if ($subject === NULL && !$as) {
				// this case enables inline usage if the "as" argument
				// is not provided. If "as" is provided, the tag content
				// (which is where inline arguments are taken from) is
				// expected to contain the rendering rather than the variable.
			$subject = $this->renderChildren();
		}
		$array = NULL;
		if (is_array($subject) === TRUE) {
			$array = $subject;
		} else {
			if ($subject instanceof Iterator) {
				/** @var Iterator $subject */
				$array = iterator_to_array($subject, TRUE);
			} elseif ($subject instanceof Tx_Extbase_Persistence_QueryResultInterface) {
				/** @var Tx_Extbase_Persistence_QueryResultInterface $subject */
				$array = $subject->toArray();
			} elseif ($subject !== NULL) {
					// a NULL value is respected and ignored, but any
					// unrecognized value other than this is considered a
					// fatal error.
				throw new Exception('Invalid variable type passed to Iterator/ReverseViewHelper. Expected any of Array, QueryResult, ' .
					' ObjectStorage or Iterator implementation but got ' . gettype($subject), 1351958941);
			}
		}
		$array = array_reverse($array, TRUE);
		if (NULL !== $as) {
			$variables = array($as => $array);
			$content = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
			return $content;
		}
		return $array;
	}

}
