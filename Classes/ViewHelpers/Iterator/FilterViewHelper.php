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
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Iterator: Filter ViewHelper
 *
 * Filters an array by filtering the array, analysing each member
 * and assering if it is equal to (weak type) the `filter` parameter.
 * If `propertyName` is set, the ViewHelper will try to extract this
 * property from each member of the array.
 *
 * Iterators and ObjectStorage etc. are supported.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class FilterViewHelper extends AbstractViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $subject The subject iterator/array to be filtered
	 * @param mixed $filter The comparison value
	 * @param string $propertyName Optional property name to extract and use for comparison instead of the object; use on ObjectStorage etc. Note: supports dot-path expressions.
	 * @param boolean $preserveKeys If TRUE, keys in the array are preserved - even if they are numeric
	 * @return mixed
	 */
	public function render($subject = NULL, $filter = NULL, $propertyName = NULL, $preserveKeys = FALSE) {
		if (NULL === $subject) {
			$subject = $this->renderChildren();
		}
		if (NULL === $subject || (FALSE === is_array($subject) && FALSE === $subject instanceof \Traversable)) {
			return array();
		}
		if (TRUE === is_null($filter) || '' === $filter) {
			return $subject;
		}
		if (TRUE === $subject instanceof \Traversable) {
			$subject = iterator_to_array($subject);
		}
		$items = array();
		foreach ($subject as $key => $item) {
			if (TRUE === $this->filter($item, $filter, $propertyName)) {
				$items[$key] = $item;
			}
		}
		return TRUE === $preserveKeys ? $items : array_values($items);
	}

	/**
	 * Filter an item/value according to desired filter. Returns TRUE if
	 * the item should be included, FALSE otherwise. This default method
	 * simply does a weak comparison (==) for sameness.
	 *
	 * @param mixed $item
	 * @param mixed $filter
	 * @param string $propertyName
	 * @return boolean
	 */
	protected function filter($item, $filter, $propertyName) {
		if (FALSE === empty($propertyName) && (TRUE === is_object($item) || TRUE === is_array($item))) {
			$value = ObjectAccess::getPropertyPath($item, $propertyName);
		} else {
			$value = $item;
		}
		return ($value == $filter);
	}

}
