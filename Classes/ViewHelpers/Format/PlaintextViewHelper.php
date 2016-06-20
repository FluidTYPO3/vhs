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
 * Processes output as plaintext. Will trim whitespace off
 * each line that is provided, making display in a <pre>
 * work correctly indented even if the source is not.
 *
 * Expects that you use f:format.htmlentities or similar
 * if you do not want HTML to be displayed as HTML, or
 * simply want it stripped out.
 */
class PlaintextViewHelper extends AbstractViewHelper
{

    /**
     * Trims content, then trims each line of content
     *
     * @param string $content
     * @return string
     */
    public function render($content = null)
    {
        if (null === $content) {
            $content = $this->renderChildren();
        }
        $content = trim($content);
        $lines = explode("\n", $content);
        $lines = array_map('trim', $lines);
        return implode(LF, $lines);
    }
}
