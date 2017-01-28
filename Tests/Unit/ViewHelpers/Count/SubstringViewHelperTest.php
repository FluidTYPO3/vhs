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
 * Class SubstringViewHelperTest
 */
class SubstringViewHelperTest extends AbstractViewHelperTest
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
                ['haystack' => 'foobar baz bar', 'string' => 'bar'],
                2
            ],
            [
                ['haystack' => 'string <b>with HTML</b>', 'string' => 'HTML'],
                1
            ],
            [
                ['haystack' => 'string with strånge unicøde chæræcters', 'string' => 'unicøde'],
                1
            ],
        ];
    }

}
