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
 * Class ReplaceViewHelperTest
 */
class ReplaceViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canReplace()
    {
        $arguments = [
            'content' => 'foobar',
            'substring' => 'foo',
            'replacement' => ''
        ];
        $test = $this->executeViewHelper($arguments);
        $this->assertSame('bar', $test);
    }

}
