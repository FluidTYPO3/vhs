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
     * @test
     * @dataProvider getExecuteConversionTestValues
     */
    public function executeConversion($value, $type, $expected)
    {
        if (is_object($expected)) {
            $assertionMethod = 'assertEquals';
        } else {
            $assertionMethod = 'assertSame';
        }
        $this->$assertionMethod($expected, $this->executeViewHelper(['value' => $value, 'type' => $type]));
    }

    /**
     * @return array
     */
    public function getExecuteConversionTestValues()
    {
        $dummy = new Foo();
        $storage = new ObjectStorage();
        $storage->attach($dummy);
        return [
            [1, 'boolean', true],
            [null, 'string', ''],
            [null, 'integer', 0],
            [null, 'float', 0.0],
            ['1', 'string', '1'],
            ['1', 'integer', 1],
            [null, 'array', []],
            [null, 'boolean', false],
            ['1', 'boolean', true],
            [1, 'boolean', true],
            ['mystring', 'boolean', true],
            ['mystring', 'array', ['mystring']],
            [[$dummy], 'ObjectStorage', $storage],
            [$storage, 'array', [$dummy]]
        ];
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
    public function returnsExpectedDefaultValue()
    {
        $this->assertTrue($this->executeViewHelper(['type' => 'boolean', 'default' => true]));
    }
}
