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
 * ### Path: Relative Extension Resource Path
 *
 * Site Relative path to Extension Resources/Public folder.
 */
class ResourcesViewHelper extends AbstractExtensionViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'path',
            'string',
            'Optional path to append after output of \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath'
        );
    }

    /**
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $path = empty($arguments['path']) ? '' : $arguments['path'];

        $extPath = ExtensionManagementUtility::extPath(
            static::getExtensionKey($arguments, $renderingContext),
            'Resources/Public/' . $path
        );
        return PathUtility::stripPathSitePrefix($extPath);
    }
}
