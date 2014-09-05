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
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Provides injected services and methods for easier implementation in
 * subclassing ViewHelpers
 *
 * @package Vhs
 * @subpackage ViewHelpers\Iterator
 */
abstract class AbstractIteratorViewHelper extends AbstractConditionViewHelper {

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
		if (TRUE === is_array($haystack)) {
			$asArray = $haystack;
		} elseif (TRUE === $haystack instanceof ObjectStorage) {
			/** @var $haystack ObjectStorage */
			$asArray = $haystack->toArray();
		} elseif (TRUE === $haystack instanceof LazyObjectStorage) {
			/** @var $haystack LazyObjectStorage */
			$asArray = $haystack->toArray();
		} elseif (TRUE === $haystack instanceof QueryResult) {
			/** @var $haystack QueryResult */
			$asArray = $haystack->toArray();
		} elseif (TRUE === is_string($haystack)) {
			$asArray = str_split($haystack);
		}
		return (TRUE === isset($asArray[$index]) ? $asArray[$index] : FALSE);
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackHasNeedle($haystack, $needle) {
		if (TRUE === is_array($haystack)) {
			return FALSE !== $this->assertHaystackIsArrayAndHasNeedle($haystack, $needle);
		} elseif (TRUE === $haystack instanceof ObjectStorage) {
			return FALSE !== $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif (TRUE === $haystack instanceof LazyObjectStorage) {
			return FALSE !== $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif (TRUE === $haystack instanceof QueryResult) {
			return FALSE !== $this->assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
		} elseif (TRUE === is_string($haystack)) {
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
		if (TRUE === $needle instanceof DomainObjectInterface) {
			/** @var $needle DomainObjectInterface */
			$needle = $needle->getUid();
		}
		foreach ($haystack as $index => $candidate) {
			/** @var $candidate DomainObjectInterface */
			if ($candidate->getUid() === $needle) {
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
		/** @var $candidate DomainObjectInterface */
		if (TRUE === $needle instanceof AbstractDomainObject) {
			$needle = $needle->getUid();
		}
		foreach ($haystack as $candidate) {
			if ($candidate->getUid() === $needle) {
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
		if ($needle instanceof DomainObjectInterface === FALSE) {
			if (TRUE === isset($this->arguments['considerKeys']) && TRUE === $this->arguments['considerKeys']) {
				$result = (boolean) (array_search($needle, $haystack) || isset($haystack[$needle]));
			} else {
				$result = (boolean) array_search($needle, $haystack);
			}
			return $result;
		} else {
			/** @var $needle DomainObjectInterface */
			foreach ($haystack as $index => $straw) {
				/** @var $straw DomainObjectInterface */
				if ($straw->getUid() === $needle->getUid()) {
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
