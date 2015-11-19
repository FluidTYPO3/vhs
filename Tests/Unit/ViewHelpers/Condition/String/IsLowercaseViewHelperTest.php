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
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'foobar',
			'fullString' => FALSE
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('then', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

	/**
	 * @test
	 */
	public function rendersThenChildIfAllCharactersAreLowercase() {
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'foobar',
			'fullString' => TRUE
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('then', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfFirstCharacterIsNotLowercase() {
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'FooBar',
			'fullString' => FALSE
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('else', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfAllCharactersAreNotLowercase() {
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'fooBar',
			'fullString' => TRUE
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('else', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

}
