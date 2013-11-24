<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>
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
 * @author Claus Due <claus@wildside.dk>
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_Var_GetViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function returnsNullIfVariableDoesNotExist() {
		$this->executeViewHelper(array('name' => 'void', array()));
	}

	/**
	 * @test
	 */
	public function returnsDirectValueIfExists() {
		$this->assertEquals(1, $this->executeViewHelper(array('name' => 'test'), array('test' => 1)));
	}

	/**
	 * @test
	 */
	public function returnsNestedValueIfRootExists() {
		$this->assertEquals(1, $this->executeViewHelper(array('name' => 'test.test'), array('test' => array('test' => 1))));
	}

	/**
	 * @test
	 */
	public function returnsNestedValueIfRootExistsAndMembersAreNumeric() {
		$this->assertEquals(2, $this->executeViewHelper(array('name' => 'test.1'), array('test' => array(1, 2))));
	}

	/**
	 * @test
	 */
	public function returnsNullAndSuppressesExceptionOnInvalidPropertyGetting() {
		$user = $this->objectManager->get('\TYPO3\CMS\Extbase\Domain\Model\FrontendUser');
		$this->assertEquals(NULL, $this->executeViewHelper(array('name' => 'test.void'), array('test' => $user)));
	}

}
