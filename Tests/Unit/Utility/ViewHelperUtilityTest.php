<?php
namespace FluidTYPO3\Vhs\Utility;
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

/**
 * @protection on
 * @package Vhs
 */
class ViewHelperUtilityTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function canConvertIteratorToArray() {
		$array = array('a', 'b', 'c');
		$iterator = new \ArrayIterator($array);
		$result = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($iterator);
		$this->assertEquals($array, $result);
	}

	/**
	 * @test
	 */
	public function canConvertCsvToArray() {
		$string = 'a,b,c';
		$array = explode(',', $string);
		$result = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($string);
		$this->assertEquals($array, $result);
	}

	/**
	 * @test
	 */
	public function createsArrayFromUnsupportedValues() {
		$result = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV(FALSE);
		$this->assertEquals(array(FALSE), $result);
	}

	/**
	 * @test
	 */
	public function returnsSameArrayIfAlreadyArray() {
		$array = array('a', 'b', 'c');
		$result = ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($array);
		$this->assertEquals($array, $result);
	}

}
