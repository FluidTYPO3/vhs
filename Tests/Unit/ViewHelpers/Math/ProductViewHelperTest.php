<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class ProductViewHelperTest
 */
class ProductViewHelperTest extends AbstractMathViewHelperTest
{

    /**
     * @test
     */
    public function testSingleArgumentIterator()
    {
        $this->executeSingleArgumentTest([2, 8], 16);
    }

    /**
     * @test
     */
    public function testDualArguments()
    {
        $this->executeDualArgumentTest(8, 2, 16);
    }

    /**
     * @test
     */
    public function executeMissingArgumentTest()
    {
        $this->expectViewHelperException('Required argument "b" was not supplied');
        $this->executeViewHelper(['a' => 1, 'fail' => true]);
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
