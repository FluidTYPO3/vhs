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
 * Returns random element from array.
 */
class RandomViewHelper extends AbstractViewHelper
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
        $this->registerArgument(
            'subject',
            'mixed',
            'The subject Traversable/Array instance from which to select a random element'
        );
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
        $subject = static::arrayFromArrayOrTraversableOrCSVStatic(
            empty($as) ? ($arguments['subject'] ?? $renderChildrenClosure()) : $arguments['subject']
        );
        if (empty($subject)) {
            return null;
        }
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $subject[array_rand($subject)],
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
