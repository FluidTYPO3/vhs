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
 * Gets keys from an iterator.
 */
class KeysViewHelper extends AbstractViewHelper
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
        $this->registerArgument('subject', 'mixed', 'Input to work on - Array/Traversable/...');
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
        /** @var string|null $as */
        $as = $arguments['as'];
        return static::renderChildrenWithVariableOrReturnInputStatic(
            array_keys(
                static::arrayFromArrayOrTraversableOrCSVStatic(
                    empty($as)
                        ? ($arguments['subject'] ?? $renderChildrenClosure())
                        : $arguments['subject']
                )
            ),
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
