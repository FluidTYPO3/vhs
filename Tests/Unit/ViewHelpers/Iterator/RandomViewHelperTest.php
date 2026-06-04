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
 * Class RandomViewHelperTest
 */
class RandomViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param array $asArray
     */
    public function testRender(array $arguments, array $asArray): void
    {
        if (($arguments['subject'] ?? null) === 'queryResult') {
            $arguments['subject'] = $this->createQueryResult(['foo', 'bar'], 0);
        }
        $value = $this->executeViewHelper($arguments);
        if (null !== $value) {
            $this->assertContains($value, $asArray);
        } else {
            $this->assertNull($value);
        }
    }

    /**
     * @return array
     */
    public static function getRenderTestValues(): array
    {
        return [
            [['subject' => ['foo', 'bar']], ['foo', 'bar']],
            [['subject' => new \ArrayIterator(['foo', 'bar'])], ['foo', 'bar']],
            [['subject' => 'queryResult'], ['foo', 'bar']],
        ];
    }

    private function createQueryResult(array $values, int $count): QueryResult
    {
        $queryResult = $this->getMockBuilder(QueryResult::class)
            ->onlyMethods(['toArray', 'initialize', 'rewind', 'valid', 'count'])
            ->disableOriginalConstructor()
            ->getMock();
        $queryResult->method('toArray')->willReturn($values);
        $queryResult->method('count')->willReturn($count);
        $queryResult->method('valid')->willReturn(false);
        return $queryResult;
    }
}
