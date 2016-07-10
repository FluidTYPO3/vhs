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
 * Trims $content by stripping off $characters (string list
 * of individual chars to strip off, default is all whitespaces).
 */
class TrimViewHelper extends AbstractViewHelper
{

    /**
     * Trims content by stripping off $characters
     *
     * @param string $content
     * @param string $characters
     * @return string
     */
    public function render($content = null, $characters = null)
    {
        if (null === $content) {
            $content = $this->renderChildren();
        }
        if (false === empty($characters)) {
            $content = trim($content, $characters);
        } else {
            $content = trim($content);
        }
        return $content;
    }
}
