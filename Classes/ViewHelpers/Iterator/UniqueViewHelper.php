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
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Iterator Unique Values ViewHelper
 *
 * Implementation of `array_unique` for Fluid
 *
 * Accepts an input array of values and returns/assigns
 * a new array containing only the unique values found
 * in the input array.
 *
 * Note that the ViewHelper does not support the sorting
 * parameter - if you wish to sort the result you should
 * use `v:iterator.sort` in a chain.
 *
 * #### Usage examples
 *
 * ```xml
 * <!--
 * Given a (large) array of every user's country with possible duplicates.
 * The idea being to output only a unique list of countries' names.
 * -->
 *
 * Countries of our users: {userCountries -> v:iterator.unique() -> v:iterator.implode(glue: ' - ')}
 * ```
 *
 * Output:
 *
 * ```xml
 * Countries of our users: USA - USA - Denmark - Germany - Germany - USA - Denmark - Germany
 * ```
 *
 * ```xml
 * <!-- Given the same use case as above but also implementing sorting -->
 * Countries of our users, in alphabetical order:
 * {userCountries -> v:iterator.unique()
 *     -> v:iterator.sort(sortFlags: 'SORT_NATURAL')
 *     -> v:iterator.implode(glue: ' - ')}
 * ```
 *
 * Output:
 *
 * ```xml
 * Countries of our users: Denmark - Germany - USA
 * ```
 */
class UniqueViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;
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
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'mixed', 'The input array/Traversable to process');
        $this->registerAsArgument();
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
        $array = static::arrayFromArrayOrTraversableOrCSVStatic($renderChildrenClosure());
        $array = array_unique($array);
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $array,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
