<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Random: String Generator
 *
 * Use either `minimumLength` / `maximumLength` or just `length`.
 *
 * Specify the characters which can be randomized using `characters`.
 */
class StringViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('length', 'integer', 'Length of string to generate');
        $this->registerArgument('minimumLength', 'integer', 'Minimum length of string if random length', false, 32);
        $this->registerArgument('maximumLength', 'integer', 'Minimum length of string if random length', false, 32);
        $this->registerArgument('characters', 'string', 'Characters to use in string', false, '0123456789abcdef');
    }

    /**
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var int $length */
        $length = $arguments['length'];

        /** @var int $minimumLength */
        $minimumLength = $arguments['minimumLength'];
        $minimumLength = (integer) $minimumLength;
        /** @var int $maximumLength */
        $maximumLength = $arguments['maximumLength'];
        $maximumLength = (integer) $maximumLength;
        /** @var string $characters */
        $characters = $arguments['characters'];
        if ($minimumLength != $maximumLength) {
            /** @var int<1, max> $length */
            $length = random_int($minimumLength, $maximumLength);
        } else {
            /** @var int<1, max> $length */
            $length = $length !== null ? $length : $minimumLength;
        }
        $string = '';
        if ($characters === '0123456789abcdef') {
            $string = bin2hex(random_bytes($length));
        } else {
            for ($i = 0; $i < $length; $i++) {
                $randomIndex = random_int(0, max(mb_strlen($characters) - 1, 1));
                $string .= $characters[$randomIndex];
            }
        }
        return $string;
    }
}
