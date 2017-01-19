<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class CubeViewHelperTest
 */
class CubeViewHelperTest extends AbstractMathViewHelperTest
{

    /**
     * @test
     */
    public function testSingleArgument()
    {
        $this->executeSingleArgumentTest(2, 8);
    }

    /**
     * @test
     */
    public function testSingleArgumentIteratorFirst()
    {
        $this->executeSingleArgumentTest([2, 3], [8, 27]);
    }
}
