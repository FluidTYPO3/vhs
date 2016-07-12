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

/**
 * ### Path: Absolute Extension Folder Path
 *
 * Returns the absolute path to an extension folder.
 */
class AbsoluteViewHelper extends AbstractExtensionViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
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
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $extensionKey = $this->getExtensionKey();
        return ExtensionManagementUtility::extPath(
            $extensionKey,
            isset($this->arguments['path']) ? $this->arguments['path'] : null
        );
    }
}
