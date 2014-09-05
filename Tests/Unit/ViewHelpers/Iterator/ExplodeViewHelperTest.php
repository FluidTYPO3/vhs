<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class ExplodeViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function explodesString() {
		$arguments = array('content' => '1,2,3', 'glue' => ',');
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals(array('1', '2', '3'), $result);
	}

	/**
	 * @test
	 */
	public function supportsCustomGlue() {
		$arguments = array('content' => '1;2;3', 'glue' => ';');
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals(array('1', '2', '3'), $result);
	}

	/**
	 * @test
	 */
	public function supportsConstantsGlue() {
		$arguments = array('content' => "1\n2\n3", 'glue' => 'constant:LF');
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals(array('1', '2', '3'), $result);
	}

	/**
	 * @test
	 */
	public function passesThroughUnknownSpecialGlue() {
		$arguments = array('content' => '1-2-3', 'glue' => 'unknown:-');
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals(array('1', '2', '3'), $result);
	}

	/**
	 * @test
	 */
	public function renderMethodCallsRenderChildrenIfContentIsNull() {
		$array = array('1', '2', '3');
		$arguments = array('glue' => ',');
		$mock = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$mock->setArguments($arguments);
		$mock->expects($this->once())->method('renderChildren')->will($this->returnValue('1,2,3'));
		$result = $mock->render();
		$this->assertEquals($array, $result);
	}

	/**
	 * @test
	 */
	public function renderMethodCallsRenderChildrenAndTemplateVariableContainerAddAndRemoveIfAsArgumentGiven() {
		$array = array('1', '2', '3');
		$arguments = array('as' => 'test', 'content' => '1,2,3', 'glue' => ',');
		$mock = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$mock->expects($this->once())->method('renderChildren')->will($this->returnValue('test'));
		$mock->setArguments($arguments);
		$mockContainer = $this->getMock('Tx_Fluid_Core_ViewHelper_TemplateVariableContainer', array('add', 'get', 'remove', 'exists'));
		$mockContainer->expects($this->once())->method('exists')->with('test')->will($this->returnValue(TRUE));
		$mockContainer->expects($this->exactly(2))->method('add')->with('test', $array);
		$mockContainer->expects($this->once())->method('get')->with('test')->will($this->returnValue($array));
		$mockContainer->expects($this->exactly(2))->method('remove')->with('test');
		ObjectAccess::setProperty($mock, 'templateVariableContainer', $mockContainer, TRUE);
		$mock->render();
	}

}
