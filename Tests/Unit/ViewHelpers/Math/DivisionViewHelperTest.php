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
class DivisionViewHelperTest extends AbstractMathViewHelperTest {

	/**
	 * @test
	 */
	public function testDualArgument() {
		$this->executeDualArgumentTest(4, 2, 2);
	}

	/**
	 * @test
	 */
	public function testDualArgumentIteratorFirst() {
		$this->executeDualArgumentTest([4, 8], 2, [2, 4]);
	}

	/**
	 * @test
	 */
	public function executeMissingArgumentTest() {
		$result = $this->executeViewHelper([]);
		$this->assertEquals('Required argument "b" was not supplied', $result);
	}

	/**
	 * @test
	 */
	public function executeInvalidFirstArgumentTypeTest() {
		$result = $this->executeViewHelper(['b' => 1, 'fail' => TRUE]);
		$this->assertEquals('Required argument "a" was not supplied', $result);
	}

	/**
	 * @test
	 */
	public function executeInvalidSecondArgumentTypeTest() {
		$result = $this->executeViewHelper(['a' => 1, 'b' => [1], 'fail' => TRUE]);
		$this->assertEquals('Math operation attempted using an iterator $b against a numeric value $a. Either both $a and $b, or only $a, must be array/Iterator', $result);
	}

}
