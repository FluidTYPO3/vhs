<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Json;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

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
		$viewHelper = $this->getMock($this->getViewHelperClassName(), ['renderChildren']);
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(''));

		$this->assertNull($viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsExpectedValueForProvidedArguments() {

		$fixture = '{"foo":"bar","bar":true,"baz":1,"foobar":null}';

		$expected = [
			'foo' => 'bar',
			'bar' => TRUE,
			'baz' => 1,
			'foobar' => NULL,
		];

		$viewHelper = $this->getMock($this->getViewHelperClassName(), ['renderChildren']);
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($fixture));

		$this->assertEquals($expected, $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function throwsExceptionForInvalidArgument() {
		$invalidJson = "{'foo': 'bar'}";

		$viewHelper = $this->getMock($this->getViewHelperClassName(), ['renderChildren']);
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($invalidJson));

		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception');
		$this->assertEquals('null', $viewHelper->render());
	}
}
