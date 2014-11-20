<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Context;

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

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class IsBackendViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function testIsBackendContext() {
		$instance = $this->createInstance();
		$result = $this->callInaccessibleMethod($instance, 'isBackendContext');
		$this->assertThat($result, new \PHPUnit_Framework_Constraint_IsType(\PHPUnit_Framework_Constraint_IsType::TYPE_BOOL));
	}

	/**
	 * @test
	 * @dataProvider getRenderTestValues
	 * @param boolean $verdict
	 * @param boolean $expected
	 */
	public function testRender($verdict, $expected) {
		$instance = $this->getMock(substr(get_class($this), 0, -4), array('isBackendContext'));
		$instance->expects($this->once())->method('isBackendContext')->will($this->returnValue($verdict));
		$arguments = array('then' => TRUE, 'else' => FALSE);
		$instance->setArguments($arguments);
		$result = $instance->render();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return array(
			array(FALSE, FALSE),
			array(TRUE, TRUE),
		);
	}

}
