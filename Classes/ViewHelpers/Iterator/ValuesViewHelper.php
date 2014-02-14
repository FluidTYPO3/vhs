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
 * Gets values from an iterator, removing current keys (if any exist)
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ValuesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $subject
	 * @throws Exception
	 * @return array
	 */
	public function render($subject = NULL) {
		$as = $this->arguments['as'];
		if ($subject === NULL) {
			$subject = $this->renderChildren();
		}
		if ($subject instanceof Iterator) {
			$subject = iterator_to_array($subject, TRUE);
		} elseif (is_array($subject) !== TRUE) {
			throw new Exception('Cannot get values of unsupported type: ' . gettype($subject), 1357098192);
		}
		$output = array_values($subject);
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
