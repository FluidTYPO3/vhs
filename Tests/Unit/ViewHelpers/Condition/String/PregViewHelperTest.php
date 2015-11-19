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
class PregViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildIfConditionMatched() {
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'foo123bar',
			'pattern' => '/([0-9]+)/i'
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('then', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatched() {
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'foobar',
			'pattern' => '/[0-9]+/i'
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('else', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

	/**
	 * @test
	 */
	public function rendersThenChildIfConditionMatchedAndGlobalEnabled() {
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'foo123bar',
			'pattern' => '/([0-9]+)/i'
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('then', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatchedAndGlobalEnabled() {
		$arguments = array(
			'then' => 'then',
			'else' => 'else',
			'string' => 'foobar',
			'pattern' => '/[0-9]+/i',
			'global' => TRUE
		);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals('else', $result);

		$staticResult = $this->executeViewHelperStatic($arguments);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

	/**
	 * @test
	 */
	public function rendersTagContentWhenConditionMatchedAndAsArgumentUsed() {
		$arguments = array(
			'string' => 'foo123bar',
			'pattern' => '/[0-9]+/',
			'global' => TRUE, 'as' => 'dummy'
		);
		$variables = array('dummy' => 'test');
		$result = $this->executeViewHelperUsingTagContent('Text', 'test', $arguments, $variables);
		$this->assertEquals('test', $result);

		$staticResult = $this->executeViewHelperUsingTagContentStatic('Text', 'test', $arguments, $variables);
		$this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
	}

}
