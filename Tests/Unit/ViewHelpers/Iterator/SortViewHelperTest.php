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
class SortViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function throwsExceptionOnUnsupportedSortFlag() {
		$arguments = ['sortFlags' => 'FOOBAR'];
		$output = $this->executeViewHelperUsingTagContent('Array', ['a', 'b', 'c'], $arguments);
		$this->assertStringStartsWith('The constant "FOOBAR" you\'re trying to use as a sortFlag is not allowed.', $output);
	}

}
