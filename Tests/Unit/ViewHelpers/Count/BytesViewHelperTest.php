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
 * Class BytesViewHelperTest
 */
class BytesViewHelperTest extends AbstractViewHelperTest
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
                ['string' => 'foobar', 'encoding' => 'UTF-8'],
                6
            ],
            [
                ['string' => 'string with spaces', 'encoding' => 'UTF-8'],
                18
            ],
            [
                ['string' => 'string <b>with HTML</b>', 'encoding' => 'UTF-8'],
                23
            ],
            [
                ['string' => 'string with strånge unicøde chæræcters', 'encoding' => 'UTF-8'],
                38
            ],
        ];
    }

}
