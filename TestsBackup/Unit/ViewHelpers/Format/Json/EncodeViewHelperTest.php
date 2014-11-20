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

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 */
class EncodeViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function encodesDateTime() {
		$dateTime = \DateTime::createFromFormat('U', 86400);
		$instance = $this->createInstance();
		$test = $this->callInaccessibleMethod($instance, 'encodeValue', $dateTime, FALSE, TRUE, NULL, NULL);
		$this->assertEquals(86400000, $test);
	}

	/**
	 * @test
	 */
	public function encodesRecursiveDomainObject() {
		/** @var Foo $object */
		$object = $this->objectManager->get('FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo');
		$object->setFoo($object);
		$instance = $this->createInstance();
		$test = $this->callInaccessibleMethod($instance, 'encodeValue', $object, TRUE, TRUE, NULL, NULL);
		$this->assertEquals('{"bar":"baz","children":[],"foo":null,"pid":null,"uid":null}', $test);
	}

	/**
	 * @test
	 */
	public function encodesDateTimeWithFormat() {
		$dateTime = \DateTime::createFromFormat('U', 86401);
		$arguments = array(
			'value' => array(
				'date' => $dateTime,
			),
			'dateTimeFormat' => 'Y-m-d',
		);
		$test = $test = $this->executeViewHelper($arguments);
		$this->assertEquals('{"date":"1970-01-02"}', $test);
	}

	/**
	 * @test
	 */
	public function encodesTraversable() {
		$traversable = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\ObjectStorage');
		$instance = $this->createInstance();
		$test = $this->callInaccessibleMethod($instance, 'encodeValue', $traversable, FALSE, TRUE, NULL, NULL);
		$this->assertEquals('[]', $test);
	}

	/**
	 * @test
	 */
	public function returnsEmptyJsonObjectForEmptyArguments() {
		$viewHelper = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));

		$this->assertEquals('{}', $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsExpectedStringForProvidedArguments() {

		$storage = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\ObjectStorage');
		$fixture = array(
			'foo' => 'bar',
			'bar' => TRUE,
			'baz' => 1,
			'foobar' => NULL,
			'date' => \DateTime::createFromFormat('U', 3216548),
			'traversable' => $storage
		);

		$expected = '{"foo":"bar","bar":true,"baz":1,"foobar":null,"date":3216548000,"traversable":[]}';

		$viewHelper = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($fixture));

		$this->assertEquals($expected, $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function throwsExceptionForInvalidArgument() {
		$viewHelper = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue("\xB1\x31"));

		$this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception');
		$this->assertEquals('null', $viewHelper->render());
	}

	/**
	 * @test
	 */
	public function returnsJsConsumableTimestamps() {
		$date = new \DateTime('now');
		$jsTimestamp = $date->getTimestamp() * 1000;

		$fixture = array('foo' => $date, 'bar' => array('baz' => $date));
		$expected = sprintf('{"foo":%s,"bar":{"baz":%s}}', $jsTimestamp, $jsTimestamp);

		$viewHelper = $this->getMock($this->getViewHelperClassName(), array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($fixture));

		$this->assertEquals($expected, $viewHelper->render());
	}

}
