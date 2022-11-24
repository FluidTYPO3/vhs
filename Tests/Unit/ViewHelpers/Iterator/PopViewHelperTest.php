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
 * Class PopViewHelperTest
 */
class PopViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expectedValue
     */
    public function testRender(array $arguments, $expectedValue)
    {
        $this->assertEquals($this->executeViewHelper($arguments), $expectedValue);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [['subject' => []], null],
            [['subject' => ['foo', 'bar']], 'bar'],
            [['subject' => new \ArrayIterator(['foo', 'bar'])], 'bar'],
        ];
    }

    /**
     * @test
     * @dataProvider getErrorTestValues
     * @param mixed $subject
     */
    public function testThrowsErrorsOnInvalidSubjectType($subject)
    {
        $this->expectViewHelperException();
        $this->executeViewHelper(['subject' => $subject]);
    }

    /**
     * @return array
     */
    public function getErrorTestValues()
    {
        return [
            [0],
            [null],
            [new \DateTime()],
            [new \stdClass()],
        ];
    }
}
