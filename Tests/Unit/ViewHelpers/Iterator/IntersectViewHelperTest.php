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
 * Class IntersectViewHelperTest
 */
class IntersectViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function intersectTest()
    {
        $array1 = array('a' => 'green', 'red', 'blue');
        $array2 = array('b' => 'green', 'yellow', 'red');
        $arguments = array('a' => $array1, 'b' => $array2);
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(array('a' => 'green', 0 => 'red'), $result);
    }

    /**
     * @test
     */
    public function intersectTestWithTagContent()
    {
        $array1 = array('a' => 'green', 'red', 'blue');
        $array2 = array('b' => 'green', 'yellow', 'red');
        $arguments = array('b' => $array2);
        $result = $this->executeViewHelperUsingTagContent('Array', $array1, $arguments);
        $this->assertEquals(array('a' => 'green', 0 => 'red'), $result);
    }
}
