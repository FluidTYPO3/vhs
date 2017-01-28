<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class AsciiViewHelperTest
 */
class AsciiViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getTestRenderValues
     * @param integer $ascii
     * @param string $expected
     */
    public function testRender($ascii, $expected)
    {
        $result = $this->executeViewHelper(['ascii' => $ascii]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getTestRenderValues()
    {
        return [
            [10, "\n"],
            [32, ' '],
            [64, '@'],
            [[65, 66, 67], 'ABC'],
            [new \ArrayIterator([67, 66, 65]), 'CBA']
        ];
    }
}
