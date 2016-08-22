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
        return array(
            array(array('subject' => array('foo', 'bar'), 'add' => 'baz', 'key' => null), array('foo', 'bar', 'baz')),
            array(array('subject' => array('f' => 'foo', 'b' => 'bar'), 'add' => 'baz', 'key' => 'c'), array('f' => 'foo', 'b' => 'bar', 'c' => 'baz')),
            array(array('subject' => array('f' => 'foo', 'b' => 'bar'), 'add' => 'baz', 'key' => 'b'), array('f' => 'foo', 'b' => 'baz')),
        );
    }
}
