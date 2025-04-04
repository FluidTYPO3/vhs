<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Converts a string to an array with $length number of bytes
 * per new array element. Wrapper for PHP's `str_split`.
 */
class SplitViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;
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
        $this->registerArgument('subject', 'string', 'The string that will be split into an array');
        $this->registerArgument('length', 'integer', 'Number of bytes per chunk in the new array', false, 1);
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
        /** @var int<1, max> $length */
        $length = $arguments['length'];
        if ((integer) $length === 0) {
            // Difference from PHP str_split: return an empty array if (potentially dynamically defined) length
            // argument is zero for some reason. PHP would throw a warning; Fluid would logically just return empty.
            return [];
        }
        /** @var string|null $as */
        $as = $arguments['as'];
        return static::renderChildrenWithVariableOrReturnInputStatic(
            str_split(
                empty($as) ? ($arguments['subject'] ?? $renderChildrenClosure()) : $arguments['subject'],
                $length
            ),
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
