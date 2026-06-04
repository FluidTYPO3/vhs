<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class FloorViewHelperTest
 */
class FloorViewHelperTest extends AbstractMathViewHelperTestCase
{
    /**
     * @test
     */
    public function testSingleArgument(): void
    {
        $this->executeSingleArgumentTest(1.5, 1);
    }

    /**
     * @test
     */
    public function testSingleArgumentIteratorFirst(): void
    {
        $this->executeSingleArgumentTest([1.5, 2.5], [1, 2]);
    }
}
