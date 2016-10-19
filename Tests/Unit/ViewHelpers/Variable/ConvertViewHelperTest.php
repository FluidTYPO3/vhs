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
 * Class ConvertViewHelperTest
 */
class ConvertViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @param mixed $value
     * @param string $type
     * @param mixed $expected
     * @return void
     */
    protected function executeConversion($value, $type, $expected)
    {
        $this->assertEquals($expected, $this->executeViewHelper(['value' => $value, 'type' => $type]));
    }

    /**
     * @test
     */
    public function throwsRuntimeExceptionIfTypeOfDefaultValueIsUnsupported()
    {
        $this->setExpectedException('RuntimeException', null, 1364542576);
        $this->executeViewHelper(['type' => 'foobar', 'value' => null, 'default' => '1']);
    }

    /**
     * @test
     */
    public function throwsRuntimeExceptionIfTypeIsUnsupportedAndNoDefaultProvided()
    {
        $this->setExpectedException('RuntimeException', null, 1364542884);
        $this->executeViewHelper(['type' => 'unsupported', 'value' => null]);
    }

    /**
     * @test
     */
    public function throwsRuntimeExceptionIfTypeOfDefaultIsNotSameAsType()
    {
        $this->setExpectedException('RuntimeException', null, 1364542576);
        $this->executeViewHelper(['type' => 'ObjectStorage', 'value' => null, 'default' => '1']);
    }

    /**
     * @test
     */
    public function convertNullToString()
    {
        $this->executeConversion(null, 'string', '');
    }

    /**
     * @test
     */
    public function convertNullToInteger()
    {
        $this->executeConversion(null, 'integer', 0);
    }

    /**
     * @test
     */
    public function convertNullToFloat()
    {
        $this->executeConversion(null, 'float', 0);
    }

    /**
     * @test
     */
    public function convertStringToString()
    {
        $this->executeConversion('1', 'string', '1');
    }

    /**
     * @test
     */
    public function convertStringToInteger()
    {
        $this->executeConversion('1', 'integer', 1);
    }

    /**
     * @test
     */
    public function convertArrayToObjectStorage()
    {
        $dummy = new Foo();
        $storage = new ObjectStorage();
        $storage->attach($dummy);
        $this->executeConversion([$dummy], 'ObjectStorage', $storage);
    }

    /**
     * @test
     */
    public function convertObjectStorageToArray()
    {
        $dummy = new Foo();
        $storage = new ObjectStorage();
        $storage->attach($dummy);
        $this->executeConversion($storage, 'array', [$dummy]);
    }

    /**
     * @test
     */
    public function returnsEmptyStringForTypeStringAndValueNull()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments(['type' => 'string']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertEquals('', $converted);
    }

    /**
     * @test
     */
    public function returnsStringForTypeStringAndValueInteger()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(12345));
        $viewHelper->setArguments(['type' => 'string']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('string', $converted);
    }

    /**
     * @test
     */
    public function returnsArrayForTypeArrayAndValueNull()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments(['type' => 'array']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('array', $converted);
    }

    /**
     * @test
     */
    public function returnsArrayForTypeArrayAndValueString()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('foo'));
        $viewHelper->setArguments(['type' => 'array']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('array', $converted);
        $this->assertEquals(['foo'], $converted);
    }

    /**
     * @test
     */
    public function returnsObjectStorageForTypeArrayAndValueNull()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments(['type' => 'ObjectStorage']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInstanceOf(ObjectStorage::class, $converted);
        $this->assertEquals(0, $converted->count());
    }

    /**
     * @test
     */
    public function returnsArrayForTypeObjectStorage()
    {
        $domainObject = new Foo();
        $storage = new ObjectStorage();
        $storage->attach($domainObject);
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($storage));
        $viewHelper->setArguments(['type' => 'array']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('array', $converted);
        $this->assertEquals(1, count($converted));
    }

    /**
     * @test
     */
    public function returnsBooleanForTypeBooleanAndValueNull()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments(['type' => 'boolean']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('boolean', $converted);
        $this->assertFalse($converted);
    }

    /**
     * @test
     */
    public function returnsBooleanForTypeBooleanAndValueInteger()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(1));
        $viewHelper->setArguments(['type' => 'boolean']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('boolean', $converted);
        $this->assertTrue($converted);
    }

    /**
     * @test
     */
    public function returnsBooleanForTypeBooleanAndValueString()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('foo'));
        $viewHelper->setArguments(['type' => 'boolean']);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('boolean', $converted);
        $this->assertTrue($converted);
    }

    /**
     * @test
     */
    public function returnsExpectedDefaultValue()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments(['type' => 'boolean', 'default' => true]);
        $viewHelper->setRenderingContext(new RenderingContext());
        $converted = $viewHelper->render();
        $this->assertInternalType('boolean', $converted);
        $this->assertTrue($converted);
    }
}
