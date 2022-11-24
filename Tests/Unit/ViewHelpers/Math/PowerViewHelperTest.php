<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class PowerViewHelperTest
 */
class PowerViewHelperTest extends AbstractMathViewHelperTest
{
    /**
     * @test
     */
    public function testDualArguments()
    {
        $this->executeDualArgumentTest(8, 2, 64);
    }
}
