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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Slice an Iterator by $start and $length.
 */
class SliceViewHelper extends AbstractViewHelper
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

    public function initializeArguments(): void
    {
        $this->registerArgument('haystack', 'mixed', 'The input array/Traversable to reverse');
        $this->registerArgument('start', 'integer', 'Starting offset', false, 0);
        $this->registerArgument('length', 'integer', 'Number of items to slice');
        $this->registerArgument('preserveKeys', 'boolean', 'Whether or not to preserve original keys', false, true);
        $this->registerAsArgument();
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var int $start */
        $start = $arguments['start'];
        /** @var int $length */
        $length = $arguments['length'];
        /** @var string|null $as */
        $as = $arguments['as'];
        $haystack = static::arrayFromArrayOrTraversableOrCSVStatic(
            empty($as) ? ($arguments['haystack'] ?? $renderChildrenClosure()) : $arguments['haystack']
        );
        $output = array_slice($haystack, $start, $length, (boolean) $arguments['preserveKeys']);
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $output,
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
