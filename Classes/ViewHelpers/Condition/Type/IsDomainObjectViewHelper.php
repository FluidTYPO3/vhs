<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Type;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Value is a domain object
 *
 * Condition ViewHelper which renders the `then` child if provided
 * value is a domain object, i.e. it inherits from extbase's base
 * class.
 */
class IsDomainObjectViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('value', 'mixed', 'value to check', true);
        $this->registerArgument('fullString', 'string', 'need', false, false);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        return is_array($arguments) && $arguments['value'] instanceof AbstractDomainObject;
    }
}
