<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Iterator: Filter ViewHelper
 *
 * Filters an array by filtering the array, analysing each member
 * and assering if it is equal to (weak type) the `filter` parameter.
 * If `propertyName` is set, the ViewHelper will try to extract this
 * property from each member of the array.
 *
 * Iterators and ObjectStorage etc. are supported.
 */
class FilterViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
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
        $subject = $renderChildrenClosure();
        $filter = $arguments['filter'];
        $propertyName = $arguments['propertyName'];
        $preserveKeys = (boolean) $arguments['preserveKeys'];
        $invert = (boolean) $arguments['invert'];
        $nullFilter = (boolean) $arguments['nullFilter'];

        if (null === $subject || (false === is_array($subject) && false === $subject instanceof \Traversable)) {
            return [];
        }
        if ((false === (boolean) $nullFilter && null === $filter) || '' === $filter) {
            return $subject;
        }
        if (true === $subject instanceof \Traversable) {
            $subject = iterator_to_array($subject);
        }
        $items = [];
        $invertFlag = !$invert;
        foreach ($subject as $key => $item) {
            if ($invertFlag === static::filter($item, $filter, $propertyName)) {
                $items[$key] = $item;
            }
        }
        return true === $preserveKeys ? $items : array_values($items);
    }

    /**
     * Filter an item/value according to desired filter. Returns TRUE if
     * the item should be included, FALSE otherwise. This default method
     * simply does a weak comparison (==) for sameness.
     *
     * @param mixed $item
     * @param mixed $filter Could be a single value or an Array. If so the function returns TRUE when
     *                      $item matches with any value in it.
     * @param string $propertyName
     * @return boolean
     */
    protected static function filter($item, $filter, $propertyName)
    {
        if (false === empty($propertyName) && (true === is_object($item) || true === is_array($item))) {
            $value = ObjectAccess::getPropertyPath($item, $propertyName);
        } else {
            $value = $item;
        }
        return is_array($filter) ? in_array($value, $filter) : ($value == $filter);
    }
}
