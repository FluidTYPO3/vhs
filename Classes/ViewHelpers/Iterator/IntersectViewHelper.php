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
 * Intersects arrays/Traversables $a and $b into an array.
 */
class IntersectViewHelper extends AbstractViewHelper
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
        parent::initializeArguments();

        $this->registerArgument('a', 'mixed', 'First Array/Traversable/CSV');
        $this->registerArgument('b', 'mixed', 'Second Array/Traversable/CSV', true);
    }

    /**
     * @return array
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $a = $arguments['a'] ?? $renderChildrenClosure();

        $a = static::arrayFromArrayOrTraversableOrCSVStatic($a);
        $b = static::arrayFromArrayOrTraversableOrCSVStatic($arguments['b']);

        return array_intersect($a, $b);
    }
}
