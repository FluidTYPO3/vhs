<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class SquareRootViewHelperTest
 */
class SquareRootViewHelperTest extends AbstractMathViewHelperTestCase
{
    /**
     * @test
     */
    public function testSingleArgument(): void
    {
        $this->executeSingleArgumentTest(9, 3);
    }

    /**
     * @test
     */
    public function testSingleArgumentIteratorFirst(): void
    {
        $this->executeSingleArgumentTest([4, 16], [2, 4]);
    }
}
