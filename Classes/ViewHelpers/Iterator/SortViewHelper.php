<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Sorts an instance of ObjectStorage, an Iterator implementation,
 * an Array or a QueryResult (including Lazy counterparts).
 *
 * Can be used inline, i.e.:
 *
 * ```
 * <f:for each="{dataset -> v:iterator.sort(sortBy: 'name')}" as="item">
 *     // iterating data which is ONLY sorted while rendering this particular loop
 * </f:for>
 * ```
 */
class SortViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;
    use ArrayConsumingViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Contains all flags that are allowed to be used
     * with the sorting functions
     *
     * @var array
     */
    protected static $allowedSortFlags = [
        'SORT_REGULAR',
        'SORT_STRING',
        'SORT_NUMERIC',
        'SORT_NATURAL',
        'SORT_LOCALE_STRING',
        'SORT_FLAG_CASE'
    ];

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'mixed', 'The array/Traversable instance to sort');
        $this->registerArgument(
            'sortBy',
            'string',
            'Which property/field to sort by - leave out for numeric sorting based on indexes(keys)'
        );
        $this->registerArgument(
            'order',
            'string',
            'ASC, DESC, RAND or SHUFFLE. RAND preserves keys, SHUFFLE does not - but SHUFFLE is faster',
            false,
            'ASC'
        );
        $this->registerArgument(
            'sortFlags',
            'string',
            'Constant name from PHP for `SORT_FLAGS`: `SORT_REGULAR`, `SORT_STRING`, `SORT_NUMERIC`, ' .
            '`SORT_NATURAL`, `SORT_LOCALE_STRING` or `SORT_FLAG_CASE`. You can provide a comma seperated list or ' .
            'array to use a combination of flags.',
            false,
            'SORT_REGULAR'
        );
        $this->registerAsArgument();
    }

    /**
     * "Render" method - sorts a target list-type target. Either $array or
     * $objectStorage must be specified. If both are, ObjectStorage takes precedence.
     *
     * Returns the same type as $subject. Ignores NULL values which would be
     * OK to use in an f:for (empty loop as result)
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $candidate = !empty($arguments['as']) ? $arguments['subject'] : $renderChildrenClosure();
        if ($candidate instanceof ObjectStorage) {
            $sorted = static::sortObjectStorage($candidate, $arguments);
        } else {
            $subject = static::arrayFromArrayOrTraversableOrCSVStatic($candidate);
            $sorted = static::sortArray($subject, $arguments);
        }

        return static::renderChildrenWithVariableOrReturnInputStatic(
            $sorted,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }

    /**
     * Sort an array
     *
     * @param array|\Iterator $array
     * @param array $arguments
     * @return array
     */
    protected static function sortArray($array, $arguments)
    {
        $sorted = [];
        foreach ($array as $index => $object) {
            if (true === isset($arguments['sortBy'])) {
                $index = static::getSortValue($object, $arguments);
            }
            while (isset($sorted[$index])) {
                $index .= '.1';
            }
            $sorted[$index] = $object;
        }
        if ('ASC' === $arguments['order']) {
            ksort($sorted, static::getSortFlags($arguments));
        } elseif ('RAND' === $arguments['order']) {
            $sortedKeys = array_keys($sorted);
            shuffle($sortedKeys);
            $backup = $sorted;
            $sorted = [];
            foreach ($sortedKeys as $sortedKey) {
                $sorted[$sortedKey] = $backup[$sortedKey];
            }
        } elseif ('SHUFFLE' === $arguments['order']) {
            shuffle($sorted);
        } else {
            krsort($sorted, static::getSortFlags($arguments));
        }
        return $sorted;
    }

    /**
     * Sort an ObjectStorage instance
     *
     * @param ObjectStorage $storage
     * @param array $arguments
     * @return ObjectStorage
     */
    protected static function sortObjectStorage($storage, $arguments)
    {
        /** @var ObjectStorage $temp */
        $temp = GeneralUtility::makeInstance(ObjectStorage::class);
        foreach ($storage as $item) {
            $temp->attach($item);
        }
        $sorted = static::sortArray($storage, $arguments);
        /** @var ObjectStorage $storage */
        $storage = GeneralUtility::makeInstance(ObjectStorage::class);
        foreach ($sorted as $item) {
            $storage->attach($item);
        }
        return $storage;
    }

    /**
     * Gets the value to use as sorting value from $object
     *
     * @param mixed $object
     * @param array $arguments
     * @return mixed
     */
    protected static function getSortValue($object, $arguments)
    {
        $field = $arguments['sortBy'];
        $value = ObjectAccess::getPropertyPath($object, $field);
        if (true === $value instanceof \DateTimeInterface) {
            $value = (integer) $value->format('U');
        } elseif (true === $value instanceof ObjectStorage || true === $value instanceof LazyObjectStorage) {
            $value = $value->count();
        } elseif (is_array($value)) {
            $value = count($value);
        }
        return $value;
    }

    /**
     * Parses the supplied flags into the proper value for the sorting
     * function.
     *
     * @param array $arguments
     * @return int
     * @throws Exception
     */
    protected static function getSortFlags($arguments)
    {
        $constants = static::arrayFromArrayOrTraversableOrCSVStatic($arguments['sortFlags']);
        $flags = 0;
        foreach ($constants as $constant) {
            if (false === in_array($constant, static::$allowedSortFlags)) {
                ErrorUtility::throwViewHelperException(
                    'The constant "' . $constant . '" you\'re trying to use as a sortFlag is not allowed. Allowed ' .
                    'constants are: ' . implode(', ', static::$allowedSortFlags) . '.',
                    1404220538
                );
            }
            $flags = $flags | constant(trim($constant));
        }
        return $flags;
    }
}
