<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Utility\VariableRegister;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ### Variable\Register: Set
 *
 * Sets a single register, readable across the current rendering with `v:variable.register.get`.
 *
 * Using as `{value -> v:variable.register.set(name: 'myVar')}` makes `myVar`
 * contain `{value}`. Mirrored into $GLOBALS["TSFE"]->register when TSFE is available
 * (TYPO3 <14) for backwards compatibility with code reading that array directly.
 */
class SetViewHelper extends AbstractViewHelper
{
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
        /** @var mixed $value */
        $value = $arguments['value'] ?? $renderChildrenClosure();
        VariableRegister::set($arguments['name'], $value);
        if (($GLOBALS['TSFE'] ?? null) instanceof TypoScriptFrontendController) {
            $GLOBALS['TSFE']->register[$arguments['name']] = $value;
        }
        return null;
    }
}
