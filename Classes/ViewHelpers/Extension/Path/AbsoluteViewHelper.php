<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension\Path;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Extension\AbstractExtensionViewHelper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ### Path: Absolute Extension Folder Path
 *
 * Returns the absolute path to an extension folder.
 */
class AbsoluteViewHelper extends AbstractExtensionViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'path',
            'string',
            'Optional path to append, second argument when calling ' .
            '\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath'
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
        /** @var string|null $path */
        $path = $arguments['path'];
        return ExtensionManagementUtility::extPath(
            static::getExtensionKey($arguments, $renderingContext),
            (string) ($path ?? '')
        );
    }
}
