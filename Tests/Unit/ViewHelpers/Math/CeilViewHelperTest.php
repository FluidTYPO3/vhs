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
class CeilViewHelperTest extends AbstractMathViewHelperTest {

	/**
	 * @test
	 */
	public function testSingleArgument() {
		$this->executeSingleArgumentTest(0.5, 1);
	}

	/**
	 * @test
	 */
	public function testSingleArgumentIteratorFirst() {
		$this->executeSingleArgumentTest([0.5, 0.8], [1, 1]);
	}

}
