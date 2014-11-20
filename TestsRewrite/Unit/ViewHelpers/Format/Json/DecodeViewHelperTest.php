<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Json;
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
 * @protection on
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 */
class DecodeViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function returnsNullForEmptyArguments() {
		$viewHelper = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(''));

		$this->assertNull($viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsExpectedValueForProvidedArguments() {

		$fixture = '{"foo":"bar","bar":true,"baz":1,"foobar":null}';

		$expected = array(
			'foo' => 'bar',
			'bar' => TRUE,
			'baz' => 1,
			'foobar' => NULL,
		);

		$viewHelper = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($fixture));

		$this->assertEquals($expected, $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function throwsExceptionForInvalidArgument() {
		$invalidJson = "{'foo': 'bar'}";

		$viewHelper = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($invalidJson));

		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception');
		$this->assertEquals('null', $viewHelper->render());
	}
}
