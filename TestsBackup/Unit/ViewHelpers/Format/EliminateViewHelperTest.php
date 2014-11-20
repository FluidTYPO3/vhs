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
class EliminateViewHelperTest extends AbstractViewHelperTest {

	protected $arguments = array(
		'caseSensitive' => TRUE,
		'characters' => NULL,
		'strings' => NULL,
		'whitespace' => FALSE,
		'tabs' => FALSE,
		'unixBreaks' => FALSE,
		'windowsBreaks' => FALSE,
		'digits' => FALSE,
		'letters' => FALSE,
		'nonAscii' => FALSE
	);

	/**
	 * @test
	 */
	public function removesNonAscii() {
		$arguments = $this->arguments;
		$arguments['nonAscii'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', 'fooøæåbar', $arguments);
		$this->assertSame('foobar', $test);
	}

	/**
	 * @test
	 */
	public function removesLetters() {
		$arguments = $this->arguments;
		$arguments['letters'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', 'foo123bar', $arguments);
		$this->assertSame('123', $test);
	}

	/**
	 * @test
	 */
	public function removesLettersRespectsCaseSensitive() {
		$arguments = $this->arguments;
		$arguments['letters'] = TRUE;
		$arguments['caseSensitive'] = FALSE;
		$test = $this->executeViewHelperUsingTagContent('Text', 'FOO123bar', $arguments);
		$this->assertSame('123', $test);
	}

	/**
	 * @test
	 */
	public function removesDigits() {
		$arguments = $this->arguments;
		$arguments['digits'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', 'foo123bar', $arguments);
		$this->assertSame('foobar', $test);
	}

	/**
	 * @test
	 */
	public function removesWindowsCarriageReturns() {
		$arguments = $this->arguments;
		$arguments['windowsBreaks'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', "breaks\rbreaks", $arguments);
		$this->assertSame('breaksbreaks', $test);
	}

	/**
	 * @test
	 */
	public function removesUnixBreaks() {
		$arguments = $this->arguments;
		$arguments['unixBreaks'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', "breaks\nbreaks", $arguments);
		$this->assertSame('breaksbreaks', $test);
	}

	/**
	 * @test
	 */
	public function removesTabs() {
		$arguments = $this->arguments;
		$arguments['tabs'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', 'tabs	tabs', $arguments);
		$this->assertSame('tabstabs', $test);
	}

	/**
	 * @test
	 */
	public function removesWhitespace() {
		$arguments = $this->arguments;
		$arguments['whitespace'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', ' trimmed ', $arguments);
		$this->assertSame('trimmed', $test);
	}

	/**
	 * @test
	 */
	public function removesCharactersRespectsCaseSensitive() {
		$arguments = $this->arguments;
		$arguments['characters'] = 'abc';
		$arguments['caseSensitive'] = FALSE;
		$result = $this->executeViewHelperUsingTagContent('Text', 'ABCdef', $arguments);
		$this->assertSame('def', $result);
	}

	/**
	 * @test
	 */
	public function removesCharactersAsString() {
		$arguments = $this->arguments;
		$arguments['characters'] = 'abc';
		$result = $this->executeViewHelperUsingTagContent('Text', 'abcdef', $arguments);
		$this->assertSame('def', $result);
	}

	/**
	 * @test
	 */
	public function removesCharactersAsArray() {
		$arguments = $this->arguments;
		$arguments['characters'] = array('a', 'b', 'c');
		$result = $this->executeViewHelperUsingTagContent('Text', 'abcdef', $arguments);
		$this->assertSame('def', $result);
	}

	/**
	 * @test
	 */
	public function removesStringsRespectsCaseSensitive() {
		$arguments = $this->arguments;
		$arguments['strings'] = 'abc,def,ghi';
		$arguments['caseSensitive'] = FALSE;
		$result = $this->executeViewHelperUsingTagContent('Text', 'aBcDeFgHijkl', $arguments);
		$this->assertSame('jkl', $result);
	}

	/**
	 * @test
	 */
	public function removesStringsAsString() {
		$arguments = $this->arguments;
		$arguments['strings'] = 'abc,def,ghi';
		$result = $this->executeViewHelperUsingTagContent('Text', 'abcdefghijkl', $arguments);
		$this->assertSame('jkl', $result);
	}

	/**
	 * @test
	 */
	public function removesStringsAsArray() {
		$arguments = $this->arguments;
		$arguments['strings'] = array('abc', 'def', 'ghi');
		$result = $this->executeViewHelperUsingTagContent('Text', 'abcdefghijkl', $arguments);
		$this->assertSame('jkl', $result);
	}

}
