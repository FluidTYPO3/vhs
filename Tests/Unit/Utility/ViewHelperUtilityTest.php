<?php
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
class Tx_Vhs_Utility_ViewHelperUtilityTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @test
	 */
	public function canConvertIteratorToArray() {
		$array = array('a', 'b', 'c');
		$iterator = new ArrayIterator($array);
		$result = Tx_Vhs_Utility_ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($iterator);
		$this->assertEquals($array, $result);
	}

	/**
	 * @test
	 */
	public function canConvertCsvToArray() {
		$string = 'a,b,c';
		$array = explode(',', $string);
		$result = Tx_Vhs_Utility_ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($string);
		$this->assertEquals($array, $result);
	}

	/**
	 * @test
	 */
	public function createsArrayFromUnsupportedValues() {
		$result = Tx_Vhs_Utility_ViewHelperUtility::arrayFromArrayOrTraversableOrCSV(FALSE);
		$this->assertEquals(array(FALSE), $result);
	}

	/**
	 * @test
	 */
	public function returnsSameArrayIfAlreadyArray() {
		$array = array('a', 'b', 'c');
		$result = Tx_Vhs_Utility_ViewHelperUtility::arrayFromArrayOrTraversableOrCSV($array);
		$this->assertEquals($array, $result);
	}

}
