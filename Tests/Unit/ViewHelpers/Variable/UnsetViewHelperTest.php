<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class UnsetViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function canUnsetVariable() {
		$variables = new \ArrayObject(['test' => TRUE]);
		$this->executeViewHelper(['name' => 'test'], $variables);
		$this->assertNotContains('test', $variables);
	}

}
