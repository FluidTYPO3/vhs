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
 * Class SubstringViewHelperTest
 */
class SubstringViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canRenderUsingArguments()
    {
        $arguments = [
            'content' => 'foobar',
            'length' => null,
            'start' => 3
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('bar', $test);
    }

    /**
     * @test
     */
    public function canRenderWithLengthArgument()
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
