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
 * Repeats rendering of children with a typical for loop: starting at index $from it will loop until the index has reached $to.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ForViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('to', 'integer', 'Number that the index needs to reach before stopping', TRUE);
		$this->registerArgument('from', 'integer', 'Starting number for the index', FALSE, 0);
		$this->registerArgument('step', 'integer', 'Stepping number that the index is increased by after each loop', FALSE, 1);
		$this->registerArgument('iteration', 'string', 'Variable name to insert result into, suppresses output', FALSE, NULL);
	}

	/**
	 * @return string
	 */
	public function render() {
		$max = $this->arguments['to'];
		$from = $this->arguments['from'];
		$step = $this->arguments['step'];
		$content = '';

		if (TRUE === $this->templateVariableContainer->exists($this->arguments['iteration'])) {
			$backupVariable = $this->templateVariableContainer->get($this->arguments['iteration']);
		}

		for ($i = $from; $i <= $max; $i += $step) {
			if (FALSE === empty($this->arguments['iteration'])) {
				$iteration = array(
					'cycle' => $i + 1,
					'index' => $i,
					'isOdd' => ($i % 2 == 0 ? 1 : 0),
					'isEven' => $i % 2,
					'isFirst' => ($i === $from ? 1 : 0),
					'isLast' => ($i === $max ? 1 : 0)
				);
				if (TRUE === $this->templateVariableContainer->exists($this->arguments['iteration'])) {
					$this->templateVariableContainer->remove($this->arguments['iteration']);
				}
				$this->templateVariableContainer->add($this->arguments['iteration'], $iteration);
				$content .= $this->renderChildren() . LF;
				$this->templateVariableContainer->remove($this->arguments['iteration']);
			} else {
				$content .= $this->renderChildren() . LF;
			}
		}

		if (TRUE === isset($backupVariable)) {
			$this->templateVariableContainer->add($this->arguments['iteration'], $backupVariable);
		}

		return $content;
	}

}