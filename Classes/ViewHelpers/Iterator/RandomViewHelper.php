<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Returns random element from array
 *
 * @author Björn Fromme, <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_RandomViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Which variable to update in the TemplateVariableContainer. If left out, returns the random element instead of updating the variable', FALSE);
	}

	/**
	 * Render method
	 *
	 * @param mixed $subject
	 * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
	 * @return mixed
	 */
	public function render($subject = NULL) {
		if (NULL === $subject && (FALSE === isset($as) || TRUE === empty($as))) {
			$subject = $this->renderChildren();
		}

		$as = $this->arguments['as'];
		$array = NULL;
		if (TRUE === is_array($subject)) {
			$array = $subject;
		} else {
			if (TRUE === ($subject instanceof Iterator)) {
				$array = iterator_to_array($subject, TRUE);
			} elseif (TRUE === ($subject instanceof \TYPO3\CMS\Extbase\Persistence\Generic\QueryResultInterface) || TRUE === ($subject instanceof TYPO3\CMS\Extbase\Persistence\QueryResultInterface)) {
				/** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResultInterface $subject */
				$array = $subject->toArray();
			} elseif (NULL !== $subject) {
				throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception('Invalid variable type passed to Iterator/RandomViewHelper. Expected any of Array, QueryResult, ' .
				' ObjectStorage or Iterator implementation but got ' . gettype($subject), 1370966821);
			}
		}
		$randomElement = $array[array_rand($array)];
		if (TRUE === isset($as) && FALSE === empty($as)) {
			$variables = array($as => $randomElement);
			$content = Tx_Vhs_Utility_ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
			return $content;
		}
		return $randomElement;
	}
}
