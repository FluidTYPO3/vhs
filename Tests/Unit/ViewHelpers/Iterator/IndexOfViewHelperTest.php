<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class IndexOfViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function returnsIndexOfElement() {
		$array = ['a', 'b', 'c'];
		$arguments = [
			'haystack' => $array,
			'needle' => 'c',
		];
		$output = $this->executeViewHelper($arguments);
		$this->assertEquals(2, $output);
	}

	/**
	 * @test
	 */
	public function returnsNegativeOneIfNeedleDoesNotExist() {
		$array = ['a', 'b', 'c'];
		$arguments = [
			'haystack' => $array,
			'needle' => 'd',
		];
		$output = $this->executeViewHelper($arguments);
		$this->assertEquals(-1, $output);
	}

}
