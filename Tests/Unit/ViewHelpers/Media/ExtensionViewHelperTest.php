<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;
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
use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

/**
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class ExtensionViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @var string
	 */
	protected $fixturesPath;

	/**
	 * Setup
	 */
	public function setUp() {
		$this->fixturesPath = 'EXT:vhs/Tests/Fixtures/Files';
	}

	/**
	 * @test
	 */
	public function returnsEmptyStringForEmptyArguments() {
		$viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\ExtensionViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$this->assertEquals('', $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsExpectedExtensionForProvidedPath() {
		$viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\ExtensionViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/foo.txt'));
		$this->assertEquals('txt', $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsEmptyStringForFileWithoutExtension() {
		$viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\ExtensionViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/noext'));
		$this->assertEquals('', $viewHelper->render());
	}
}
