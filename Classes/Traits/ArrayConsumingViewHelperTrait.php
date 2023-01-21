<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

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
trait ArrayConsumingViewHelperTrait
{
    /**
     * Override of VhsViewHelperTrait equivalent. Does what
     * that function does, but also ensures an array return.
     */
    protected function getArgumentFromArgumentsOrTagContentAndConvertToArray(string $argumentName): array
    {
        return static::getArgumentFromArgumentsOrTagContentAndConvertToArrayStatic(
            $this->arguments,
            $argumentName,
            $this->buildRenderChildrenClosure()
        );
    }

    /**
     * Override of VhsViewHelperTrait equivalent. Does what
     * that function does, but also ensures an array return.
     */
    protected static function getArgumentFromArgumentsOrTagContentAndConvertToArrayStatic(
        array $arguments,
        string $argumentName,
        \Closure $renderChildrenClosure
    ): array {
        if (!isset($arguments[$argumentName])) {
            $value = $renderChildrenClosure();
        } else {
            $value = $arguments[$argumentName];
        }
        return static::arrayFromArrayOrTraversableOrCSVStatic($value);
    }

    /**
     * @param mixed $candidate
     */
    protected static function arrayFromArrayOrTraversableOrCSVStatic($candidate, bool $useKeys = true): array
    {
        if ($candidate instanceof QueryResultInterface) {
            return $candidate->toArray();
        }
        if (is_array($candidate)) {
            return $candidate;
        }
        if ($candidate instanceof \Traversable) {
            return iterator_to_array($candidate, $useKeys);
        }
        if (is_string($candidate)) {
            return GeneralUtility::trimExplode(',', $candidate, true);
        }
        ErrorUtility::throwViewHelperException('Unsupported input type; cannot convert to array!');
        return [];
    }

    protected function mergeArrays(array $array1, array $array2): array
    {
        return static::mergeArraysStatic($array1, $array2);
    }

    protected static function mergeArraysStatic(array $array1, array $array2): array
    {
        ArrayUtility::mergeRecursiveWithOverrule($array1, $array2);
        return $array1;
    }

    /**
     * @param mixed $subject
     */
    protected static function assertIsArrayOrIterator($subject): bool
    {
        return is_array($subject) || $subject instanceof \Traversable;
    }
}
