<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Variable\Register: Set
 *
 * Sets a single register in the TSFE-register.
 *
 * Using as `{value -> v:variable.register.set(name: 'myVar')}` makes $GLOBALS["TSFE"]->register['myVar']
 * contain `{value}`.
 */
class SetViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'Value to set');
        $this->registerArgument('name', 'string', 'Name of register', true);
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        if (!$GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            return null;
        }
        $GLOBALS['TSFE']->register[$arguments['name']] = $renderChildrenClosure();
        return null;
    }
}
