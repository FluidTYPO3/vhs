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
 * ### Path: Relative Extension Resource Path
 *
 * Site Relative path to Extension Resources/Public folder.
 */
class ResourcesViewHelper extends AbstractExtensionViewHelper
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
            'Optional path to append after output of \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath'
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
        $path = true === empty($this->arguments['path']) ? '' : $this->arguments['path'];
        return ExtensionManagementUtility::extRelPath($extensionKey) . 'Resources/Public/' . $path;
    }
}
