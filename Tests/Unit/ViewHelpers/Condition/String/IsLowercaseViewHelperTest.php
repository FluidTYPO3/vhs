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
class IsLowercaseViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfFirstCharacterIsLowercase() {
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'foobar', 'fullString' => FALSE)));
	}

	/**
	 * @test
	 */
	public function rendersThenChildIfAllCharactersAreLowercase() {
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'foobar', 'fullString' => TRUE)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfFirstCharacterIsNotLowercase() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'FooBar', 'fullString' => FALSE)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfAllCharactersAreNotLowercase() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'fooBar', 'fullString' => TRUE)));
	}

}
