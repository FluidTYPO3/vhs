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
 * ### Path: Relative Extension Folder Path
 *
 * Returns the relative path to an Extension folder.
 */
class RelativeViewHelper extends AbstractExtensionViewHelper
{

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $extensionKey = $this->getExtensionKey();
        return ExtensionManagementUtility::extRelPath($extensionKey);
    }
}
