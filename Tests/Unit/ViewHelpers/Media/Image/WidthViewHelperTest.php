<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media\Image;
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

/**
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Media\Image
 */
class WidthViewHelperTest extends AbstractViewHelperTest {

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
	public function returnsZeroForEmptyArguments() {
		$viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\WidthViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));

		$this->assertEquals(0, $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsFileWidthAsInteger() {
		$viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\WidthViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/typo3_logo.jpg'));

		$this->assertEquals(385, $width = $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function throwsExceptionWhenFileNotFound() {
		$viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\WidthViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('/this/path/hopefully/does/not/exist.txt'));

		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception');
		$viewHelper->render();
	}

	/**
	 * @test
	 */
	public function throwsExceptionWhenFileIsNotAccessibleOrIsADirectory() {
		$viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\WidthViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));

		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception');
		$viewHelper->render();
	}

}
