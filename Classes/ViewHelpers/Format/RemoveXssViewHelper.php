<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Removes XSS from $string
 *
 * Class RemoveXssViewHelper
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class RemoveXssViewHelper extends AbstractViewHelper
{

    /**
     * Removes XSS from string
     *
     * @param string $string
     * @return string
     */
    public function render($string = null)
    {
        if (null === $string) {
            $string = $this->renderChildren();
        }
        return GeneralUtility::removeXSS($string);
    }
}
