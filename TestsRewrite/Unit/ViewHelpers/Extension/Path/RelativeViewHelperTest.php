<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension\Path;
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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Extension\Path
 */
class RelativeViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersUsingArgument() {
		$test = $this->executeViewHelper(array('extensionName' => 'Vhs'));
		$this->assertSame(ExtensionManagementUtility::extRelPath('vhs'), $test);
	}

	/**
	 * @test
	 */
	public function rendersUsingControllerContext() {
		$test = $this->executeViewHelper(array(), array(), NULL, 'Vhs');
		$this->assertSame(ExtensionManagementUtility::extRelPath('vhs'), $test);
	}

	/**
	 * @test
	 */
	public function throwsErrorWhenUnableToDetectExtensionName() {
		$this->setExpectedException('RuntimeException', NULL, 1364167519);
		$this->executeViewHelper();
	}

}
