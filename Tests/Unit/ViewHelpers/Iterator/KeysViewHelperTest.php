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
 * Class KeysViewHelperTest
 */
class KeysViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsKeys()
    {
        $array = array('a' => 'A', 'b' => 'B', 'c' => 'C');
        $expected = array('a', 'b', 'c');
        $arguments = array(
            'subject' => $array,
        );
        $output = $this->executeViewHelper($arguments);
        $output2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', array(), array('v' => $array));
        $this->assertEquals($expected, $output);
        $this->assertEquals($output, $output2);
    }

    /**
     * @test
     */
    public function supportsAsArgument()
    {
        $array = array('a' => 'A', 'b' => 'B', 'c' => 'C');
        $arguments = array('as' => 'v', 'subject' => $array);
        $result = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v.1', $arguments);
        $this->assertEquals('b', $result);
    }
}
