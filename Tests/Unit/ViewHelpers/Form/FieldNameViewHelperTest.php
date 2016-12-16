<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper;

/**
 * Class FieldNameViewHelperTest
 */
class FieldNameViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param string|NULL $prefix
     * @param string|NULL $objectName
     * @param array|NULL $names
     * @param string $expected
     */
    public function testRender(array $arguments, $prefix, $objectName, $names, $expected)
    {
        $instance = $this->buildViewHelperInstance($arguments, [], null, 'Vhs');
        $viewHelperVariableContainer = new ViewHelperVariableContainer();
        if (null !== $objectName) {
            $viewHelperVariableContainer->add(FormViewHelper::class, 'formObjectName', $objectName);
        }
        if (null !== $prefix) {
            $viewHelperVariableContainer->add(FormViewHelper::class, 'fieldNamePrefix', $prefix);
        }
        if (null !== $names) {
            $viewHelperVariableContainer->add(FormViewHelper::class, 'formFieldNames', $names);
        }
        ObjectAccess::setProperty($instance, 'viewHelperVariableContainer', $viewHelperVariableContainer, true);
        $instance->setArguments($arguments);
        $result = $instance->render();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [[], null, null, null, ''],
            [['name' => 'test'], null, null, null, 'test'],
            [['property' => 'test'], null, null, null, ''],
            [['name' => 'test'], 'prefix', 'object', null, 'prefix[test]'],
            [['property' => 'test'], 'prefix', 'object', null, 'prefix[object][test]'],
            [['name' => 'test'], '', '', null, 'test'],
            [['property' => 'test'], '', '', null, 'test'],
            [['name' => 'test'], 'prefix', '', null, 'prefix[test]'],
            [['property' => 'test'], 'prefix', '', null, 'prefix[test]'],
            [['name' => 'test'], 'prefix', 'object', [], 'prefix[test]'],
            [['property' => 'test'], 'prefix', 'object', [], 'prefix[object][test]'],
        ];
    }
}
