<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Bar;
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ContainsViewHelperTest
 */
class ContainsViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @dataProvider getPositiveTestValues
     * @param mixed $haystack
     * @param mixed $needle
     */
    public function testRendersThen($haystack, $needle)
    {
        $arguments = [
            'haystack' => $haystack,
            'needle' => $needle,
            'then' => 'then'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    /**
     * @return array
     */
    public static function getPositiveTestValues()
    {
        $bar = self::createBar(1);
        $foo = self::createFoo(2);
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($bar);
        $lazyObjectStorage = self::createInitializedLazyObjectStorage();
        $lazyObjectStorage->attach($foo);
        return [
            'with array and string' => [['foo'], 'foo'],
            'with csv' => ['foo,bar', 'foo'],
            'with array and domain object' => [[$foo], $foo],
            'with object storage' => [$objectStorage, $bar],
            'with lazy object storage' => [$lazyObjectStorage, $foo],
        ];
    }

    /**
     * @dataProvider getNegativeTestValues
     * @param mixed $haystack
     * @param mixed $needle
     */
    public function testRendersElse($haystack, $needle)
    {
        $arguments = [
            'haystack' => $haystack,
            'needle' => $needle,
            'else' => 'else'
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    /**
     * @return array
     */
    public static function getNegativeTestValues()
    {
        $bar = self::createBar(1);
        $foo = self::createFoo(2);
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($bar);
        $lazyObjectStorage = self::createInitializedLazyObjectStorage();
        $lazyObjectStorage->attach($foo);
        return [
            [['foo'], 'bar'],
            ['foo,baz', 'bar'],
            [$objectStorage, $foo],
            [$lazyObjectStorage, $bar]
        ];
    }

    private static function createBar(int $uid): Bar
    {
        $bar = new Bar();
        self::setObjectUid($bar, $uid);
        return $bar;
    }

    private static function createFoo(int $uid): Foo
    {
        $foo = new Foo();
        self::setObjectUid($foo, $uid);
        return $foo;
    }

    private static function setObjectUid(object $object, int $uid): void
    {
        $property = new \ReflectionProperty($object, 'uid');
        $property->setAccessible(true);
        $property->setValue($object, $uid);
    }

    private static function createInitializedLazyObjectStorage(): LazyObjectStorage
    {
        $reflection = new \ReflectionClass(LazyObjectStorage::class);
        /** @var LazyObjectStorage $lazyObjectStorage */
        $lazyObjectStorage = $reflection->newInstanceWithoutConstructor();
        $property = new \ReflectionProperty($lazyObjectStorage, 'isInitialized');
        $property->setAccessible(true);
        $property->setValue($lazyObjectStorage, true);
        return $lazyObjectStorage;
    }
}
