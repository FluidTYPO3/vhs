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
class Tx_Vhs_ViewHelpers_Var_ConvertViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

	/**
	 * @param mixed $value
	 * @param string $type
	 * @param mixed $expected
	 * @return void
	 */
	protected function executeConversion($value, $type, $expected) {
		$this->assertEquals($expected, $this->executeViewHelper(array('value' => $value, 'type' => $type)));
	}

	/**
	 * @test
	 */
	public function throwsRuntimeExceptionIfTypeOfDefaultValueIsUnsupported() {
		$this->setExpectedException('RuntimeException', NULL, 1364542576);
		$this->executeViewHelper(array('type' => 'foobar', 'value' => NULL, 'default' => '1'));
	}

	/**
	 * @test
	 */
	public function throwsRuntimeExceptionIfTypeIsUnsupportedAndNoDefaultProvided() {
		$this->setExpectedException('RuntimeException', NULL, 1364542884);
		$this->executeViewHelper(array('type' => 'unsupported', 'value' => NULL));
	}

	/**
	 * @test
	 */
	public function throwsRuntimeExceptionIfTypeOfDefaultIsNotSameAsType() {
		$this->setExpectedException('RuntimeException', NULL, 1364542576);
		$this->executeViewHelper(array('type' => 'ObjectStorage', 'value' => NULL, 'default' => '1'));
	}

	/**
	 * @test
	 */
	public function convertNullToString() {
		$this->executeConversion(NULL, 'string', '');
	}

	/**
	 * @test
	 */
	public function convertNullToInteger() {
		$this->executeConversion(NULL, 'integer', 0);
	}

	/**
	 * @test
	 */
	public function convertNullToFloat() {
		$this->executeConversion(NULL, 'float', 0);
	}

	/**
	 * @test
	 */
	public function convertStringToString() {
		$this->executeConversion('1', 'string', '1');
	}

	/**
	 * @test
	 */
	public function convertStringToInteger() {
		$this->executeConversion('1', 'integer', 1);
	}

	/**
	 * @test
	 */
	public function convertArrayToObjectStorage() {
		$dummy = $this->objectManager->get('Tx_Extbase_Domain_Model_FrontendUser');
		$storage = $this->objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
		$storage->attach($dummy);
		$this->executeConversion(array($dummy), 'ObjectStorage', $storage);
	}

	/**
	 * @test
	 */
	public function convertObjectStorageToArray() {
		$dummy = $this->objectManager->get('Tx_Extbase_Domain_Model_FrontendUser');
		$storage = $this->objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
		$storage->attach($dummy);
		$this->executeConversion($storage, 'array', array($dummy));
	}

	/**
	 * @test
	 */
	public function returnsEmptyStringForTypeStringAndValueNull() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'string'));
		$converted = $viewHelper->render();
		$this->assertEquals('', $converted);
	}

	/**
	 * @test
	 */
	public function returnsStringForTypeStringAndValueInteger() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(12345));
		$viewHelper->setArguments(array('type' => 'string'));
		$converted = $viewHelper->render();
		$this->assertInternalType('string', $converted);
	}

	/**
	 * @test
	 */
	public function returnsArrayForTypeArrayAndValueNull() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'array'));
		$converted = $viewHelper->render();
		$this->assertInternalType('array', $converted);
	}

	/**
	 * @test
	 */
	public function returnsArrayForTypeArrayAndValueString() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('foo'));
		$viewHelper->setArguments(array('type' => 'array'));
		$converted = $viewHelper->render();
		$this->assertInternalType('array', $converted);
		$this->assertEquals(array('foo'), $converted);
	}

	/**
	 * @test
	 */
	public function returnsObjectStorageForTypeArrayAndValueNull() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'ObjectStorage'));
		$converted = $viewHelper->render();
		$this->assertInstanceOf('Tx_Extbase_Persistence_ObjectStorage', $converted);
		$this->assertEquals(0, $converted->count());
	}

	/**
	 * @test
	 */
	public function returnsArrayForTypeObjectStorage() {
		$domainObject = $this->objectManager->get('Tx_Vhs_Tests_Fixtures_Domain_Model_Foo');
		$storage = $this->objectManager->get('Tx_Extbase_Persistence_ObjectStorage');
		$storage->attach($domainObject);
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($storage));
		$viewHelper->setArguments(array('type' => 'array'));
		$converted = $viewHelper->render();
		$this->assertInternalType('array', $converted);
		$this->assertEquals(1, count($converted));
	}

	/**
	 * @test
	 */
	public function returnsBooleanForTypeBooleanAndValueNull() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'boolean'));
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertFalse($converted);
	}

	/**
	 * @test
	 */
	public function returnsBooleanForTypeBooleanAndValueInteger() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(1));
		$viewHelper->setArguments(array('type' => 'boolean'));
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertTrue($converted);
	}

	/**
	 * @test
	 */
	public function returnsBooleanForTypeBooleanAndValueString() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('foo'));
		$viewHelper->setArguments(array('type' => 'boolean'));
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertTrue($converted);
	}

	/**
	 * @test
	 */
	public function returnsExpectedDefaultValue() {
		$viewHelper = $this->getAccessibleMock('Tx_Vhs_ViewHelpers_Var_ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'boolean', 'default' => TRUE));
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertTrue($converted);
	}
}
