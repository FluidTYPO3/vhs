<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Form\Select;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * Class OptionViewHelperTest
 */
class OptionViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @param mixed $content
     * @return mixed
     */
    public static function fakeRenderChildrenClosure($content)
    {
        return $content;
    }

    /**
     * @return void
     */
    public function testRenderWithoutContextThrowsException()
    {
        $this->setExpectedException('RuntimeException');
        $this->executeViewHelper();
    }

    /**
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $selectedValue
     * @param mixed $content
     * @param string $expected
     */
    public function testRender(array $arguments, $selectedValue, $content, $expected)
    {
        $instance = $this->buildViewHelperInstance($arguments, array(), null, 'Vhs');
        $viewHelperVariableContainer = new ViewHelperVariableContainer();
        $viewHelperVariableContainer->add('FluidTYPO3\Vhs\ViewHelpers\Form\SelectViewHelper', 'options', array());
        $viewHelperVariableContainer->add('FluidTYPO3\Vhs\ViewHelpers\Form\SelectViewHelper', 'value', $selectedValue);
        ObjectAccess::setProperty($instance, 'viewHelperVariableContainer', $viewHelperVariableContainer, true);
        $instance->setArguments($arguments);
        $instance->setRenderChildrenClosure(function () use ($content) {
            return $content;

        });
        $result = $instance->render();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return array(
            array(array(), '', '', '<option selected="selected" />'),
            array(array(), 'notfound', '', '<option />'),
            array(array(), 'notfound', 'content', '<option>content</option>'),
            array(array('selected' => true), 'notfound', 'content', '<option selected="selected">content</option>'),
            array(
                array('value' => 'notfound'),
                'notfound',
                'content',
                '<option selected="selected" value="notfound">content</option>'
            ),
            array(
                array('value' => 'a'),
                array('a', 'b'),
                'content',
                '<option selected="selected" value="a">content</option>'
            ),
        );
    }
}
