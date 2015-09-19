<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Context;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class IsFrontendViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function testIsFrontendContext() {
		$instance = $this->createInstance();
		$result = $this->callInaccessibleMethod($instance, 'isFrontendContext');
		$this->assertThat($result, new \PHPUnit_Framework_Constraint_IsType(\PHPUnit_Framework_Constraint_IsType::TYPE_BOOL));
	}

	/**
	 * @test
	 * @dataProvider getRenderTestValues
	 * @param boolean $verdict
	 * @param boolean $expected
	 */
	public function testRender($verdict, $expected) {
		$instance = $this->getMock($this->getViewHelperClassName(), array('isFrontendContext'));
		$instance->expects($this->once())->method('isFrontendContext')->will($this->returnValue($verdict));
		$arguments = array('then' => TRUE, 'else' => FALSE);
		$result = $this->executeViewHelper($arguments);
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
