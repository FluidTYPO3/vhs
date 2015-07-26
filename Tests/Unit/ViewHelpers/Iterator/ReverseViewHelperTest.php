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
class ReverseViewHelperTest extends AbstractViewHelperTest {

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
		$queryResult = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult',
			['toArray', 'initialize', 'rewind', 'valid', 'count'], [], '', FALSE);
		$queryResult->expects($this->any())->method('toArray')->will($this->returnValue(['foo', 'bar']));
		$queryResult->expects($this->any())->method('valid')->will($this->returnValue(FALSE));
		$queryResult->expects($this->any())->method('count')->will($this->returnValue(1));
		return [
			[['subject' => []], []],
			[['subject' => ['foo', 'bar']], [1 => 'bar', 0 => 'foo']],
			[['subject' => ['foo', 'bar'], 'as' => 'variable'], [1 => 'bar', 0 => 'foo']],
			[['subject' => new \ArrayIterator(['foo', 'bar'])], [1 => 'bar', 0 => 'foo']],
			[['subject' => new \ArrayIterator(['foo', 'bar']), 'as' => 'variable'], [1 => 'bar', 0 => 'foo']]
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
		$this->assertEquals($expected, $result, $result);
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
