<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class DivisionViewHelperTest
 */
class DivisionViewHelperTest extends AbstractMathViewHelperTestCase
{
    /**
     * @test
     */
    public function testDualArgument(): void
    {
        $this->executeDualArgumentTest(4, 2, 2);
    }

    /**
     * @test
     */
    public function testDualArgumentIteratorFirst(): void
    {
        $this->executeDualArgumentTest([4, 8], 2, [2, 4]);
    }

    /**
     * @test
     */
    public function executeMissingArgumentTest(): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelper(['a' => 1, 'fail' => true]);
    }

    /**
     * @test
     */
    public function executeInvalidFirstArgumentTypeTest(): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelper(['b' => 1, 'fail' => true]);
    }

    /**
     * @test
     */
    public function executeInvalidSecondArgumentTypeTest(): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelper(['a' => 1, 'b' => [1], 'fail' => true]);
    }
}
