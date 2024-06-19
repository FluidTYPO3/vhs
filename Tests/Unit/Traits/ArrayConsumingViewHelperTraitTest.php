<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyArrayConsumingViewHelper;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class ArrayConsumingViewHelperTraitTest extends AbstractTestCase
{
    /**
     * @dataProvider getPositiveTestValues
     */
    public function testGetArgumentFromArgumentsOrTagContentAndConvertToArrayWithArgument(
        array $expected,
        $value
    ): void {
        self::assertSame($expected, $this->executeTest($value, false));
    }

    /**
     * @dataProvider getPositiveTestValues
     */
    public function testGetArgumentFromArgumentsOrTagContentAndConvertToArrayWithTagContent(
        array $expected,
        $value
    ): void {
        self::assertSame($expected, $this->executeTest($value, true));
    }

    public function getPositiveTestValues(): array
    {
        $queryResult = $this->getMockBuilder(QueryResultInterface::class)->getMockForAbstractClass();
        $queryResult->method('toArray')->willReturn(['a', 'b', 'c']);
        return [
            'with string' => [['a', 'b', 'c'], 'a,b,c'],
            'with array' => [['a', 'b', 'c'], ['a', 'b', 'c']],
            'with query result' => [['a', 'b', 'c'], $queryResult],
            'with iterator' => [['a', 'b', 'c'], new \ArrayIterator(['a', 'b', 'c'])],
        ];
    }

    private function executeTest($value, bool $asTagContent): array
    {
        $subject = new DummyArrayConsumingViewHelper();
        if ($asTagContent) {
            $subject->value = $value;
        } else {
            $subject->arguments['value'] = $value;
        }
        return $subject->execute();
    }

    public function testMergeArraysWithKeys(): void
    {
        $subject = new DummyArrayConsumingViewHelper();
        $output = $subject->merge(['a' => 'a', 'b' => 'b'], ['a' => 'a', 'c' => 'c']);
        self::assertSame(['a' => 'a', 'b' => 'b', 'c' => 'c'], $output);
    }

    /**
     * @dataProvider getNegativeTestValues
     */
    public function testThrowsErrorOnUnsupportedValues($value): void
    {
        self::expectException(Exception::class);
        $this->executeTest($value, false);
    }

    public function getNegativeTestValues(): array
    {
        return [
            'with null' => [null],
            'with integer' => [1],
            'with float' => [1.0],
            'with resource' => [fopen('php://temp', 'r')],
            'with DateTime' => [new \DateTime('now')],
            'with subject fixture' => [new DummyArrayConsumingViewHelper()],
        ];
    }
}
