<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;

/**
 * ViewHelper Utility
 *
 * Contains helper methods used in resources ViewHelpers
 */
class ResourceUtility
{

    /**
     * Fixes a bug in TYPO3 6.2.0 that the properties metadata is not overlayed on localization.
     *
     * @param File $file
     * @return array
     */
    public static function getFileArray(File $file)
    {
        $properties = $file->getProperties();
        $stat = $file->getStorage()->getFileInfo($file);
        $array = $file->toArray();

        foreach ($properties as $key => $value) {
            $array[$key] = $value;
        }
        foreach ($stat as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }
}
