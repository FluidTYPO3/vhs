<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class ImplodeViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function implodesString() {
		$arguments = ['content' => ['1', '2', '3'], 'glue' => ','];
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('1,2,3', $result);
	}

	/**
	 * @test
	 */
	public function supportsCustomGlue() {
		$arguments = ['content' => ['1', '2', '3'], 'glue' => ';'];
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('1;2;3', $result);
	}

	/**
	 * @test
	 */
	public function supportsConstantsGlue() {
		$arguments = ['content' => ['1', '2', '3'], 'glue' => 'constant:LF'];
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals("1\n2\n3", $result);
	}

	/**
	 * @test
	 */
	public function passesThroughUnknownSpecialGlue() {
		$arguments = ['content' => ['1', '2', '3'], 'glue' => 'unknown:-'];
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('1-2-3', $result);
	}

	/**
	 * @test
	 */
	public function renderMethodCallsRenderChildrenIfContentIsNull() {
		$array = ['1', '2', '3'];
		$arguments = ['glue' => ','];
		$mock = $this->getMock($this->getViewHelperClassName(), ['renderChildren']);
		$mock->setArguments($arguments);
		$mock->expects($this->once())->method('renderChildren')->will($this->returnValue($array));
		$result = $mock->render();
		$this->assertEquals('1,2,3', $result);
	}

	/**
	 * @test
	 */
	public function renderMethodCallsRenderChildrenAndTemplateVariableContainerAddAndRemoveIfAsArgumentGiven() {
		$array = ['1', '2', '3'];
		$arguments = ['as' => 'test', 'content' => $array, 'glue' => ','];
		$mock = $this->getMock($this->getViewHelperClassName(), ['renderChildren']);
		$mock->expects($this->once())->method('renderChildren')->will($this->returnValue('test'));
		$mock->setArguments($arguments);
		$mockContainer = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TemplateVariableContainer', ['add', 'get', 'remove', 'exists']);
		$mockContainer->expects($this->once())->method('exists')->with('test')->will($this->returnValue(TRUE));
		$mockContainer->expects($this->exactly(2))->method('add')->with('test', '1,2,3');
		$mockContainer->expects($this->once())->method('get')->with('test')->will($this->returnValue($array));
		$mockContainer->expects($this->exactly(2))->method('remove')->with('test');
		ObjectAccess::setProperty($mock, 'templateVariableContainer', $mockContainer, TRUE);
		$mock->render();
	}

}
