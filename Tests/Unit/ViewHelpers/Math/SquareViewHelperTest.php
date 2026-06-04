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
 * Class SquareViewHelperTest
 */
class SquareViewHelperTest extends AbstractMathViewHelperTestCase
{
    #[Test]
    public function testSingleArgument(): void
    {
        $this->executeSingleArgumentTest(3, 9);
    }

    #[Test]
    public function testSingleArgumentIteratorFirst(): void
    {
        $this->executeSingleArgumentTest([2, 4], [4, 16]);
    }
}
