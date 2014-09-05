<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

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
