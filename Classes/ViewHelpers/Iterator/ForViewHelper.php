<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * Repeats rendering of children with a typical for loop: starting at
 * index $from it will loop until the index has reached $to.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ForViewHelper extends Tx_Vhs_ViewHelpers_Iterator_AbstractLoopViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('to', 'integer', 'Number that the index needs to reach before stopping', TRUE);
		$this->registerArgument('from', 'integer', 'Starting number for the index', FALSE, 0);
		$this->registerArgument('step', 'integer', 'Stepping number that the index is increased by after each loop', FALSE, 1);
	}

	/**
	 * @throws RuntimeException
	 * @return string
	 */
	public function render() {
		$to = intval($this->arguments['to']);
		$from = intval($this->arguments['from']);
		$step = intval($this->arguments['step']);
		$iteration = $this->arguments['iteration'];
		$content = '';

		if (0 === $step) {
			throw new RuntimeException('"step" may not be 0.', 1383267698);
		}
		if ($from < $to && 0 > $step) {
			throw new RuntimeException('"step" must be greater than 0 if "from" is smaller than "to".', 1383268407);
		}
		if ($from > $to && 0 < $step) {
			throw new RuntimeException('"step" must be smaller than 0 if "from" is greater than "to".', 1383268415);
		}

		if (TRUE === $this->templateVariableContainer->exists($iteration)) {
			$backupVariable = $this->templateVariableContainer->get($iteration);
			$this->templateVariableContainer->remove($iteration);
		}

		if ($from === $to) {
			$content = $this->renderIteration($from, $from, $to, $step, $iteration);
		} elseif ($from < $to) {
			for ($i = $from; $i <= $to; $i += $step) {
				$content .= $this->renderIteration($i, $from, $to, $step, $iteration);
			}
		} else {
			for ($i = $from; $i >= $to; $i += $step) {
				$content .= $this->renderIteration($i, $from, $to, $step, $iteration);
			}
		}

		if (TRUE === isset($backupVariable)) {
			$this->templateVariableContainer->add($iteration, $backupVariable);
		}

		return $content;
	}

}
