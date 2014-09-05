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
use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Slice an Iterator by $start and $length
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class SliceViewHelper extends AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $haystack
	 * @param integer $start
	 * @param integer $length
	 * @param string $as
	 * @throws \Exception
	 * @return array
	 */
	public function render($haystack = NULL, $start = 0, $length = NULL, $as = NULL) {
		if (NULL === $haystack) {
			$haystack = $this->renderChildren();
		}
		if (TRUE === $haystack instanceof \Iterator) {
			$haystack = iterator_to_array($haystack, TRUE);
		} elseif (FALSE === is_array($haystack)) {
			throw new \Exception('Cannot slice unsupported type: ' . gettype($haystack), 1353812601);
		}
		$output = array_slice($haystack, $start, $length, TRUE);
		if (NULL !== $as) {
			$variables = array($as => $output);
			$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		}
		return $output;
	}

}
