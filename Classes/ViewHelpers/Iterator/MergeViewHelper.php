<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;
use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Merges arrays/Traversables $a and $b into an array.
 */
class MergeViewHelper extends AbstractViewHelper
{
    use ArrayConsumingViewHelperTrait;
    use CompileWithRenderStatic;

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
        $this->registerArgument(
            'a',
            'mixed',
            'First array/Traversable - if not set, the ViewHelper can be in a chain (inline-notation)'
        );
        $this->registerArgument('b', 'mixed', 'Second array or Traversable');
        $this->registerArgument(
            'useKeys',
            'boolean',
            'If TRUE comparison is done while also observing and merging the keys used in each array',
            false,
            false
        );
    }

    /**
     * @return array
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $a = static::arrayFromArrayOrTraversableOrCSVStatic($arguments['a'] ?? $renderChildrenClosure());
        $b = static::arrayFromArrayOrTraversableOrCSVStatic($arguments['b'], (bool) $arguments['useKeys']);
        return static::mergeArraysStatic($a, $b);
    }
}
