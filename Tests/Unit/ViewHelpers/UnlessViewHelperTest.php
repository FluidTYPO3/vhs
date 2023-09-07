<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\UnlessViewHelper;

class UnlessViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender()
    {
        $this->assertEmpty($this->executeViewHelper());
    }

    /**
     * @dataProvider getBehaviorTestValues
     */
    public function testBehavior(?string $expected, bool $condition): void
    {
        $output = $this->executeViewHelperUsingTagContent('matched', ['condition' => $condition]);
        self::assertSame($expected, $output);
    }

    /**
     * @dataProvider getBehaviorTestValues
     */
    public function testStaticBehavior(?string $expected, bool $condition): void
    {
        $closure = function() { return 'matched'; };
        $output = UnlessViewHelper::renderStatic(['condition' => $condition], $closure, $this->renderingContext);
        self::assertSame($expected, $output);
    }

    public function getBehaviorTestValues(): array
    {
        return [
            'Renders nothing if condition is true' => [null, true],
            'Renders child content if condition is false' => ['matched', false],
        ];
    }
}
