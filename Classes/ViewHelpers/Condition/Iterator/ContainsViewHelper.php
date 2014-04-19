<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator;

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
 * Condition ViewHelper. Renders the then-child if Iterator/array
 * haystack contains needle value.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Iterator
 */
class ContainsViewHelper extends AbstractConditionViewHelper {

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
		if (0 > $index) {
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
	 * @return boolean|integer
	 */
	protected function assertHaystackHasNeedle($haystack, $needle) {
		if (TRUE === is_array($haystack)) {
			return $this->assertHaystackIsArrayAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof ObjectStorage) {
			return $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof LazyObjectStorage) {
			return $this->assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof QueryResult) {
			return $this->assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
		} elseif (TRUE === is_string($haystack)) {
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
		if (TRUE === $needle instanceof DomainObjectInterface) {
			/** @var $needle DomainObjectInterface */
			$needle = $needle->getUid();
		}
		foreach ($haystack as $index => $candidate) {
			/** @var $candidate DomainObjectInterface */
			if ((integer) $candidate->getUid() === (integer) $needle) {
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
		/** @var $candidate DomainObjectInterface */
		if (TRUE === $needle instanceof AbstractDomainObject) {
			$needle = $needle->getUid();
		}
		foreach ($haystack as $candidate) {
			if ((integer) $candidate->getUid() === (integer) $needle) {
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
		if (FALSE === $needle instanceof DomainObjectInterface) {
			if (TRUE === (boolean) $this->arguments['considerKeys']) {
				$result = (boolean) (FALSE !== array_search($needle, $haystack) || TRUE === isset($haystack[$needle]));
			} else {
				$result = array_search($needle, $haystack);
			}
			return $result;
		} else {
			/** @var $needle DomainObjectInterface */
			foreach ($haystack as $index => $straw) {
				/** @var $straw DomainObjectInterface */
				if ((integer) $straw->getUid() === (integer) $needle->getUid()) {
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
