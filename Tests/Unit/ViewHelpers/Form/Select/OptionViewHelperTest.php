<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Form\Select;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\ViewHelpers\Form\SelectViewHelper;
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
        $instance = $this->buildViewHelperInstance($arguments, [], null, 'Vhs');
        $viewHelperVariableContainer = new ViewHelperVariableContainer();
        $viewHelperVariableContainer->add(SelectViewHelper::class, 'options', []);
        $viewHelperVariableContainer->add(SelectViewHelper::class, 'value', $selectedValue);
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
        return [
            [[], '', '', '<option selected="selected" />'],
            [[], 'notfound', '', '<option />'],
            [[], 'notfound', 'content', '<option>content</option>'],
            [['selected' => true], 'notfound', 'content', '<option selected="selected">content</option>'],
            [
                ['value' => 'notfound'],
                'notfound',
                'content',
                '<option selected="selected" value="notfound">content</option>'
            ],
            [
                ['value' => 'a'],
                ['a', 'b'],
                'content',
                '<option selected="selected" value="a">content</option>'
            ],
        ];
    }
}
