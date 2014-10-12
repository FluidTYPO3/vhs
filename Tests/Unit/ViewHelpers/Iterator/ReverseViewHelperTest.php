<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

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
			$value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', array(), array('v' => $arguments['subject']));
			$this->assertEquals($value, $value2);
		}
		$this->assertEquals($value, $expectedValue);
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		$queryResult = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult',
			array('toArray', 'initialize', 'rewind', 'valid'), array(), '', FALSE);
		$queryResult->expects($this->any())->method('toArray')->will($this->returnValue(array('foo', 'bar')));
		$queryResult->expects($this->any())->method('valid')->will($this->returnValue(FALSE));
		return array(
			array(array('subject' => array()), array()),
			array(array('subject' => array('foo', 'bar')), array(1 => 'bar', 0 => 'foo')),
			array(array('subject' => array('foo', 'bar'), 'as' => 'variable'), array(1 => 'bar', 0 => 'foo')),
			array(array('subject' => new \ArrayIterator(array('foo', 'bar'))), array(1 => 'bar', 0 => 'foo')),
			array(array('subject' => new \ArrayIterator(array('foo', 'bar')), 'as' => 'variable'), array(1 => 'bar', 0 => 'foo')),
			array(array('subject' => $queryResult), array(1 => 'bar', 0 => 'foo')),
			array(array('subject' => $queryResult, 'as' => 'variable'), array(1 => 'bar', 0 => 'foo'))
		);
	}

	/**
	 * @test
	 * @dataProvider getErrorTestValues
	 * @param mixed $subject
	 */
	public function testThrowsErrorsOnInvalidSubjectType($subject) {
		$expected = 'Invalid variable type passed to Iterator/ReverseViewHelper. Expected any of Array, QueryResult, ' .
			'ObjectStorage or Iterator implementation but got ' . gettype($subject);
		$result = $this->executeViewHelper(array('subject' => $subject));
		$this->assertEquals($expected, $result, $result);
	}

	/**
	 * @return array
	 */
	public function getErrorTestValues() {
		return array(
			array(new \DateTime()),
			array('invalid'),
			array(new \stdClass()),
		);
	}

}
