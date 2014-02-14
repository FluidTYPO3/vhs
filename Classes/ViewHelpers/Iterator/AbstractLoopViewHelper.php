<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * Abstract class with basic functionality for loop view helpers.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
abstract class Tx_Vhs_ViewHelpers_Iterator_AbstractLoopViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('iteration', 'string', 'Variable name to insert result into, suppresses output', FALSE, NULL);
	}

	/**
	 * @param integer $i
	 * @param integer $from
	 * @param integer $to
	 * @param integer $step
	 * @param string $iterationArgument
	 * @return string
	 */
	protected function renderIteration($i, $from, $to, $step, $iterationArgument) {
		if (FALSE === empty($iterationArgument)) {
			$cycle = intval(($i - $from) / $step);
			$iteration = array(
				'index' => $i,
				'cycle' => $cycle + 1,
				'isOdd' => (0 === $cycle % 2 ? TRUE : FALSE),
				'isEven' => (0 === $cycle % 2 ? FALSE : TRUE),
				'isFirst' => ($i === $from ? TRUE : FALSE),
				'isLast' => $this->isLast($i, $from, $to, $step)
			);
			$this->templateVariableContainer->add($iterationArgument, $iteration);
			$content = $this->renderChildren();
			$this->templateVariableContainer->remove($iterationArgument);
		} else {
			$content = $this->renderChildren();
		}

		return $content;
	}

	/**
	 * @param integer $i
	 * @param integer $from
	 * @param integer $to
	 * @param integer $step
	 * @return boolean
	 */
	protected function isLast($i, $from, $to, $step) {
		if ($from === $to) {
			$isLast = TRUE;
		} elseif ($from < $to) {
			$isLast = ($i + $step > $to);
		} else {
			$isLast = ($i + $step < $to);
		}

		return $isLast;
	}

}