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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Extension: Loaded (Condition) ViewHelper
 *
 * Condition to check if an extension is loaded.
 *
 * ### Example:
 *
 * ```
 * {v:extension.loaded(extensionName: 'news', then: 'yes', else: 'no')}
 * ```
 *
 * ```
 * <v:extension.loaded extensionName="news">
 *     ...
 * </v:extension.loaded>
 * ```
 */
class LoadedViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'extensionName',
            'string',
            'Name of extension that must be loaded in order to evaluate as TRUE, UpperCamelCase',
            true
        );
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        /** @var string $extensionName */
        $extensionName = $arguments['extensionName'];
        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
        $isLoaded = ExtensionManagementUtility::isLoaded($extensionKey);
        return $isLoaded;
    }
}
