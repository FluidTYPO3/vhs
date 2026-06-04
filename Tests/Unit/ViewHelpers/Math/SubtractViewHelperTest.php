<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class SubtractViewHelperTest
 */
class SubtractViewHelperTest extends AbstractMathViewHelperTestCase
{
    /**
     * @test
     */
    public function testSingleArgumentIterator(): void
    {
        $this->executeSingleArgumentTest([8, 2], -10);
    }

    /**
     * @test
     */
    public function testDualArguments(): void
    {
        $this->executeDualArgumentTest(8, 2, 6);
    }

    /**
     * @test
     */
    public function executeMissingArgumentTest(): void
    {
        $this->expectViewHelperException();
        $result = $this->executeViewHelper(['a' => 1, 'fail' => true]);
    }

    /**
     * @test
     */
    public function executeInvalidArgumentTypeTest(): void
    {
        $this->expectViewHelperException();
        $result = $this->executeViewHelper(['b' => 1, 'fail' => true]);
    }
}
