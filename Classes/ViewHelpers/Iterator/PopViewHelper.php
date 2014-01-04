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
 * Pops the last value off $subject (but does not change $subject itself as array_pop would)
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_PopViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $subject The subject Traversable/Array instance to pop
	 * @param string $as If specified, inserts a template variable with this name, then renders the child content, then removes the variable
	 * @throws Exception
	 * @return array
	 */
	public function render($subject = NULL, $as = NULL) {
		if ($subject === NULL) {
			$subject = $this->renderChildren();
		}
		if ($subject instanceof Traversable) {
			$subject = iterator_to_array($subject, TRUE);
		} elseif (is_array($subject) !== TRUE) {
			throw new Exception('Cannot get values of unsupported type: ' . gettype($subject), 1357098192);
		}
		$output = array_pop($subject);
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
