<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

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
 * Repeats rendering of children $count times while updating $iteration
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class LoopViewHelper extends AbstractLoopViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument('count', 'integer', 'Number of times to render child content', TRUE);
		$this->registerArgument('minimum', 'integer', 'Minimum number of loops before stopping', FALSE, 0);
		$this->registerArgument('maximum', 'integer', 'Maxiumum number of loops before stopping', FALSE, PHP_INT_MAX);
	}

	/**
	 * @return string
	 */
	public function render() {
		$count = intval($this->arguments['count']);
		$minimum = intval($this->arguments['minimum']);
		$maximum = intval($this->arguments['maximum']);
		$iteration = $this->arguments['iteration'];
		$content = '';

		if ($count < $minimum) {
			$count = $minimum;
		} elseif ($count > $maximum) {
			$count = $maximum;
		}

		if (TRUE === $this->templateVariableContainer->exists($iteration)) {
			$backupVariable = $this->templateVariableContainer->get($iteration);
			$this->templateVariableContainer->remove($iteration);
		}

		for ($i = 0; $i < $count; $i++) {
			$content .= $this->renderIteration($i, 0, $count, 1, $iteration);
		}

		if (TRUE === isset($backupVariable)) {
			$this->templateVariableContainer->add($iteration, $backupVariable);
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
		return ($i + $step >= $to);
	}

}
