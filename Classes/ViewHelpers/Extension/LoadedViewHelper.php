<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Extension: Loaded (Condition) ViewHelper
 *
 * Condition to check if an extension is loaded.
 *
 * ### Example:
 *
 *     {v:extension.loaded(extensionName: 'news', then: 'yes', else: 'no')}
 *
 *     <v:extension.loaded extensionName="news">
 *         ...
 *     </v:extension.loaded>
 */
class LoadedViewHelper extends AbstractConditionViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'extensionName',
            'string',
            'Name of extension that must be loaded in order to evaluate as TRUE, UpperCamelCase',
            true
        );
    }

    /**
     * This method decides if the condition is TRUE or FALSE. It can be overriden in extending viewhelpers
     * to adjust functionality.
     *
     * @param array $arguments ViewHelper arguments to evaluate the condition for this ViewHelper, allows for
     *                         flexiblity in overriding this method.
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        $extensionName = $arguments['extensionName'];
        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
        $isLoaded = ExtensionManagementUtility::isLoaded($extensionKey);
        return true === $isLoaded;
    }
}
