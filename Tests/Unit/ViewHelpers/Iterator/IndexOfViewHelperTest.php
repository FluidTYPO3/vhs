<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class IndexOfViewHelperTest
 */
class IndexOfViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function returnsIndexOfElement()
    {
        $array = ['a', 'b', 'c'];
        $arguments = [
            'haystack' => $array,
            'needle' => 'c',
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(2, $result);
    }

    /**
     * @test
     */
    public function returnsNegativeOneIfNeedleDoesNotExist()
    {
        $array = ['a', 'b', 'c'];
        $arguments = [
            'haystack' => $array,
            'needle' => 'd',
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(-1, $result);
    }
}
