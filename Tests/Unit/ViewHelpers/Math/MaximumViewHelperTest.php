<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class MaximumViewHelperTest
 */
class MaximumViewHelperTest extends AbstractMathViewHelperTest
{

    /**
     * @test
     */
    public function testSingleArgument()
    {
        $this->executeSingleArgumentTest([1, 3], 3);
    }

    /**
     * @test
     */
    public function testDualArgument()
    {
        $this->executeDualArgumentTest(4, 2, 4);
    }

    /**
     * @test
     */
    public function testDualArgumentBothIterators()
    {
        $this->executeDualArgumentTest([4, 8], [8, 8], [8, 8]);
    }

    /**
     * @test
     */
    public function executeMissingArgumentTest()
    {
        $this->expectViewHelperException('Required argument "b" was not supplied');
        $result = $this->executeViewHelper(['a' => 1, 'fail' => true]);
    }

    /**
     * @test
     */
    public function executeInvalidArgumentTypeTest()
    {
        $this->expectViewHelperException('Required argument "a" was not supplied');
        $this->executeViewHelper(['b' => 1, 'fail' => true]);
    }
}
