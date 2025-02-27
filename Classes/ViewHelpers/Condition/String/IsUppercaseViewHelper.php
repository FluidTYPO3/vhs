<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\String;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: String is lowercase
 *
 * Condition ViewHelper which renders the `then` child if provided
 * string is uppercase. By default only the first letter is tested.
 * To test the full string set $fullString to TRUE.
 */
class IsUppercaseViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', 'string to check', true);
        $this->registerArgument('fullString', 'string', 'need', false, false);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $fullStrinng = (bool) $arguments['fullString'];
        /** @var string $string */
        $string = $arguments['string'];
        if ($arguments['fullString']) {
            $result = ctype_upper((string) $string);
        } else {
            $result = ctype_upper(substr((string) $string, 0, 1));
        }
        return $result;
    }
}
