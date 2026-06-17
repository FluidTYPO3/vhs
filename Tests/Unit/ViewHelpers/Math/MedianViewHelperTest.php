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
 * Class MedianViewHelperTest
 */
class MedianViewHelperTest extends AbstractMathViewHelperTestCase
{
    #[Test]
    public function testSingleArgumentNotIteratorPassesThrough(): void
    {
        $this->executeSingleArgumentTest(1, 1);
    }

    #[Test]
    public function testSingleArgumentThreeMembers(): void
    {
        $this->executeSingleArgumentTest([1, 2, 3], 2);
    }

    #[Test]
    public function testSingleArgumentFourMembers(): void
    {
        $this->executeSingleArgumentTest([1, 2, 3, 4], 2.5);
    }
}
