<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class MedianViewHelperTest
 */
class MedianViewHelperTest extends AbstractMathViewHelperTest
{

    /**
     * @test
     */
    public function testSingleArgumentNotIteratorPassesThrough()
    {
        $this->executeSingleArgumentTest(1, 1);
    }

    /**
     * @test
     */
    public function testSingleArgumentThreeMembers()
    {
        $this->executeSingleArgumentTest([1, 2, 3], 2);
    }

    /**
     * @test
     */
    public function testSingleArgumentFourMembers()
    {
        $this->executeSingleArgumentTest([1, 2, 3, 4], 2.5);
    }
}
