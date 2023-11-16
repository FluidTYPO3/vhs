<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ### PregReplace regular expression ViewHelper
 *
 * Implementation of `preg_replace` for Fluid.
 */
class PregReplaceViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;
    use TemplateVariableViewHelperTrait;

    public function initializeArguments(): void
    {
        $this->registerArgument('subject', 'string', 'String to match with the regex pattern or patterns');
        $this->registerArgument('pattern', 'string', 'Regex pattern to match against', true);
        $this->registerArgument('replacement', 'string', 'String to replace matches with', true);
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
        /** @var string $pattern */
        $pattern = $arguments['pattern'];
        /** @var string $replacement */
        $replacement = $arguments['replacement'];

        $subject = isset($as)
            ? $arguments['subject']
            : ($arguments['subject'] ?? $renderChildrenClosure());

        $value = preg_replace($pattern, $replacement, $subject);
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $value,
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
