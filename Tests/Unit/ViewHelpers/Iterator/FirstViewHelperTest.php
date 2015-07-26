<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

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
class FirstViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function returnsFirstElement() {
		$array = ['a', 'b', 'c'];
		$arguments = [
			'haystack' => $array
		];
		$output = $this->executeViewHelper($arguments);
		$this->assertEquals('a', $output);
	}

	/**
	 * @test
	 */
	public function supportsIterators() {
		$array = new \ArrayIterator(['a', 'b', 'c']);
		$arguments = [
			'haystack' => $array
		];
		$output = $this->executeViewHelper($arguments);
		$this->assertEquals('a', $output);
	}

	/**
	 * @test
	 */
	public function supportsTagContent() {
		$array = ['a', 'b', 'c'];
		$arguments = [
			'haystack' => NULL
		];
		$output = $this->executeViewHelperUsingTagContent('Array', $array, $arguments);
		$this->assertEquals('a', $output);
	}

	/**
	 * @test
	 */
	public function returnsNullIfHaystackIsNull() {
		$arguments = [
			'haystack' => NULL
		];
		$output = $this->executeViewHelper($arguments);
		$this->assertEquals(NULL, $output);
	}

	/**
	 * @test
	 */
	public function returnsNullIfHaystackIsEmptyArray() {
		$arguments = [
			'haystack' => []
		];
		$output = $this->executeViewHelper($arguments);
		$this->assertEquals(NULL, $output);
	}

	/**
	 * @test
	 */
	public function throwsExceptionOnUnsupportedHaystacks() {
		$arguments = [
			'haystack' => new \DateTime('now')
		];
		$output = $this->executeViewHelper($arguments);
		$this->assertStringStartsWith('Invalid argument supplied to Iterator/FirstViewHelper - expected array, Iterator or NULL but got', $output);
	}

}
