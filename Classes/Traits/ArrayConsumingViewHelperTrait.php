<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Class ArrayConsumingViewHelperTrait
 *
 * Trait implemented by ViewHelpers that operate with
 * arrays, ArrayAccess, Iterator etc. instances.
 *
 * Contains the following main responsibilities:
 *
 * - retrieving an argument either from arguments or from
 *   tag contents while also converting it to array.
 * - merge arrays with a switch to respect TYPO3 version.
 */
trait ArrayConsumingViewHelperTrait {

	/**
	 * Override of VhsViewHelperTrait equivalent. Does what
	 * that function does, but also ensures an array return.
	 *
	 * @param string $argumentName
	 * @return mixed
	 */
	protected function getArgumentFromArgumentsOrTagContentAndConvertToArray($argumentName) {
		if (FALSE === $this->hasArgument($argumentName)) {
			$value = $this->renderChildren();
		} else {
			$value = $this->arguments[$argumentName];
		}
		return $this->arrayFromArrayOrTraversableOrCSV($value);
	}

	/**
	 * @param mixed $candidate
	 * @param boolean $useKeys
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function arrayFromArrayOrTraversableOrCSV($candidate, $useKeys = TRUE) {
		if (TRUE === $candidate instanceof \Traversable) {
			return iterator_to_array($candidate, $useKeys);
		} elseif (TRUE === $candidate instanceof QueryResultInterface) {
			/** @var QueryResultInterface $candidate */
			return $candidate->toArray();
		}
		if (TRUE === is_string($candidate)) {
			return GeneralUtility::trimExplode(',', $candidate, TRUE);
		} elseif (TRUE === is_array($candidate)) {
			return $candidate;
		}
		throw new Exception('Unsupported input type; cannot convert to array!');
	}

	/**
	 * @param $array1
	 * @param $array2
	 * @return array
	 */
	protected function mergeArrays($array1, $array2) {
		if (6.2 <= (float) substr(TYPO3_version, 0, 3)) {
			ArrayUtility::mergeRecursiveWithOverrule($array1, $array2);
			return $array1;
		} else {
			return GeneralUtility::array_merge_recursive_overrule($array1, $array2);
		}
	}

}
