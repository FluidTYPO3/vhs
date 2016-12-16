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
 * Class WordsViewHelperTest
 */
class WordsViewHelperTest extends AbstractViewHelperTest
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
                ['string' => 'string with four words'],
                4
            ],
            [
                ['string' => 'string <b>with HTML</b> inside'],
                4
            ],
            [
                ['string' => 'string with strånge unicøde chæræcters'],
                5
            ],
            [
                ['string' => 'string with ' . PHP_EOL . ' line break'],
                4
            ],
            [
                ['string' => 'string with ' . PHP_EOL . ' line break and <b>HTML</b>'],
                6
            ],
            [
                ['string' => '<li>foo</li></li>bar</li>'],
                2
            ],
        ];
    }

}
