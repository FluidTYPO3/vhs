<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class AppendViewHelperTest
 */
class AppendViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canAppendValueToArgument()
    {
        $arguments = [
            'subject' => 'before',
            'add' => 'after'
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertStringEndsWith($arguments['add'], $test);
    }

    /**
     * @test
     */
    public function canAppendValueToChildContent()
    {
        $arguments = [
            'add' => 'after'
        ];
        $test = $this->executeViewHelperUsingTagContent('before', $arguments);
        $this->assertStringEndsWith($arguments['add'], $test);
    }
}
