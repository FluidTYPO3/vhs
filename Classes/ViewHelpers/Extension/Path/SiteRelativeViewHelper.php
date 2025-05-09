<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension\Path;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use FluidTYPO3\Vhs\ViewHelpers\Extension\AbstractExtensionViewHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ### Path: Relative Extension Folder Path
 *
 * Returns the site relative path to an extension folder.
 */
class SiteRelativeViewHelper extends AbstractExtensionViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $extensionKey = static::getExtensionKey($arguments, $renderingContext);
        $extensionPath = ExtensionManagementUtility::extPath($extensionKey);
        return PathUtility::getAbsoluteWebPath($extensionPath);
    }
}
