<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class ReverseViewHelperTest
 */
class ReverseViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expectedValue
     */
    public function testRender(array $arguments, mixed $expectedValue): void
    {
        if (($arguments['subject'] ?? null) === 'queryResult') {
            $arguments['subject'] = $this->createQueryResult(['foo', 'bar'], 1);
        }
        $this->assertEquals($this->executeViewHelper($arguments), $expectedValue);
    }

    /**
     * @return array
     */
    public static function getRenderTestValues(): array
    {
        return [
            [['subject' => []], []],
            [['subject' => ['foo', 'bar']], [1 => 'bar', 0 => 'foo']],
            [['subject' => new \ArrayIterator(['foo', 'bar'])], [1 => 'bar', 0 => 'foo']],
            [['subject' => 'queryResult'], [1 => 'bar', 0 => 'foo']],
        ];
    }

    private function createQueryResult(array $values, int $count): QueryResult
    {
        $queryResult = $this->getMockBuilder(QueryResult::class)
            ->onlyMethods(['toArray', 'initialize', 'rewind', 'valid', 'count'])
            ->disableOriginalConstructor()
            ->getMock();
        $queryResult->method('toArray')->willReturn($values);
        $queryResult->method('valid')->willReturn(false);
        $queryResult->method('count')->willReturn($count);
        return $queryResult;
    }

    /**
     * @test
     * @dataProvider getErrorTestValues
     * @param mixed $subject
     */
    public function testThrowsErrorsOnInvalidSubjectType(mixed $subject): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelper(['subject' => $subject]);
    }

    /**
     * @return array
     */
    public static function getErrorTestValues(): array
    {
        return [
            [0],
            [null],
            [new \DateTime()],
            [new \stdClass()],
        ];
    }
}
