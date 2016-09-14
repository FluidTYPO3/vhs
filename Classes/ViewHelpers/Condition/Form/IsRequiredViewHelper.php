<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ### Is Field Required ViewHelper (condition)
 *
 * Takes a property (dotted path supported) and renders the
 * then-child if the property at the given path has an
 * @validate NotEmpty annotation.
 */
class IsRequiredViewHelper extends HasValidatorViewHelper
{

    /**
     * Default implementation for use in compiled templates
     *
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
        $arguments['validatorName'] = 'NotEmpty';
        return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
    }
}
