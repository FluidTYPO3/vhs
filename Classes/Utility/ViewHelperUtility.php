<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;

/**
 * ViewHelper Utility
 *
 * Contains compatibility methods used in ViewHelpers
 */
class ViewHelperUtility
{
    /**
     * Fixes a bug in TYPO3 6.2.0 that the properties metadata is not overlayed on localization.
     *
     * @param RenderingContextInterface $renderingContext
     * @return TemplateVariableContainer|\TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface
     */
    public static function getVariableProviderFromRenderingContext(RenderingContextInterface $renderingContext)
    {
        if (method_exists($renderingContext, 'getVariableProvider')) {
            return $renderingContext->getVariableProvider();
        }
        return $renderingContext->getTemplateVariableContainer();
    }
}
