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
class SliceViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 * @dataProvider getRenderTestValues
	 * @param array $arguments
	 * @param mixed $expectedValue
	 */
	public function testRender(array $arguments, $expectedValue) {
		if (TRUE === isset($arguments['as'])) {
			$value = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'variable', $arguments);
		} else {
			$value = $this->executeViewHelper($arguments);
			$haystack = $arguments['haystack'];
			unset($arguments['haystack']);
			$value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', $arguments, ['v' => $haystack]);
			$this->assertEquals($value, $value2);
		}
		$this->assertEquals($value, $expectedValue);
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return [
			[['haystack' => [], 'length' => 0, 'start' => 0], []],
			[['haystack' => ['foo', 'bar'], 'length' => 1, 'start' => 0], ['foo']],
			[['haystack' => ['foo', 'bar'], 'length' => 1, 'start' => 0, 'as' => 'variable'], ['foo']],
			[['haystack' => new \ArrayIterator(['foo', 'bar']), 'start' => 1, 'length' => 1], [1 => 'bar']],
			[['haystack' => new \ArrayIterator(['foo', 'bar']), 'start' => 1, 'length' => 1, 'as' => 'variable'], [1 => 'bar']],
		];
	}

}
