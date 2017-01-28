<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Math: Round
 *
 * Rounds off $a which can be either an array-accessible
 * value (Iterator+ArrayAccess || array) or a raw numeric
 * value.
 */
class RoundViewHelper extends AbstractSingleMathViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('decimals', 'integer', 'Number of decimals', false, 0);
    }

    /**
     * @param mixed $a
     * @param null $b
     * @param array $arguments
     * @return integer
     */
    protected static function calculateAction($a, $b, array $arguments)
    {
        if (static::assertIsArrayOrIterator($a)) {
            foreach ($a as $index => $value) {
                $a[$index] = static::calculateAction($value, $b, $arguments);
            }
            return $a;
        }
        return round($a, $arguments['decimals']);
    }
}
