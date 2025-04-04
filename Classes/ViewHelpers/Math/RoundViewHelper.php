<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;

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

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('decimals', 'integer', 'Number of decimals', false, 0);
    }

    /**
     * @param float|integer|string|iterable|array $a
     * @return float|array
     */
    protected static function calculateAction($a, array $arguments = [])
    {
        if (static::assertIsArrayOrIterator($a)) {
            /**
             * @var string|integer $index
             * @var float|integer|string|iterable|array $value
             * @var array $a
             */
            foreach ($a as $index => $value) {
                $a[$index] = static::calculateAction($value, $arguments);
            }
            return $a;
        }
        if (!is_scalar($a)) {
            return 0;
        }
        /** @var integer|float $a */
        return round($a, $arguments['decimals']);
    }
}
