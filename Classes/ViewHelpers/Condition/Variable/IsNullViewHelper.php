<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Value is NULL
 *
 * Condition ViewHelper which renders the `then` child if provided
 * value is NULL.
 */
class IsNullViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('value', 'string', 'value to check', true);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext)
    {
        return null === $arguments['value'];
    }
}
