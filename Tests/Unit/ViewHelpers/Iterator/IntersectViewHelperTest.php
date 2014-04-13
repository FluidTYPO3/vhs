<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_Iterator_IntersectViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function intersectTest() {
		$array1 = array('a' => 'green', 'red', 'blue');
		$array2 = array('b' => 'green', 'yellow', 'red');
		$arguments = array('a' => $array1, 'b' => $array2);
		$result = $this->executeViewHelper($arguments);
		$this->assertEquals(array('a' => 'green', 0 => 'red'), $result);
	}

	/**
	 * @test
	 */
	public function intersectTestWithTagContent() {
		$array1 = array('a' => 'green', 'red', 'blue');
		$array2 = array('b' => 'green', 'yellow', 'red');
		$arguments = array('b' => $array2);
		$result = $this->executeViewHelperUsingTagContent('Array', $array1, $arguments);
		$this->assertEquals(array('a' => 'green', 0 => 'red'), $result);
	}

}
