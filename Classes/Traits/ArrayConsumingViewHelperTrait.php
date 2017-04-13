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
trait ArrayConsumingViewHelperTrait
{

    /**
     * Override of VhsViewHelperTrait equivalent. Does what
     * that function does, but also ensures an array return.
     *
     * @param string $argumentName
     * @return mixed
     */
    protected function getArgumentFromArgumentsOrTagContentAndConvertToArray($argumentName)
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
     *
     * @param array $arguments
     * @param string $argumentName
     * @param \Closure $renderChildrenClosure
     *
     * @return mixed
     */
    protected static function getArgumentFromArgumentsOrTagContentAndConvertToArrayStatic(
        array $arguments,
        $argumentName,
        \Closure $renderChildrenClosure
    ) {
        if (!isset($arguments[$argumentName])) {
            $value = $renderChildrenClosure();
        } else {
            $value = $arguments[$argumentName];
        }
        return static::arrayFromArrayOrTraversableOrCSVStatic($value);
    }

    /**
     * @param mixed $candidate
     * @param boolean $useKeys
     *
     * @return array
     * @throws Exception
     */
    protected function arrayFromArrayOrTraversableOrCSV($candidate, $useKeys = true)
    {
        return static::arrayFromArrayOrTraversableOrCSVStatic($candidate, $useKeys);
    }

    /**
     * @param mixed $candidate
     * @param boolean $useKeys
     *
     * @return array
     * @throws Exception
     */
    protected static function arrayFromArrayOrTraversableOrCSVStatic($candidate, $useKeys = true)
    {
        if (true === $candidate instanceof \Traversable) {
            return iterator_to_array($candidate, $useKeys);
        } elseif (true === $candidate instanceof QueryResultInterface) {
            /** @var QueryResultInterface $candidate */
            return $candidate->toArray();
        }
        if (true === is_string($candidate)) {
            return GeneralUtility::trimExplode(',', $candidate, true);
        } elseif (true === is_array($candidate)) {
            return $candidate;
        }
        ErrorUtility::throwViewHelperException('Unsupported input type; cannot convert to array!');
    }

    /**
     * @param $array1
     * @param $array2
     * @return array
     */
    protected function mergeArrays($array1, $array2)
    {
        return static::mergeArraysStatic($array1, $array2);
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected static function mergeArraysStatic($array1, $array2)
    {
        ArrayUtility::mergeRecursiveWithOverrule($array1, $array2);
        return $array1;
    }

    /**
     * @param mixed $subject
     * @return boolean
     */
    protected static function assertIsArrayOrIterator($subject)
    {
        return (boolean) (true === is_array($subject) || true === $subject instanceof \Traversable);
    }
}
