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
class RegularExpressionViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function canReplaceValues() {
		$arguments = array(
			'subject' => 'foo123bar',
			'return' => FALSE,
			'pattern' => '/[0-9]{3}/',
			'replacement' => 'baz',
		);
		$test = $this->executeViewHelper($arguments);
		$this->assertSame('foobazbar', $test);
	}

	/**
	 * @test
	 */
	public function canReturnMatches() {
		$arguments = array(
			'subject' => 'foo123bar',
			'return' => TRUE,
			'pattern' => '/[0-9]{3}/',
			'replacement' => 'baz',
		);
		$test = $this->executeViewHelper($arguments);
		$this->assertSame(array('123'), $test);
	}

	/**
	 * @test
	 */
	public function canTakeSubjectFromRenderChildren() {
		$arguments = array(
			'return' => TRUE,
			'pattern' => '/[0-9]{3}/',
			'replacement' => 'baz',
		);
		$test = $this->executeViewHelperUsingTagContent('Text', 'foo123bar', $arguments);
		$this->assertSame(array('123'), $test);
	}

}
