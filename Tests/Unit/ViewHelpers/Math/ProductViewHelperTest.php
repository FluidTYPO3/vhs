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
class ProductViewHelperTest extends AbstractMathViewHelperTest {

	/**
	 * @test
	 */
	public function testSingleArgumentIterator() {
		$this->executeSingleArgumentTest(array(2, 8), 16);
	}

	/**
	 * @test
	 */
	public function testDualArguments() {
		$this->executeDualArgumentTest(8, 2, 16);
	}

}
