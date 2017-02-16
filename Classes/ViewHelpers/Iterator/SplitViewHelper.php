<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Converts a string to an array with $length number of bytes
 * per new array element. Wrapper for PHP's `str_split`.
 */
class SplitViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;
    use TemplateVariableViewHelperTrait;

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
        $this->registerArgument('subject', 'string', 'The string that will be split into an array');
        $this->registerArgument('length', 'integer', 'Number of bytes per chunk in the new array', false, 1);
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
        if ((integer) $arguments['length'] === 0) {
            // Difference from PHP str_split: return an empty array if (potentially dynamically defined) length
            // argument is zero for some reason. PHP would throw a warning; Fluid would logically just return empty.
            return [];
        }
        return str_split($renderChildrenClosure(), $arguments['length']);
    }
}
