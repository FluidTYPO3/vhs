<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class RangeViewHelperTest
 */
class RangeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @param array $arguments
     * @param array $expected
     * @dataProvider getRenderTestValues
     */
    public function testRender(array $arguments, array $expected)
    {
        $this->assertSame($this->executeViewHelper($arguments), $expected);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [['low' => 1, 'high' => 10, 'step' => 1], [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
            [['low' => 5, 'high' => 10, 'step' => 1], [5, 6, 7, 8, 9, 10]],
            [['low' => 1, 'high' => 5, 'step' => 1], [1, 2, 3, 4, 5]],
            [['low' => 1, 'high' => 10, 'step' => 3], [1, 4, 7, 10]],
        ];
    }
}
