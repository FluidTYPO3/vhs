<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class MergeViewHelperTest
 */
class MergeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function testMergesArraysWithOverrule()
    {
        $array1 = array('foo');
        $array2 = array('bar');
        $expected = array('bar');
        $result = $this->executeViewHelper(array('a' => $array1, 'b' => $array2, 'useKeys' => false));
        $this->assertEquals($expected, $result);
    }
}
