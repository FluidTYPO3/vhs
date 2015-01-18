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
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'foo123bar', 'pattern' => '/([0-9]+)/i')));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatched() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'foobar', 'pattern' => '/[0-9]+/i')));
	}

	/**
	 * @test
	 */
	public function rendersThenChildIfConditionMatchedAndGlobalEnabled() {
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'foo123bar', 'pattern' => '/([0-9]+)/i')));
	}

	/**
	 * @test
	 */
	public function rendersElseChildIfConditionNotMatchedAndGlobalEnabled() {
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'string' => 'foobar', 'pattern' => '/[0-9]+/i', 'global' => TRUE)));
	}

	/**
	 * @test
	 */
	public function rendersTagContentWhenConditionMatchedAndAsArgumentUsed() {
		$this->assertEquals('test', $this->executeViewHelperUsingTagContent('Text', 'test', array('string' => 'foo123bar', 'pattern' => '/[0-9]+/', 'global' => TRUE, 'as' => 'dummy'), array('dummy' => 'test')));
	}

}
