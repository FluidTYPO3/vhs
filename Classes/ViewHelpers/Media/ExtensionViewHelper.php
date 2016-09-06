<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the extension of the provided file.
 */
class ExtensionViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('file', 'string', 'Path to the file to determine extension for.', false);
    }

    /**
     * @return string
     */
    public function render()
    {

        $filePath = $this->arguments['file'];

        if (true === empty($filePath)) {
            $filePath = $this->renderChildren();

            if (null === $filePath) {
                return '';
            }
        }

        $file = GeneralUtility::getFileAbsFileName($filePath);

        $parts = explode('.', basename($file));

        // file has no extension
        if (1 === count($parts)) {
            return '';
        }

        $extension = strtolower(array_pop($parts));

        return $extension;
    }
}
