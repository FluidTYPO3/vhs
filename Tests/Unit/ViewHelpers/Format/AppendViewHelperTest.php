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

/**
 * Class AppendViewHelperTest
 */
class AppendViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function canAppendValueToArgument(): void
    {
        $arguments = [
            'subject' => 'before',
            'add' => 'after'
        ];
        $test = $this->executeViewHelper($arguments);
        self::assertIsString($test);
        $this->assertStringEndsWith($arguments['add'], $test);
    }

    /**
     * @test
     */
    public function canAppendValueToChildContent(): void
    {
        $arguments = [
            'add' => 'after'
        ];
        $test = $this->executeViewHelperUsingTagContent('before', $arguments);
        self::assertIsString($test);
        $this->assertStringEndsWith($arguments['add'], $test);
    }
}
