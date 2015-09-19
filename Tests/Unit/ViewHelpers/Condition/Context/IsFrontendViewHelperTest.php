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
		$result = $this->callInaccessibleMethod($instance, 'evaluateCondition');
		$this->assertThat($result, new \PHPUnit_Framework_Constraint_IsType(\PHPUnit_Framework_Constraint_IsType::TYPE_BOOL));
	}

	/**
	 * @test
	 */
	public function testRender() {
		$arguments = array('then' => TRUE, 'else' => FALSE);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals(FALSE, $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

}
