<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ### PregReplace regular expression ViewHeloer
 *
 * Implementation of `preg_replace` for Fluid.
 */
class PregReplaceViewHelper extends AbstractViewHelper
{

    use CompileWithRenderStatic;
    use TemplateVariableViewHelperTrait;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'string', 'String to match with the regex pattern or patterns');
        $this->registerArgument('pattern', 'string', 'Regex pattern to match against', true);
        $this->registerArgument('replacement', 'string', 'String to replace matches with', true);
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
        $subject = $arguments['subject'] ?? $renderChildrenClosure();
        $value = preg_replace($arguments['pattern'], $arguments['replacement'], $subject);
        return static::renderChildrenWithVariableOrReturnInputStatic(
            $value,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
