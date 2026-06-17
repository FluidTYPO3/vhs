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
 * Class SumViewHelperTest
 */
class SumViewHelperTest extends AbstractMathViewHelperTestCase
{
    #[Test]
    public function testSingleArgumentIterator(): void
    {
        $this->executeSingleArgumentTest([8, 2], 10);
    }

    #[Test]
    public function testDualArguments(): void
    {
        $this->executeDualArgumentTest(8, 2, 10);
    }
}
