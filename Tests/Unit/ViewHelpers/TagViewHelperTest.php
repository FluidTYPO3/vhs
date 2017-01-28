<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class TagViewHelperTest
 */
class TagViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getRenderTagTestValues
     * @param array $arguments
     * @param mixed $content
     * @param string $expected
     */
    public function renderTag(array $arguments, $content, $expected)
    {
        $result = $this->executeViewHelperUsingTagContent($content, $arguments);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getRenderTagTestValues()
    {
        return [
            [['name' => 'div'], 'test', '<div>test</div>'],
            [['name' => 'div', 'class' => 'test'], 'test', '<div class="test">test</div>'],
            [['name' => 'div', 'hideIfEmpty' => true], '', ''],
        ];
    }
}
