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
 * @protection on
 * @author Claus Due <claus@wildside.dk>
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_Extension_Path_AbsoluteViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersUsingArgument() {
		$test = $this->executeViewHelper(array('extensionName' => 'Vhs'));
		$this->assertSame(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('vhs'), $test);
	}

	/**
	 * @test
	 */
	public function rendersUsingControllerContext() {
		$test = $this->executeViewHelper(array(), array(), NULL, 'Vhs');
		$this->assertSame(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('vhs'), $test);
	}

	/**
	 * @test
	 */
	public function throwsErrorWhenUnableToDetectExtensionName() {
		$this->setExpectedException('RuntimeException', NULL, 1364167519);
		$this->executeViewHelper();
	}

}
