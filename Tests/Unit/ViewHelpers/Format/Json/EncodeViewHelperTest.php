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
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/**
 * Class EncodeViewHelperTest
 */
class EncodeViewHelperTest extends AbstractViewHelperTestCase
{
    private int $defaultOptions = 0;

    protected function setUp(): void
    {
        if (!function_exists('json_encode')) {
            self::markTestSkipped('Skipped: no ext-json PHP module is not installed');
        }
        $this->defaultOptions = JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG;
        $this->singletonInstances[ReflectionService::class] = $this->getMockBuilder(ReflectionService::class)
            ->setMethods(['__destruct'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();
    }

    protected function getInstanceOfFoo()
    {
        return new Foo();
    }

    /**
     * @test
     */
    public function encodesDateTime()
    {
        $dateTime = \DateTime::createFromFormat('U', 86400);
        $instance = $this->createInstance();
        $test = $this->callInaccessibleMethod($instance, 'encodeValue', $dateTime, false, true, null, null, $this->defaultOptions);
        $this->assertEquals(86400000, $test);
    }

    /**
     * @test
     */
    public function encodesRecursiveDomainObject()
    {
        /** @var Foo $object */
        $object = $this->getInstanceOfFoo();
        $object->setFoo($object);
        $instance = $this->createInstance();
        $test = $this->callInaccessibleMethod($instance, 'encodeValue', $object, true, true, null, null, $this->defaultOptions);
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
        $traversable = new ObjectStorage();
        $instance = $this->createInstance();
        $test = $this->callInaccessibleMethod($instance, 'encodeValue', $traversable, false, true, null, null, $this->defaultOptions);
        $this->assertEquals('[]', $test);
    }

    /**
     * @test
     */
    public function returnsEmptyObjectOnTopLevel()
    {
        $this->assertEquals('{}', $this->executeViewHelper(['value' => new \stdClass()]));
    }

    /**
     * @test
     */
    public function returnsEmptyArrayOnTopLevel()
    {
        $this->assertEquals('[]', $this->executeViewHelper(['value' => []]));
    }

    /**
     * @test
     */
    public function returnsFalseOnTopLevel()
    {
        $this->assertEquals('false', $this->executeViewHelper(['value' => false]));
    }

    /**
     * @test
     */
    public function returnsTrueOnTopLevel()
    {
        $this->assertEquals('true', $this->executeViewHelper(['value' => true]));
    }

    /**
     * @test
     */
    public function returnsNumberOnTopLevel()
    {
        $this->assertSame(json_encode(1.0), $this->executeViewHelper(['value' => 1.0]));
    }

    /**
     * @test
     */
    public function returnsNullOnTopLevel()
    {
        $this->assertEquals('null', $this->executeViewHelper(['value' => null]));
    }

    /**
     * @test
     */
    public function returnsExpectedStringForProvidedArguments()
    {

        $storage = new ObjectStorage();
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
