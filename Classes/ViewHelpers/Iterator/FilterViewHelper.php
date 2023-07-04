<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Iterator: Filter ViewHelper
 *
 * Filters an array by filtering the array, analysing each member
 * and asserting if it is equal to (weak type) the `filter` parameter.
 * If `propertyName` is set, the ViewHelper will try to extract this
 * property from each member of the array.
 *
 * Iterators and ObjectStorage etc. are supported.
 */
class FilterViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('subject', 'mixed', 'The subject iterator/array to be filtered');
        $this->registerArgument('filter', 'mixed', 'The comparison value');
        $this->registerArgument(
            'propertyName',
            'string',
            'Optional property name to extract and use for comparison instead of the object; use on ObjectStorage ' .
            'etc. Note: supports dot-path expressions'
        );
        $this->registerArgument(
            'preserveKeys',
            'boolean',
            'If TRUE, keys in the array are preserved - even if they are numeric',
            false,
            false
        );
        $this->registerArgument('invert', 'boolean', 'Invert the behavior of the filtering', false, false);
        $this->registerArgument(
            'nullFilter',
            'boolean',
            'If TRUE and $filter is NULL (not set) includes only NULL values. Useful with $invert.',
            false,
            false
        );
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var array|iterable $subject */
        $subject = $arguments['subject'] ?? $renderChildrenClosure();
        $filter = $arguments['filter'];
        /** @var string $propertyName */
        $propertyName = $arguments['propertyName'];
        $preserveKeys = (boolean) $arguments['preserveKeys'];
        $invert = (boolean) $arguments['invert'];
        $nullFilter = (boolean) $arguments['nullFilter'];

        if (!is_array($subject) && !$subject instanceof \Traversable) {
            return [];
        }
        if ((!$nullFilter && null === $filter) || '' === $filter) {
            return $subject;
        }
        if ($subject instanceof \Traversable) {
            $subject = iterator_to_array($subject);
        }
        $items = [];
        $invertFlag = !$invert;
        foreach ($subject as $key => $item) {
            if ($invertFlag === static::filter($item, $filter, $propertyName)) {
                $items[$key] = $item;
            }
        }
        return $preserveKeys ? $items : array_values($items);
    }

    /**
     * Filter an item/value according to desired filter. Returns TRUE if
     * the item should be included, FALSE otherwise. This default method
     * simply does a weak comparison (==) for sameness.
     *
     * @param mixed $item
     * @param mixed $filter Could be a single value or an Array. If so the function returns TRUE when
     *                      $item matches with any value in it.
     */
    protected static function filter($item, $filter, ?string $propertyName): bool
    {
        if (!empty($propertyName) && (is_object($item) || is_array($item))) {
            $value = ObjectAccess::getPropertyPath($item, $propertyName);
        } else {
            $value = $item;
        }
        return is_array($filter) ? in_array($value, $filter) : ($value == $filter);
    }
}
