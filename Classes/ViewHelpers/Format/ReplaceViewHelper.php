<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Replaces $substring in $content with $replacement.
 */
class ReplaceViewHelper extends AbstractViewHelper
{

    /**
     * @param string $substring
     * @param string $content
     * @param string $replacement
     * @param integer $count
     * @param boolean $caseSensitve
     * @return string
     */
    public function render($substring, $content = null, $replacement = '', $count = null, $caseSensitive = true)
    {
        if (null === $content) {
            $content = $this->renderChildren();
        }
        $function = (true === $caseSensitive ? 'str_replace' : 'str_ireplace');
        return $function($substring, $replacement, $content, $count);
    }
}
