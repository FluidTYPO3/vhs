<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class AverageViewHelperTest
 */
class AverageViewHelperTest extends AbstractMathViewHelperTestCase
{
    /**
     * @test
     */
    public function testSingleArgument(): void
    {
        $this->executeSingleArgumentTest(1, 1);
    }

    /**
     * @test
     */
    public function testSingleArgumentIteratorFirst(): void
    {
        $this->executeSingleArgumentTest([1, 3], 2);
    }

    /**
     * @test
     */
    public function testDualArgument(): void
    {
        $this->executeDualArgumentTest(1, 3, 2);
    }

    /**
     * @test
     */
    public function testDualArgumentWithIteratorFirst(): void
    {
        $this->executeDualArgumentTest([1, 5], 3, [2, 4]);
    }

    /**
     * @test
     */
    public function testDualArgumentBothIterators(): void
    {
        $this->executeDualArgumentTest([1, 5], [3, 3], [2, 4]);
    }
}
