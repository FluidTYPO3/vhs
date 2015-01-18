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
		$this->executeDualArgumentTest(array(4, 8), 2, array(2, 4));
	}

}
