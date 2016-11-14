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
        $this->assertEquals($this->executeViewHelper($arguments), $expectedValue);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [['haystack' => [], 'length' => 0, 'start' => 0], []],
            [['haystack' => ['foo', 'bar'], 'length' => 1, 'start' => 0], ['foo']],
            [['haystack' => new \ArrayIterator(['foo', 'bar']), 'start' => 1, 'length' => 1, 'preserveKeys' => true], [1 => 'bar']],
            [['haystack' => new \ArrayIterator(['foo', 'bar']), 'start' => 1, 'length' => 1, 'preserveKeys' => false], [0 => 'bar']],
        ];
    }
}
