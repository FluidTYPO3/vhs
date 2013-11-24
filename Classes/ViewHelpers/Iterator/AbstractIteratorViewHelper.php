<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Provides injected services and methods for easier implementation in
 * subclassing ViewHelpers
 *
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
abstract class Tx_Vhs_ViewHelpers_Iterator_AbstractIteratorViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

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
		} elseif ($haystack instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
			/** @var $haystack \TYPO3\CMS\Extbase\Persistence\ObjectStorage */
			$asArray = $haystack->toArray();
		} elseif ($haystack instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage) {
			/** @var $haystack Tx_Extbase_Persistence_LazyObjectStorage */
			$asArray = $haystack->toArray();
		} elseif ($haystack instanceof \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult) {
			/** @var $haystack \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult */
			$asArray = $haystack->toArray();
		} elseif (is_string($haystack)) {
			$asArray = str_split($haystack);
		}
		return isset($asArray[$index]) ? $asArray[$index] : FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackHasNeedle($haystack, $needle) {
		if (is_array($haystack)) {
			return FALSE !== $this->assertHaystackIsArrayAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
			return FALSE !== $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage) {
			return FALSE !== $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult) {
			return FALSE !== $this->assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
		} elseif (is_string($haystack)) {
			return FALSE !== strpos($haystack, $needle);
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return mixed
	 */
	protected function assertHaystackIsQueryResultAndHasNeedle($haystack, $needle) {
		if ($needle instanceof \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface) {
			/** @var $needle \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface */
			$needle = $needle->getUid();
		}
		foreach ($haystack as $index => $candidate) {
			/** @var $candidate \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface */
			if ($candidate->getUid() == $needle) {
				return $index;
			}
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return mixed
	 */
	protected function assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle) {
		$index = 0;
		/** @var $candidate \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface */
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
	 * @return boolean
	 */
	protected function assertHaystackIsArrayAndHasNeedle($haystack, $needle) {
		if ($needle instanceof \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface === FALSE) {
			if (isset($this->arguments['considerKeys']) && $this->arguments['considerKeys']) {
				$result = array_search($needle, $haystack) || isset($haystack[$needle]);
			} else {
				$result = array_search($needle, $haystack);
			}
			return $result;
		} else {
			/** @var $needle \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface */
			foreach ($haystack as $index => $straw) {
				/** @var $straw \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface */
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
	 * @return boolean
	 */
	protected function assertHaystackIsStringAndHasNeedle($haystack, $needle) {
		return strpos($haystack, $needle);
	}

}
