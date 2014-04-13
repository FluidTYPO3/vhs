<?php
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
 * Condition ViewHelper. Renders the then-child if Iterator/array
 * haystack contains needle value.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\If\Iterator
 */
class Tx_Vhs_ViewHelpers_If_Iterator_ContainsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * @var mixed
	 */
	protected $evaluation = FALSE;

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('needle', 'mixed', 'Needle to search for in haystack', TRUE);
		$this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', TRUE);
		$this->registerArgument('considerKeys', 'boolean', 'Tell whether to consider keys in the search assuming haystack is an array.', FALSE, FALSE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$haystack = $this->arguments['haystack'];
		$needle = $this->arguments['needle'];

		$this->evaluation = $this->assertHaystackHasNeedle($haystack, $needle);

		if (FALSE !== $this->evaluation) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

	/**
	 * @param integer $index
	 * @return mixed
	 */
	protected function getNeedleAtIndex($index) {
		if ($index < 0) {
			return NULL;
		}
		$haystack = $this->arguments['haystack'];
		$asArray = array();
		if (is_array($haystack)) {
			$asArray = $haystack;
		} elseif ($haystack instanceof Tx_Extbase_Persistence_ObjectStorage) {
			/** @var $haystack Tx_Extbase_Persistence_ObjectStorage */
			$asArray = $haystack->toArray();
		} elseif ($haystack instanceof Tx_Extbase_Persistence_LazyObjectStorage) {
			/** @var $haystack Tx_Extbase_Persistence_LazyObjectStorage */
			$asArray = $haystack->toArray();
		} elseif ($haystack instanceof Tx_Extbase_Persistence_QueryResult) {
			/** @var $haystack Tx_Extbase_Persistence_QueryResult */
			$asArray = $haystack->toArray();
		} elseif (is_string($haystack)) {
			$asArray = str_split($haystack);
		}
		return isset($asArray[$index]) ? $asArray[$index] : FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean|integer
	 */
	protected function assertHaystackHasNeedle($haystack, $needle) {
		if (is_array($haystack)) {
			return $this->assertHaystackIsArrayAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof Tx_Extbase_Persistence_ObjectStorage) {
			return $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof Tx_Extbase_Persistence_LazyObjectStorage) {
			return $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof Tx_Extbase_Persistence_QueryResult) {
			return $this->assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
		} elseif (is_string($haystack)) {
			return strpos($haystack, $needle);
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean|integer
	 */
	protected function assertHaystackIsQueryResultAndHasNeedle($haystack, $needle) {
		if ($needle instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
			/** @var $needle Tx_Extbase_DomainObject_DomainObjectInterface */
			$needle = $needle->getUid();
		}
		foreach ($haystack as $index => $candidate) {
			/** @var $candidate Tx_Extbase_DomainObject_DomainObjectInterface */
			if ($candidate->getUid() == $needle) {
				return $index;
			}
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean|integer
	 */
	protected function assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle) {
		$index = 0;
		/** @var $candidate Tx_Extbase_DomainObject_DomainObjectInterface */
		if ($needle instanceof Tx_Extbase_DomainObject_AbstractDomainObject) {
			$needle = $needle->getUid();
		}
		foreach ($haystack as $candidate) {
			if ($candidate->getUid() == $needle) {
				return $index;
			}
			$index++;
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean|integer
	 */
	protected function assertHaystackIsArrayAndHasNeedle($haystack, $needle) {
		if ($needle instanceof Tx_Extbase_DomainObject_DomainObjectInterface === FALSE) {
			if (isset($this->arguments['considerKeys']) && $this->arguments['considerKeys']) {
				$result = array_search($needle, $haystack) || isset($haystack[$needle]);
			} else {
				$result = array_search($needle, $haystack);
			}
			return $result;
		} else {
			/** @var $needle Tx_Extbase_DomainObject_DomainObjectInterface */
			foreach ($haystack as $index => $straw) {
				/** @var $straw Tx_Extbase_DomainObject_DomainObjectInterface */
				if ($straw->getUid() == $needle->getUid()) {
					return $index;
				}
			}
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean|integer
	 */
	protected function assertHaystackIsStringAndHasNeedle($haystack, $needle) {
		return strpos($haystack, $needle);
	}

}
