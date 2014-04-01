<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
class Tx_Vhs_ViewHelpers_Media_FilesViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @var string
	 */
	protected $fixturesPath;

	/**
	 * Setup
	 */
	public function setUp() {
		$this->fixturesPath = dirname(__FILE__) . '/../../../Fixtures/Files';
	}

	/**
	 * @test
	 */
	public function returnsEmtpyArrayWhenArgumentsAreNotSet() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_FilesViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));

		$this->assertEquals(array(), $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsEmptyArrayWhenPathIsInaccessible() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_FilesViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('/this/path/hopefully/does/not/exist'));

		$this->assertEquals(array(), $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsPopulatedArrayOfAllFoundFiles() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_FilesViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));
		$actualFiles = glob($this->fixturesPath . '/*');
		$actualFilesCount = count($actualFiles);

		$this->assertCount($actualFilesCount, $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsPopulatedArrayOfFilteredFiles() {
		$viewHelper = $this->getMock('Tx_Vhs_ViewHelpers_Media_FilesViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));
		$viewHelper->setArguments(array('extensionList' => 'txt'));
		$actualFiles = glob($this->fixturesPath . '/*.txt');
		$actualFilesCount = count($actualFiles);

		$this->assertCount($actualFilesCount, $viewHelper->render());
	}

}
