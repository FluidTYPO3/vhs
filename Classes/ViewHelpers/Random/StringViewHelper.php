<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Random: String Generator
 *
 * Use either `minimumLength` / `maximumLength` or just `length`.
 *
 * Specify the characters which can be randomized using `characters`.
 *
 * Has built-in insurance that first character of random string is
 * an alphabetic character (allowing safe use as DOM id for example).
 */
class StringViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('length', 'integer', 'Length of string to generate');
        $this->registerArgument('minimumLength', 'integer', 'Minimum length of string if random length', false, 32);
        $this->registerArgument('maximumLength', 'integer', 'Minimum length of string if random length', false, 32);
        $this->registerArgument('characters', 'string', 'Characters to use in string', false, '0123456789abcdef');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $length = $arguments['length'];
        $minimumLength = (integer) $arguments['minimumLength'];
        $maximumLength = (integer) $arguments['maximumLength'];
        $characters = $arguments['characters'];
        if ($minimumLength != $maximumLength) {
            $length = rand($minimumLength, $maximumLength);
        } else {
            $length = $length !== null ? $length : $minimumLength;
        }
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, mb_strlen($characters) - 1);
            $string .= $characters{$randomIndex};
        }
        return $string;
    }
}
