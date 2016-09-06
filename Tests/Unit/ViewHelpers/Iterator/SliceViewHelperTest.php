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
 * Class SliceViewHelperTest
 */
class SliceViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expectedValue
     */
    public function testRender(array $arguments, $expectedValue)
    {
        if (true === isset($arguments['as'])) {
            $value = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'variable', $arguments);
        } else {
            $value = $this->executeViewHelper($arguments);
            $haystack = $arguments['haystack'];
            unset($arguments['haystack']);
            $value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', $arguments, array('v' => $haystack));
            $this->assertEquals($value, $value2);
        }
        $this->assertEquals($value, $expectedValue);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return array(
            array(array('haystack' => array(), 'length' => 0, 'start' => 0), array()),
            array(array('haystack' => array('foo', 'bar'), 'length' => 1, 'start' => 0), array('foo')),
            array(array('haystack' => array('foo', 'bar'), 'length' => 1, 'start' => 0, 'as' => 'variable'), array('foo')),
            array(array('haystack' => new \ArrayIterator(array('foo', 'bar')), 'start' => 1, 'length' => 1), array(1 => 'bar')),
            array(array('haystack' => new \ArrayIterator(array('foo', 'bar')), 'start' => 1, 'length' => 1, 'as' => 'variable'), array(1 => 'bar')),
        );
    }
}
