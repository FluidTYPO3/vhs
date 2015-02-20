<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class MaximumViewHelperTest extends AbstractMathViewHelperTest {

	/**
	 * @test
	 */
	public function testSingleArgument() {
		$this->executeSingleArgumentTest(array(1, 3), 3);
	}

	/**
	 * @test
	 */
	public function testDualArgument() {
		$this->executeDualArgumentTest(4, 2, 4);
	}

	/**
	 * @test
	 */
	public function testDualArgumentBothIterators() {
		$this->executeDualArgumentTest(array(4, 8), array(8, 8), array(8, 8));
	}

	/**
	 * @test
	 */
	public function executeMissingArgumentTest() {
		$result = $this->executeViewHelper(array());
		$this->assertEquals('Required argument "b" was not supplied', $result);
	}

	/**
	 * @test
	 */
	public function executeInvalidArgumentTypeTest() {
		$result = $this->executeViewHelper(array('b' => 1, 'fail' => TRUE));
		$this->assertEquals('Required argument "a" was not supplied', $result);
	}

}
