<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Math;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class AbstractMathViewHelperTest
 */
abstract class AbstractMathViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @param mixed $a
     * @param mixed $expected
     * @return void
     */
    protected function executeSingleArgumentTest($a, $expected)
    {
        $result = $this->executeViewHelper(['a' => $a, 'fail' => false]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $expected
     * @return void
     */
    protected function executeDualArgumentTest($a, $b, $expected)
    {
        $result = $this->executeViewHelper(['a' => $a, 'b' => $b, 'fail' => false]);
        $this->assertEquals($expected, $result);
    }
}
