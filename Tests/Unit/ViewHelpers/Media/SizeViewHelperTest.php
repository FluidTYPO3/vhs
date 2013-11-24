<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_Media_SizeViewHelperTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var string
	 */
	protected $fixturesPath;

	public function setUp() {
		$this->fixturesPath = 'EXT:vhs/Tests/Fixtures/Files';
	}

	/**
	 * @test
	 */
	public function returnsZeroForEmptyArguments() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_SizeViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));

		$this->assertEquals(0, $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsFileSizeAsInteger() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_SizeViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/typo3_logo.jpg'));

		$this->assertEquals(7094, $size = $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function throwsExceptionWhenFileNotFound() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_SizeViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('/this/path/hopefully/does/not/exist.txt'));

		$this->setExpectedException('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\Exception');
		$viewHelper->render();
	}

	/**
	 * @test
	 */
	public function throwsExceptionWhenFileIsNotAccessibleOrIsADirectory() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_SizeViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));

		$this->setExpectedException('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\Exception');
		$viewHelper->render();
	}

}
