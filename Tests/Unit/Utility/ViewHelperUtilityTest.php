<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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

	/**
	 * @test
	 */
	public function mergesArraysAndOverridesValuesWithIdenticalKeys() {
		$array1 = array('a' => 'a', 'b' => 'b', 'c' => 'c');
		$array2 = array('d' => 'd', 'e' => 'e', 'f' => 'f');
		$result = ViewHelperUtility::mergeArrays($array1, $array2);
		$this->assertCount(6, $result);

		$array1 = array('a' => 'a', 'b' => 'b', 'c' => 'c', 'd' => 'd');
		$array2 = array('a' => 'd', 'b' => 'b', 'c' => 'f', 'e' => 'e');
		$result = ViewHelperUtility::mergeArrays($array1, $array2);
		$this->assertEquals(array('a' => 'd', 'b' => 'b', 'c' => 'f', 'd' => 'd', 'e' => 'e'), $result);
	}

}
