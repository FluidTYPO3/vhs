<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class RangeViewHelperTest
 */
class RangeViewHelperTest extends AbstractMathViewHelperTest
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
    public function testSingleArgumentIteratorSingleValue()
    {
        $this->executeSingleArgumentTest(array(2), array(2, 2));
    }

    /**
     * @test
     */
    public function testSingleArgumentIteratorMultipleValues()
    {
        $this->executeSingleArgumentTest(array(2, 4, 6, 3, 8), array(2, 8));
    }
}
