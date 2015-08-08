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
class EliminateViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @var array
	 */
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
	public function removesWhitespaceBetweenHtmlTags() {
		$arguments = $this->arguments;
		$arguments['whitespaceBetweenHtmlTags'] = TRUE;
		$test = $this->executeViewHelperUsingTagContent('Text', ' <p> Foo </p> <p> Bar </p> ', $arguments);
		$this->assertSame('<p> Foo </p><p> Bar </p>', $test);
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
