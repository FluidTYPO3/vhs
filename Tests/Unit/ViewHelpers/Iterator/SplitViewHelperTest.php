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
 * Class SplitViewHelperTest
 */
class SplitViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expectedValue
     */
    public function testRender(array $arguments, $expectedValue)
    {
        $value = $this->executeViewHelper($arguments);
        $this->assertEquals($value, $expectedValue);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            'zero length empty string' => [['subject' => '', 'length' => 0], []],
            'zero length non-empty string' => [['subject' => 'not-empty', 'length' => 0], []],
            'non-empty string 1 length' => [['subject' => 'foobar', 'length' => 1], ['f', 'o', 'o', 'b', 'a', 'r']],
            'non-empty string 2 length' => [['subject' => 'foobar', 'length' => 2], ['fo', 'ob', 'ar']],
        ];
    }
}
