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

/**
 * Class ShiftViewHelperTest
 */
class ShiftViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expectedValue
     */
    public function testRender(array $arguments, mixed $expectedValue): void
    {
        $this->assertEquals($this->executeViewHelper($arguments), $expectedValue);
    }

    /**
     * @return array
     */
    public static function getRenderTestValues(): array
    {
        return [
            [['subject' => []], null],
            [['subject' => ['foo', 'bar']], 'foo'],
            [['subject' => new \ArrayIterator(['foo', 'bar'])], 'foo'],
        ];
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
