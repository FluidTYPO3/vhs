<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\String;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;

/**
 * ### Condition: String is lowercase
 *
 * Condition ViewHelper which renders the `then` child if provided
 * string is uppercase. By default only the first letter is tested.
 * To test the full string set $fullString to TRUE.
 */
class IsUppercaseViewHelper extends AbstractConditionViewHelper
{

    use ConditionViewHelperTrait;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', 'string to check', true);
        $this->registerArgument('fullString', 'string', 'need', false, false);
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        if (true === $arguments['fullString']) {
            $result = ctype_upper($arguments['string']);
        } else {
            $result = ctype_upper(substr($arguments['string'], 0, 1));
        }
        return true === $result;
    }
}
