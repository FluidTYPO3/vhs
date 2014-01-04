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
 * Intersects arrays/Traversables $a and $b into an array
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_IntersectViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument('a', 'mixed', 'First Array/Traversable/CSV', FALSE, NULL);
		$this->registerArgument('b', 'mixed', 'Second Array/Traversable/CSV', TRUE);
	}

	/**
	 * @return array
	 */
	public function render() {
		$a = $this->arguments['a'];
		if (NULL === $a) {
			$a = $this->renderChildren();
		}

		$a = Tx_Vhs_Utility_ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($a);
		$b = Tx_Vhs_Utility_ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($this->arguments['b']);

		return array_intersect($a, $b);
	}

}
