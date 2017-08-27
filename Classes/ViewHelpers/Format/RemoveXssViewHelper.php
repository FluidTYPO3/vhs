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
 * @deprecated Since VHS 4.3
 */
class RemoveXssViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        GeneralUtility::deprecationLog('ViewHelper v:format.removeXSS is deprecated - escaping is now enabled by default');
        parent::initializeArguments();
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->renderChildren();
    }
}
