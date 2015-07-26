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
class PopViewHelperTest extends AbstractViewHelperTest {

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
			$value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', [], ['v' => $arguments['subject']]);
			$this->assertEquals($value, $value2);
		}
		$this->assertEquals($value, $expectedValue);
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return [
			[['subject' => []], NULL],
			[['subject' => ['foo', 'bar']], 'bar'],
			[['subject' => ['foo', 'bar'], 'as' => 'variable'], 'bar'],
			[['subject' => new \ArrayIterator(['foo', 'bar'])], 'bar'],
			[['subject' => new \ArrayIterator(['foo', 'bar']), 'as' => 'variable'], 'bar'],
		];
	}

	/**
	 * @test
	 * @dataProvider getErrorTestValues
	 * @param mixed $subject
	 */
	public function testThrowsErrorsOnInvalidSubjectType($subject) {
		$expected = 'Unsupported input type; cannot convert to array!';
		$result = $this->executeViewHelper(['subject' => $subject]);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getErrorTestValues() {
		return [
			[0],
			[NULL],
			[new \DateTime()],
			[new \stdClass()],
		];
	}

}
