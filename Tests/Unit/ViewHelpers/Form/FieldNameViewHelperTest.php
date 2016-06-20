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
        $instance = $this->buildViewHelperInstance($arguments, array(), null, 'Vhs');
        $viewHelperVariableContainer = new ViewHelperVariableContainer();
        if (null !== $objectName) {
            $viewHelperVariableContainer->add('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'formObjectName', $objectName);
        }
        if (null !== $prefix) {
            $viewHelperVariableContainer->add('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'fieldNamePrefix', $prefix);
        }
        if (null !== $names) {
            $viewHelperVariableContainer->add('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'formFieldNames', $names);
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
        return array(
            array(array(), null, null, null, ''),
            array(array('name' => 'test'), null, null, null, 'test'),
            array(array('property' => 'test'), null, null, null, ''),
            array(array('name' => 'test'), 'prefix', 'object', null, 'prefix[test]'),
            array(array('property' => 'test'), 'prefix', 'object', null, 'prefix[object][test]'),
            array(array('name' => 'test'), '', '', null, 'test'),
            array(array('property' => 'test'), '', '', null, 'test'),
            array(array('name' => 'test'), 'prefix', '', null, 'prefix[test]'),
            array(array('property' => 'test'), 'prefix', '', null, 'prefix[test]'),
            array(array('name' => 'test'), 'prefix', 'object', array(), 'prefix[test]'),
            array(array('property' => 'test'), 'prefix', 'object', array(), 'prefix[object][test]'),
        );
    }
}
