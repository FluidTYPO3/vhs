<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\DefaultRenderMethodViewHelperTrait;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Hides output from browser, but still renders tag content
 * which means any ViewHelper inside the tag content still
 * gets processed.
 */
class HideViewHelper extends AbstractViewHelper
{

    use DefaultRenderMethodViewHelperTrait;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'disabled',
            'boolean',
            'If TRUE, renders content - use to quickly enable/disable Fluid code',
            false,
            false
        );
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
        $content = $renderChildrenClosure();
        if ($arguments['disabled']) {
            return $content;
        }
        return null;
    }
}
