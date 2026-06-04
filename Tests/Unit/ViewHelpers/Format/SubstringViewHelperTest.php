<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;
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
 * Class SubstringViewHelperTest
 */
class SubstringViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function canRenderUsingArguments(): void
    {
        $arguments = [
            'content' => 'foobar',
            'length' => null,
            'start' => 3
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('bar', $test);
    }

    #[Test]
    public function canRenderWithLengthArgument(): void
    {
        $arguments = [
            'content' => 'foobar',
            'length' => 3,
            'start' => 2
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('oba', $test);
    }
}
