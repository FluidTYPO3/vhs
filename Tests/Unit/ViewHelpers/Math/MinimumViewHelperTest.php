<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class MinimumViewHelperTest
 */
class MinimumViewHelperTest extends AbstractMathViewHelperTestCase
{
    #[Test]
    public function testSingleArgument(): void
    {
        $this->executeSingleArgumentTest([1, 3], 1);
    }

    #[Test]
    public function testDualArgument(): void
    {
        $this->executeDualArgumentTest(4, 2, 2);
    }

    #[Test]
    public function testDualArgumentBothIterators(): void
    {
        $this->executeDualArgumentTest([4, 8], [8, 8], [4, 8]);
    }

    #[Test]
    public function executeMissingArgumentTest(): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelper(['a' => 1, 'fail' => true]);
    }

    #[Test]
    public function executeInvalidArgumentTypeTest(): void
    {
        $this->expectViewHelperException();
        $this->executeViewHelper(['b' => 1, 'fail' => true]);
    }
}
