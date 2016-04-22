<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Context;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Is context Backend?
 *
 * A condition ViewHelper which renders the `then` child if
 * current context being rendered is BE.
 *
 * ### Examples
 *
 *     <!-- simple usage, content becomes then-child -->
 *     <v:condition.context.isBackend>
 *         Hooray for BE contexts!
 *     </v:condition.context.isBackend>
 *     <!-- extended use combined with f:then and f:else -->
 *     <v:condition.context.isBackend>
 *         <f:then>
 *            Hooray for BE contexts!
 *         </f:then>
 *         <f:else>
 *            Maybe FE, maybe CLI.
 *         </f:else>
 *     </v:condition.context.isBackend>
 *
 * @author Claus Due <claus@namelesscoder.net>
 */
class IsBackendViewHelper extends AbstractConditionViewHelper
{

    use ConditionViewHelperTrait;

    /**
     * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers to adjust functionality.
     *
     * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for flexiblity in overriding this method.
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return ('BE' === TYPO3_MODE);
    }
}
