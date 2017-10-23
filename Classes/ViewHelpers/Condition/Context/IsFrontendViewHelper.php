<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Context;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Condition: Is context Frontend?
 *
 * A condition ViewHelper which renders the `then` child if
 * current context being rendered is FE.
 *
 * ### Examples
 *
 *     <!-- simple usage, content becomes then-child -->
 *     <v:condition.context.isFrontend>
 *         Hooray for BE contexts!
 *     </v:condition.context.isFrontend>
 *     <!-- extended use combined with f:then and f:else -->
 *     <v:condition.context.isFrontend>
 *         <f:then>
 *            Hooray for BE contexts!
 *         </f:then>
 *         <f:else>
 *            Maybe BE, maybe CLI.
 *         </f:else>
 *     </v:condition.context.isFrontend>
 */
class IsFrontendViewHelper extends AbstractConditionViewHelper
{
    /**
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return ('FE' === TYPO3_MODE);
    }
}
