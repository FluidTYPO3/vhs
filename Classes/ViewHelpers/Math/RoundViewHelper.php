<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Math: Round
 *
 * Rounds off $a which can be either an array-accessible
 * value (Iterator+ArrayAccess || array) or a raw numeric
 * value.
 */
class RoundViewHelper extends AbstractSingleMathViewHelper
{

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
     * @return integer
     */
    protected function calculateAction($a)
    {
        return round($a, $this->arguments['decimals']);
    }
}
