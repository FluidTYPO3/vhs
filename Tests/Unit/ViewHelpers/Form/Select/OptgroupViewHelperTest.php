<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Form\Select;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class OptgroupViewHelperTest
 */
class OptgroupViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param string|NULL $content
     * @param string $expected
     */
    public function testRender(array $arguments, $content, $expected)
    {
        $result = $this->executeViewHelperUsingTagContent('Text', $content, $arguments);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return array(
            array(array('label' => 'test'), '', '<optgroup label="test" />'),
            array(array('label' => 'test'), 'content', '<optgroup label="test">content</optgroup>')
        );
    }
}
