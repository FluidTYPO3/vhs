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
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class FieldNameViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @dataProvider getRenderTestValues
	 * @param array $arguments
	 * @param string|NULL $prefix
	 * @param string|NULL $objectName
	 * @param array|NULL $names
	 * @param string $expected
	 */
	public function testRender(array $arguments, $prefix, $objectName, $names, $expected) {
		$instance = $this->buildViewHelperInstance($arguments, [], NULL, 'Vhs');
		$viewHelperVariableContainer = new ViewHelperVariableContainer();
		if (NULL !== $objectName) {
			$viewHelperVariableContainer->add('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'formObjectName', $objectName);
		}
		if (NULL !== $prefix) {
			$viewHelperVariableContainer->add('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'fieldNamePrefix', $prefix);
		}
		if (NULL !== $names) {
			$viewHelperVariableContainer->add('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'formFieldNames', $names);
		}
		ObjectAccess::setProperty($instance, 'viewHelperVariableContainer', $viewHelperVariableContainer, TRUE);
		$instance->setArguments($arguments);
		$result = $instance->render();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return [
			[[], NULL, NULL, NULL, ''],
			[['name' => 'test'], NULL, NULL, NULL, 'test'],
			[['property' => 'test'], NULL, NULL, NULL, ''],
			[['name' => 'test'], 'prefix', 'object', NULL, 'prefix[test]'],
			[['property' => 'test'], 'prefix', 'object', NULL, 'prefix[object][test]'],
			[['name' => 'test'], '', '', NULL, 'test'],
			[['property' => 'test'], '', '', NULL, 'test'],
			[['name' => 'test'], 'prefix', '', NULL, 'prefix[test]'],
			[['property' => 'test'], 'prefix', '', NULL, 'prefix[test]'],
			[['name' => 'test'], 'prefix', 'object', [], 'prefix[test]'],
			[['property' => 'test'], 'prefix', 'object', [], 'prefix[object][test]'],
		];
	}

}
