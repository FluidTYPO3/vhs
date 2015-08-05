<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\String;

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
class ContainsViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfConditionMatched() {
		$this->assertEquals('then', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'haystack' => 'foobar', 'needle' => 'bar']));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatched() {
		$this->assertEquals('else', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'haystack' => 'foobar', 'needle' => 'baz']));
	}

}
