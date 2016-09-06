<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Render: ASCII Character
 *
 * Renders a single character identified by its charset number.
 *
 * For example: `<v:render.character ascii="10" /> renders a UNIX linebreak
 * as does {v:render.character(ascii: 10)}. Can be used in combination with
 * `v:iterator.loop` to render sequences or repeat the same character:
 *
 *     {v:render.ascii(ascii: 10) -> v:iterator.loop(count: 5)}
 *
 * And naturally you can feed any integer variable or ViewHelper return value
 * into the `ascii` parameter throught `renderChildren` to allow chaining:
 *
 *     {variableWithAsciiInteger -> v:render.ascii()}
 *
 * And arrays are also supported - they will produce a string of characters
 * from each number in the array:
 *
 *     {v:render.ascii(ascii: {0: 13, 1: 10})}
 *
 * Will produce a Windows line break, \r\n.
 */
class AsciiViewHelper extends AbstractViewHelper
{

    /**
     * @param mixed $ascii
     * @return string
     */
    public function render($ascii = null)
    {
        if (null === $ascii) {
            $ascii = $this->renderChildren();
        }
        if (true === is_numeric($ascii)) {
            return chr((integer) $ascii);
        }
        if (true === is_array($ascii) || true === $ascii instanceof \Traversable) {
            $string = '';
            foreach ($ascii as $characterNumber) {
                $string .= chr($characterNumber);
            }
            return $string;
        }
        return '';
    }
}
