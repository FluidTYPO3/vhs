<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Count;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class LinesViewHelperTest
 */
class LinesViewHelperTest extends AbstractViewHelperTest
{
    /**
     * @param array $arguments
     * @param integer $expected
     * @test
     * @dataProvider getRenderTestValues
     */
    public function testRender(array $arguments, $expected)
    {
        $this->assertEquals($expected, $this->executeViewHelper($arguments));
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [
                ['string' => ''],
                0
            ],
            [
                ['string' => 'foobar'],
                1
            ],
            [
                ['string' => 'word with ' . PHP_EOL . ' one line break'],
                2
            ],
            [
                ['string' => 'word with ' . PHP_EOL . ' two line breaks ' . PHP_EOL],
                3
            ],
        ];
    }

}
