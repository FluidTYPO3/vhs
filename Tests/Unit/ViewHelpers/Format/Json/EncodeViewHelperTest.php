<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Json;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class EncodeViewHelperTest
 */
class EncodeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function encodesDateTime()
    {
        $dateTime = \DateTime::createFromFormat('U', 86400);
        $instance = $this->createInstance();
        $test = $this->callInaccessibleMethod($instance, 'encodeValue', $dateTime, false, true, null, null);
        $this->assertEquals(86400000, $test);
    }

    /**
     * @test
     */
    public function encodesRecursiveDomainObject()
    {
        /** @var Foo $object */
        $object = $this->objectManager->get(Foo::class);
        $object->setFoo($object);
        $instance = $this->createInstance();
        $test = $this->callInaccessibleMethod($instance, 'encodeValue', $object, true, true, null, null);
        $this->assertEquals('{"bar":"baz","children":[],"foo":null,"name":null,"pid":null,"uid":null}', $test);
    }

    /**
     * @test
     */
    public function encodesDateTimeWithFormat()
    {
        $dateTime = \DateTime::createFromFormat('U', 86401);
        $arguments = [
            'value' => [
                'date' => $dateTime,
            ],
            'dateTimeFormat' => 'Y-m-d',
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertEquals('{"date":"1970-01-02"}', $test);
    }

    /**
     * @test
     */
    public function encodesTraversable()
    {
        $traversable = $this->objectManager->get(ObjectStorage::class);
        $instance = $this->createInstance();
        $test = $this->callInaccessibleMethod($instance, 'encodeValue', $traversable, false, true, null, null);
        $this->assertEquals('[]', $test);
    }

    /**
     * @test
     */
    public function returnsEmptyJsonObjectForEmptyArguments()
    {
        $this->assertEquals('{}', $this->executeViewHelper([]));
    }

    /**
     * @test
     */
    public function returnsExpectedStringForProvidedArguments()
    {

        $storage = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage');
        $fixture = [
            'foo' => 'bar',
            'bar' => true,
            'baz' => 1,
            'foobar' => null,
            'date' => \DateTime::createFromFormat('U', 3216548),
            'traversable' => $storage
        ];

        $expected = '{"foo":"bar","bar":true,"baz":1,"foobar":null,"date":3216548000,"traversable":[]}';

        $this->assertEquals($expected, $this->executeViewHelper(['value' => $fixture]));
    }

    /**
     * @test
     */
    public function throwsExceptionForInvalidArgument()
    {
        $this->expectViewHelperException();
        $this->assertEquals('null', $this->executeViewHelper(['value' => "\xB1\x31"]));
    }

    /**
     * @test
     */
    public function returnsJsConsumableTimestamps()
    {
        $date = new \DateTime('now');
        $jsTimestamp = $date->getTimestamp() * 1000;

        $fixture = ['foo' => $date, 'bar' => ['baz' => $date]];
        $expected = sprintf('{"foo":%s,"bar":{"baz":%s}}', $jsTimestamp, $jsTimestamp);

        $this->assertEquals($expected, $this->executeViewHelper(['value' => $fixture]));
    }
}
