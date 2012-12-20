<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Andreas Lappe <nd@kaeufli.ch>, kaeufli.ch
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Loop through the iterator and extract a key, join the results
 * if more than one value is found.
 *
 * @author Andreas Lappe <nd@kaeufli.ch>
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
class Tx_Vhs_ViewHelpers_Iterator_ExtractViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param array $content
	 * @param string $key
	 * @param string $glue
	 */
	public function render($content = NULL, $key = NULL, $glue=' ') {
		if ($content === NULL ) {
			$content = $this->renderChildren();
		}
		if ($key === NULL) {
			$key = $this->arguments('key');
		}

		$result = $this->recursivelyExtractKey($content, $key, $glue);

		return $result;
	}

	/**
	 * Recursively extract the key
	 *
	 * @param array $iterator
	 * @param string $key
	 * @param string $glue
	 * @return string
	 */
	public function recursivelyExtractKey($iterator, $key, $glue) {
		$content = array();

		foreach ($iterator as $k => $v) {
			if (is_array($v)) {
				$content[] = $this->recursivelyExtractKey($v, $key, $glue);
			} else {
				if ($k === $key) {
					$content[] = $v;
				}
			}
		}

		return implode($glue, $content);
	}
}
?>