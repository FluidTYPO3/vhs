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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the first element of $haystack
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class FirstViewHelper extends AbstractViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @throws \Exception
	 * @return mixed|NULL
	 */
	public function render() {
		$haystack = $this->arguments['haystack'];
		if (NULL === $haystack) {
			$haystack = $this->renderChildren();
		}
		if (FALSE === is_array($haystack) && FALSE === $haystack instanceof \Iterator && FALSE === is_null($haystack)) {
			throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception('Invalid argument supplied to Iterator/FirstViewHelper - expected array, Iterator or NULL but got ' .
				gettype($haystack), 1351958398);
		}
		if (NULL === $haystack) {
			return NULL;
		}
		foreach ($haystack as $needle) {
			return $needle;
		}
		return NULL;
	}

}
