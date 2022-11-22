<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Content\AbstractContentViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper used to render raw content records typically fetched
 * with `<v:content.get(column: '0', render: FALSE) />`.
 *
 * If you simply want to render a content element, try `<v:content.render>`.
 */
class RecordViewHelper extends AbstractContentViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('record', 'array', 'Record to render');
    }

    /**
     * @return string|null
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        if (false === isset($arguments['record']['uid'])) {
            return null;
        }
        return static::renderRecord($arguments['record']);
    }
}
