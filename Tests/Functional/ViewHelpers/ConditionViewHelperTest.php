<?php

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers;

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Bar;
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class ConditionViewHelperTest extends AbstractFunctionalViewHelperCase
{
    /**
     * @dataProvider conditionTemplates
     */
    public function testConditionsRenderExpectedBranch(string $source, ?string $expected, array $variables = []): void
    {
        $this->assertTemplateRenders($expected, $source, $variables);
    }

    public function conditionTemplates(): array
    {
        return [
            'string contains then' => [
                '<v:condition.string.contains haystack="foobar" needle="foo" then="then" else="else" />',
                'then',
            ],
            'string contains else' => [
                '<v:condition.string.contains haystack="foobar" needle="baz" then="then" else="else" />',
                'else',
            ],
            'string lowercase first character' => [
                '<v:condition.string.isLowercase string="lowerCase" then="then" else="else" />',
                'then',
            ],
            'string lowercase full string mismatch' => [
                '<v:condition.string.isLowercase string="lowerCase" fullString="1" then="then" else="else" />',
                'else',
            ],
            'string uppercase first character' => [
                '<v:condition.string.isUppercase string="UpperCase" then="then" else="else" />',
                'then',
            ],
            'string uppercase full string mismatch' => [
                '<v:condition.string.isUppercase string="UpperCase" fullString="1" then="then" else="else" />',
                'else',
            ],
            'string numeric then' => [
                '<v:condition.string.isNumeric value="123" then="then" else="else" />',
                'then',
            ],
            'string numeric else' => [
                '<v:condition.string.isNumeric value="z123" then="then" else="else" />',
                'else',
            ],
            'type array then' => [
                '<v:condition.type.isArray value="{value}" then="then" else="else" />',
                'then',
                ['value' => []],
            ],
            'type boolean then' => [
                '<v:condition.type.isBoolean value="{value}" then="then" else="else" />',
                'then',
                ['value' => true],
            ],
            'type domain object then' => [
                '<v:condition.type.isDomainObject value="{value}" then="then" else="else" />',
                'then',
                ['value' => new Foo()],
            ],
            'type float then' => [
                '<v:condition.type.isFloat value="{value}" then="then" else="else" />',
                'then',
                ['value' => 0.5],
            ],
            'type instance then' => [
                '<v:condition.type.isInstanceOf value="{value}" class="DateTime" then="then" else="else" />',
                'then',
                ['value' => new \DateTime('@1')],
            ],
            'type integer then' => [
                '<v:condition.type.isInteger value="{value}" then="then" else="else" />',
                'then',
                ['value' => 1],
            ],
            'type object then' => [
                '<v:condition.type.isObject value="{value}" then="then" else="else" />',
                'then',
                ['value' => new \stdClass()],
            ],
            'type string then' => [
                '<v:condition.type.isString value="{value}" then="then" else="else" />',
                'then',
                ['value' => 'test'],
            ],
            'type traversable then' => [
                '<v:condition.type.isTraversable value="{value}" then="then" else="else" />',
                'then',
                ['value' => new \ArrayIterator(['test'])],
            ],
            'type array else' => [
                '<v:condition.type.isArray value="{value}" then="then" else="else" />',
                'else',
                ['value' => new \stdClass()],
            ],
            'variable null then' => [
                '<v:condition.variable.isNull value="{value}" then="then" else="else" />',
                'then',
                ['value' => null],
            ],
            'variable null else' => [
                '<v:condition.variable.isNull value="{value}" then="then" else="else" />',
                'else',
                ['value' => true],
            ],
            'variable isset then' => [
                '<v:condition.variable.isset name="test" then="then" else="else" />',
                'then',
                ['test' => true],
            ],
            'variable isset else' => [
                '<v:condition.variable.isset name="test" then="then" else="else" />',
                'else',
            ],
            'unless positive case' => [
                '<v:unless condition="{a}">rendered</v:unless>',
                'rendered',
                ['a' => false],
            ],
            'unless negative case' => [
                '<v:unless condition="{a}">rendered</v:unless>',
                null,
                ['a' => true],
            ],
        ];
    }

    public function testFrontendAndBackendContextConditionsUseRequestApplicationType(): void
    {
        $source = '<v:condition.context.isFrontend then="frontend" else="not-frontend" />'
            . '|<v:condition.context.isBackend then="backend" else="not-backend" />';

        self::assertSame('frontend|not-backend', $this->executeTemplateWithRequest($source, $this->createFrontendRequest()));
        self::assertSame('not-frontend|backend', $this->executeTemplateWithRequest($source, $this->createBackendRequest()));
    }

    public function testApplicationContextConditionsRenderFromEnvironment(): void
    {
        $source = '<v:condition.context.isDevelopment then="development" else="not-development" />'
            . '|<v:condition.context.isProduction then="production" else="not-production" />'
            . '|<v:condition.context.isTesting then="testing" else="not-testing" />'
            . '|<v:context.get />';

        self::assertSame(
            'development|not-production|not-testing|Development',
            $this->executeTemplate($source)
        );
    }

    public function testIteratorContainsConditionHandlesArraysStringsAndObjectStorage(): void
    {
        $bar = new Bar();
        $foo = new Foo();
        $this->setDomainObjectUid($bar, 1);
        $this->setDomainObjectUid($foo, 2);

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($bar);

        $lazyObjectStorage = $this->getMockBuilder(LazyObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->setInaccessibleProperty($lazyObjectStorage, 'isInitialized', true);
        $lazyObjectStorage->attach($foo);

        $source = '<v:condition.iterator.contains haystack="{array}" needle="foo" then="1" else="0" />'
            . '<v:condition.iterator.contains haystack="{array}" needle="bar" then="1" else="0" />'
            . '<v:condition.iterator.contains haystack="foo,baz" needle="foo" then="1" else="0" />'
            . '<v:condition.iterator.contains haystack="{storage}" needle="{bar}" then="1" else="0" />'
            . '<v:condition.iterator.contains haystack="{storage}" needle="{foo}" then="1" else="0" />'
            . '<v:condition.iterator.contains haystack="{lazyStorage}" needle="{foo}" then="1" else="0" />'
            . '<v:condition.iterator.contains haystack="{lazyStorage}" needle="{bar}" then="1" else="0" />';

        self::assertSame(
            '1011000',
            $this->executeTemplate(
                $source,
                [
                    'array' => ['foo'],
                    'bar' => $bar,
                    'foo' => $foo,
                    'storage' => $objectStorage,
                    'lazyStorage' => $lazyObjectStorage,
                ]
            )
        );
    }

    public function testQueryResultConditionChecksInterface(): void
    {
        $queryResult = $this->getMockBuilder(QueryResultInterface::class)->getMockForAbstractClass();

        $source = '<v:condition.type.isQueryResult value="{queryResult}" then="then" else="else" />'
            . '|<v:condition.type.isQueryResult value="{notQueryResult}" then="then" else="else" />';

        self::assertSame(
            'then|else',
            $this->executeTemplate($source, ['queryResult' => $queryResult, 'notQueryResult' => 1])
        );
    }

    private function setDomainObjectUid(object $object, int $uid): void
    {
        $this->setInaccessibleProperty($object, 'uid', $uid);
    }

    private function setInaccessibleProperty(object $object, string $propertyName, mixed $value): void
    {
        $property = new \ReflectionProperty($object, $propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
