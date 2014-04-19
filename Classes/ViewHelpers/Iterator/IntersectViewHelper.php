<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

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
use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Intersects arrays/Traversables $a and $b into an array
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class IntersectViewHelper extends AbstractViewHelper {

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

		$a = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($a);
		$b = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($this->arguments['b']);

		return array_intersect($a, $b);
	}

}
