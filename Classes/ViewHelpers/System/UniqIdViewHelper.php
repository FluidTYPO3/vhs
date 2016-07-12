<?php
namespace FluidTYPO3\Vhs\ViewHelpers\System;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### System: Unique ID
 *
 * Returns a unique ID based on PHP's uniqid-function.
 *
 * Comes in useful when handling/generating html-element-IDs
 * for usage with JavaScript.
 */
class UniqIdViewHelper extends AbstractViewHelper
{

    /**
     * @param string $prefix An optional prefix for making sure it's unique across environments
     * @param boolean $moreEntropy Add some pseudo random strings. Refer to uniqid()'s Reference.
     * @return string
     */
    public function render($prefix = '', $moreEntropy = false)
    {
        $uniqueId = uniqid($prefix, $moreEntropy);
        return $uniqueId;
    }
}
