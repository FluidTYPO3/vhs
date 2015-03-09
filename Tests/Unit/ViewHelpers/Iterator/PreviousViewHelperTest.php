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
class PreviousViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function returnsPreviousElement() {
		$array = array('a', 'b', 'c');
		next($array);
		$arguments = array(
			'haystack' => $array,
			'needle' => 'c',
		);
		$output = $this->executeViewHelper($arguments);
		$this->assertEquals('b', $output);
	}

}
