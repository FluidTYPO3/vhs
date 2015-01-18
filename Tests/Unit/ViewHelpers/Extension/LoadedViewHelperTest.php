<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Extension;

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
 * @subpackage ViewHelpers\Extension
 */
class LoadedViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfExtensionIsLoaded() {
		$test = $this->executeViewHelper(array('extensionName' => 'Vhs', 'then' => 1, 'else' => 0));
		$this->assertSame(1, $test);
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfExtensionIsNotLoaded() {
		$test = $this->executeViewHelper(array('extensionName' => 'Void', 'then' => 1, 'else' => 0));
		$this->assertSame(0, $test);
	}

}
