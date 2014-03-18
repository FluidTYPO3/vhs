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
 * ************************************************************* */

/**
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_If_Iterator_ContainsViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function supportsArray() {
		$needle = 'test';
		$with = array($needle);
		$without = array();
		$this->renderTestWithHaystackAndNeedle($with, $without, $needle);
	}

	/**
	 * @test
	 */
	public function supportsObjectStorage() {
		$needle = new Tx_Extbase_Domain_Model_FrontendUser();
		\TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($needle, 'uid', 1);
		$with = new Tx_Extbase_Persistence_ObjectStorage();
		$without = new Tx_Extbase_Persistence_ObjectStorage();
		$with->attach($needle);
		$this->renderTestWithHaystackAndNeedle($with, $without, $needle);
	}

	/**
	 * @param mixed $haystackWithNeedle
	 * @param mixed $haystackWithoutNeedle
	 * @param mixed $needle
	 */
	protected function renderTestWithHaystackAndNeedle($haystackWithNeedle, $haystackWithoutNeedle, $needle) {
		$arguments = array(
			'haystack' => $haystackWithNeedle,
			'needle' => $needle,
			'then' => 'then',
			'else' => 'else'
		);
		$this->assertEquals($arguments['then'], $this->executeViewHelper($arguments));
		$arguments['haystack'] = $haystackWithoutNeedle;
		$this->assertEquals($arguments['else'], $this->executeViewHelper($arguments));
	}

}
