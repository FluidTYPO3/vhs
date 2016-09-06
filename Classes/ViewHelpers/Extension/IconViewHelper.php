<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * ### Extension: Icon ViewHelper
 *
 * Outputs the icon of the extension key. Supports both
 * extension key and extension name arguments.
 */
class IconViewHelper extends AbstractExtensionViewHelper
{

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $extensionKey = $this->getExtensionKey();
        return ExtensionManagementUtility::extPath($extensionKey, 'ext_icon.gif');
    }
}
