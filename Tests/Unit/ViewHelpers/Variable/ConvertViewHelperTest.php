<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 */
class ConvertViewHelperTest extends AbstractViewHelperTest {

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
		$dummy = new Foo();
		$storage = new ObjectStorage();
		$storage->attach($dummy);
		$this->executeConversion(array($dummy), 'ObjectStorage', $storage);
	}

	/**
	 * @test
	 */
	public function convertObjectStorageToArray() {
		$dummy = new Foo();
		$storage = new ObjectStorage();
		$storage->attach($dummy);
		$this->executeConversion($storage, 'array', array($dummy));
	}

	/**
	 * @test
	 */
	public function returnsEmptyStringForTypeStringAndValueNull() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'string'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertEquals('', $converted);
	}

	/**
	 * @test
	 */
	public function returnsStringForTypeStringAndValueInteger() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(12345));
		$viewHelper->setArguments(array('type' => 'string'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('string', $converted);
	}

	/**
	 * @test
	 */
	public function returnsArrayForTypeArrayAndValueNull() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'array'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('array', $converted);
	}

	/**
	 * @test
	 */
	public function returnsArrayForTypeArrayAndValueString() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('foo'));
		$viewHelper->setArguments(array('type' => 'array'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('array', $converted);
		$this->assertEquals(array('foo'), $converted);
	}

	/**
	 * @test
	 */
	public function returnsObjectStorageForTypeArrayAndValueNull() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'ObjectStorage'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInstanceOf('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', $converted);
		$this->assertEquals(0, $converted->count());
	}

	/**
	 * @test
	 */
	public function returnsArrayForTypeObjectStorage() {
		$domainObject = new Foo();
		$storage = new ObjectStorage();
		$storage->attach($domainObject);
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($storage));
		$viewHelper->setArguments(array('type' => 'array'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('array', $converted);
		$this->assertEquals(1, count($converted));
	}

	/**
	 * @test
	 */
	public function returnsBooleanForTypeBooleanAndValueNull() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'boolean'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertFalse($converted);
	}

	/**
	 * @test
	 */
	public function returnsBooleanForTypeBooleanAndValueInteger() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(1));
		$viewHelper->setArguments(array('type' => 'boolean'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertTrue($converted);
	}

	/**
	 * @test
	 */
	public function returnsBooleanForTypeBooleanAndValueString() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('foo'));
		$viewHelper->setArguments(array('type' => 'boolean'));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertTrue($converted);
	}

	/**
	 * @test
	 */
	public function returnsExpectedDefaultValue() {
		$viewHelper = $this->getAccessibleMock('FluidTYPO3\Vhs\ViewHelpers\Variable\ConvertViewHelper', array('renderChildren'));
		$viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(NULL));
		$viewHelper->setArguments(array('type' => 'boolean', 'default' => TRUE));
		$viewHelper->setRenderingContext(new RenderingContext());
		$converted = $viewHelper->render();
		$this->assertInternalType('boolean', $converted);
		$this->assertTrue($converted);
	}
}
