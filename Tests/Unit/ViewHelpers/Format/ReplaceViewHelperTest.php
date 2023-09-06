<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

class ReplaceViewHelperTest extends AbstractViewHelperTestCase
{
    public function testCanReplace(): void
    {
        $arguments = [
            'content' => 'foobar',
            'substring' => 'foo',
            'replacement' => '',
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('bar', $test);
    }

    public function testReturnsCountWhenAsked(): void
    {
        $arguments = [
            'content' => 'foobar',
            'substring' => 'foo',
            'replacement' => '',
            'returnCount' => true,
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertSame(1, $test);
    }
}
