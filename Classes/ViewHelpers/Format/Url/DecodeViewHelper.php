<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Url;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Urldecodes the provided string.
 */
class DecodeViewHelper extends AbstractViewHelper
{

    /**
     * @param string $content
     * @return string
     */
    public function render($content = null)
    {
        if (null === $content) {
            $content = $this->renderChildren();
        }
        return rawurldecode($content);
    }
}
