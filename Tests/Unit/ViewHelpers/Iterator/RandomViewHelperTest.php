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
class RandomViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 * @dataProvider getRenderTestValues
	 * @param array $arguments
	 * @param array $asArray
	 */
	public function testRender(array $arguments, array $asArray) {
		if (TRUE === isset($arguments['as'])) {
			$value = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'variable', $arguments);
		} else {
			$value = $this->executeViewHelper($arguments);
			$value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', [], ['v' => $arguments['subject']]);
			if (NULL !== $value2) {
				$this->assertContains($value2, $asArray);
			}
		}
		if (NULL !== $value) {
			$this->assertContains($value, $asArray);
		} else {
			$this->assertNull($value);
		}
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		$queryResult = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult',
			['toArray', 'initialize', 'rewind', 'valid', 'count'], [], '', FALSE);
		$queryResult->expects($this->any())->method('toArray')->will($this->returnValue(['foo', 'bar']));
		$queryResult->expects($this->any())->method('count')->will($this->returnValue(0));
		$queryResult->expects($this->any())->method('valid')->will($this->returnValue(FALSE));
		return [
			[['subject' => ['foo', 'bar']], ['foo', 'bar']],
			[['subject' => ['foo', 'bar'], 'as' => 'variable'], ['foo', 'bar']],
			[['subject' => new \ArrayIterator(['foo', 'bar'])], ['foo', 'bar']],
			[['subject' => new \ArrayIterator(['foo', 'bar']), 'as' => 'variable'], ['foo', 'bar']],
			[['subject' => $queryResult], ['foo', 'bar']],
			[['subject' => $queryResult, 'as' => 'variable'], ['foo', 'bar']]
		];
	}

}
