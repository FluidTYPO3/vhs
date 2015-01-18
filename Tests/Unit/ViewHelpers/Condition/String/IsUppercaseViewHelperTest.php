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
class IsUppercaseViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfFirstCharacterIsUppercase() {
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'Foobar', 'fullString' => FALSE)));
	}

	/**
	 * @test
	 */
	public function rendersThenChildIfAllCharactersAreUppercase() {
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'FOOBAR', 'fullString' => TRUE)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfFirstCharacterIsNotUppercase() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'fooBar', 'fullString' => FALSE)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfAllCharactersAreNotUppercase() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'FooBar', 'fullString' => TRUE)));
	}

}
