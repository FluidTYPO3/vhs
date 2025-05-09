<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Render: ASCII Character
 *
 * Renders a single character identified by its charset number.
 *
 * For example: `<v:render.character ascii="10" /> renders a UNIX linebreak
 * as does {v:render.character(ascii: 10)}. Can be used in combination with
 * `v:iterator.loop` to render sequences or repeat the same character:
 *
 * ```
 * {v:render.ascii(ascii: 10) -> v:iterator.loop(count: 5)}
 * ```
 *
 * And naturally you can feed any integer variable or ViewHelper return value
 * into the `ascii` parameter throught `renderChildren` to allow chaining:
 *
 * ```
 * {variableWithAsciiInteger -> v:render.ascii()}
 * ```
 *
 * And arrays are also supported - they will produce a string of characters
 * from each number in the array:
 *
 * ```
 * {v:render.ascii(ascii: {0: 13, 1: 10})}
 * ```
 *
 * Will produce a Windows line break, \r\n.
 */
class AsciiViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('ascii', 'mixed', 'ASCII character to render');
    }

    /**
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $ascii = $renderChildrenClosure();
        if (is_numeric($ascii)) {
            return chr((integer) $ascii);
        }
        if (is_array($ascii) || $ascii instanceof \Traversable) {
            $string = '';
            foreach ($ascii as $characterNumber) {
                $string .= chr($characterNumber);
            }
            return $string;
        }
        return '';
    }
}
