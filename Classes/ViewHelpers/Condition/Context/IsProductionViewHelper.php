<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Context;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Context: IsProduction
 *
 * Returns true if current root application context is production otherwise false.
 * If no application context has been set, then this is the default context.
 *
 * #### Note about how to set the application context
 *
 * The context TYPO3 CMS runs in is specified through the environment variable TYPO3_CONTEXT.
 * It can be set by .htaccess or in the server configuration
 *
 * See: http://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Bootstrapping/Index.html#bootstrapping-context
 *
 * @author     Benjamin Beck <beck@beckdigitalemedien.de>
 */
class IsProductionViewHelper extends AbstractConditionViewHelper
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
        return GeneralUtility::getApplicationContext()->isProduction();
    }
}
