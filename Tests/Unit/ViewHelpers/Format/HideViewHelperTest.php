<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

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
class HideViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function hidesTagContent() {
		$test = $this->executeViewHelperUsingTagContent('Text', 'this is hidden');
		$this->assertNull($test);
	}

	/**
	 * @test
	 */
	public function canBeDisabled() {
		$test = $this->executeViewHelperUsingTagContent('Text', 'this is shown', array('disabled' => TRUE));
		$this->assertSame('this is shown', $test);
	}

}
