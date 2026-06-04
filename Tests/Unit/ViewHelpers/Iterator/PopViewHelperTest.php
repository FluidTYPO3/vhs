<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class PopViewHelperTest
 */
class PopViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @param array $arguments
     * @param mixed $expectedValue
     */
    #[Test]
    #[DataProvider('getRenderTestValues')]
    public function testRender(array $arguments, mixed $expectedValue): void
    {
        $this->assertSame($expectedValue, $this->executeViewHelper($arguments));
    }

    /**
     * @return array
     */
    public static function getRenderTestValues(): array
    {
        return [
            [['subject' => []], null],
            [['subject' => ['foo', 'bar']], 'bar'],
            [['subject' => new \ArrayIterator(['foo', 'bar'])], 'bar'],
        ];
    }

    /**
     * @param mixed $subject
     */
    #[Test]
    #[DataProvider('getErrorTestValues')]
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
