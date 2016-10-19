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
 * Class PushViewHelperTest
 */
class PushViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expectedValue
     */
    public function testRender(array $arguments, $expectedValue)
    {
        $this->assertEquals($this->executeViewHelper($arguments), $expectedValue);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [['subject' => ['foo', 'bar'], 'add' => 'baz', 'key' => null], ['foo', 'bar', 'baz']],
            [['subject' => ['f' => 'foo', 'b' => 'bar'], 'add' => 'baz', 'key' => 'c'], ['f' => 'foo', 'b' => 'bar', 'c' => 'baz']],
            [['subject' => ['f' => 'foo', 'b' => 'bar'], 'add' => 'baz', 'key' => 'b'], ['f' => 'foo', 'b' => 'baz']],
        ];
    }
}
