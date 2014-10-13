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
 ***************************************************************/

use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

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
			$value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', $arguments, array('v' => $haystack));
			$this->assertEquals($value, $value2);
		}
		$this->assertEquals($value, $expectedValue);
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return array(
			array(array('haystack' => array(), 'length' => 0, 'start' => 0), array()),
			array(array('haystack' => array('foo', 'bar'), 'length' => 1, 'start' => 0), array('foo')),
			array(array('haystack' => array('foo', 'bar'), 'length' => 1, 'start' => 0, 'as' => 'variable'), array('foo')),
			array(array('haystack' => new \ArrayIterator(array('foo', 'bar')), 'start' => 1, 'length' => 1), array(1 => 'bar')),
			array(array('haystack' => new \ArrayIterator(array('foo', 'bar')), 'start' => 1, 'length' => 1, 'as' => 'variable'), array(1 => 'bar')),
		);
	}

	/**
	 * @test
	 * @dataProvider getErrorTestValues
	 * @param mixed $subject
	 */
	public function testThrowsErrorsOnInvalidSubjectType($subject) {
		$expected = 'Cannot slice unsupported type: ' . gettype($subject);
		$result = $this->executeViewHelper(array('haystack' => $subject));
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
