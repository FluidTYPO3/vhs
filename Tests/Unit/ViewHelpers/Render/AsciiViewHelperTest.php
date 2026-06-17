<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Render;
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
 * Class AsciiViewHelperTest
 */
class AsciiViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @param integer $ascii
     * @param string $expected
     */
    #[Test]
    #[DataProvider('getTestRenderValues')]
    public function testRender(mixed $ascii, string $expected): void
    {
        $result = $this->executeViewHelper(['ascii' => $ascii]);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public static function getTestRenderValues(): array
    {
        return [
            [10, "\n"],
            [32, ' '],
            [64, '@'],
            [[65, 66, 67], 'ABC'],
            [new \ArrayIterator([67, 66, 65]), 'CBA']
        ];
    }
}
