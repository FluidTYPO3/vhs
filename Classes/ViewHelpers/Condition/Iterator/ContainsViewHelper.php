<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;

/**
 * Condition ViewHelper. Renders the then-child if Iterator/array
 * haystack contains needle value.
 *
 * ### Example:
 *
 *     {v:condition.iterator.contains(needle: 'foo', haystack: {0: 'foo'}, then: 'yes', else: 'no')}
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Condition\Iterator
 */
class ContainsViewHelper extends AbstractConditionViewHelper {

	use ConditionViewHelperTrait;

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('needle', 'mixed', 'Needle to search for in haystack', TRUE);
		$this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle', TRUE);
		$this->registerArgument('considerKeys', 'boolean', 'Tell whether to consider keys in the search assuming haystack is an array.', FALSE, FALSE);
	}

	/**
	 * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = NULL) {
		return FALSE !== self::assertHaystackHasNeedle($arguments['haystack'], $arguments['needle'], $arguments);;
	}

	/**
	 * @param integer $index
	 * @param array $arguments
	 * @return mixed
	 */
	static protected function getNeedleAtIndex($index, $arguments) {
		if (0 > $index) {
			return NULL;
		}
		$haystack = $arguments['haystack'];
		$asArray = array();
		if (TRUE === is_array($haystack)) {
			$asArray = $haystack;
		} elseif (TRUE === $haystack instanceof LazyObjectStorage) {
			/** @var $haystack LazyObjectStorage */
			$asArray = $haystack->toArray();
		} elseif (TRUE === $haystack instanceof ObjectStorage) {
			/** @var $haystack ObjectStorage */
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
	 * @param array $arguments
	 * @return boolean|integer
	 */
	static protected function assertHaystackHasNeedle($haystack, $needle, $arguments) {
		if (TRUE === is_array($haystack)) {
			return self::assertHaystackIsArrayAndHasNeedle($haystack, $needle, $arguments);
		} elseif ($haystack instanceof LazyObjectStorage) {
			return self::assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof ObjectStorage) {
			return self::assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle);
		} elseif ($haystack instanceof QueryResult) {
			return self::assertHaystackIsQueryResultAndHasNeedle($haystack, $needle);
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
	static protected function assertHaystackIsQueryResultAndHasNeedle($haystack, $needle) {
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
	static protected function assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle) {
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
	 * @param array $arguments
	 * @return boolean|integer
	 */
	static protected function assertHaystackIsArrayAndHasNeedle($haystack, $needle, $arguments) {
		if (FALSE === $needle instanceof DomainObjectInterface) {
			if (TRUE === (boolean) $arguments['considerKeys']) {
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
	static protected function assertHaystackIsStringAndHasNeedle($haystack, $needle) {
		return strpos($haystack, $needle);
	}

}
