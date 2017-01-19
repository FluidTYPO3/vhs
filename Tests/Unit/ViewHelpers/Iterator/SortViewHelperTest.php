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
 * Class SortViewHelperTest
 */
class SortViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function throwsExceptionOnUnsupportedSortFlag()
    {
        $arguments = ['sortFlags' => 'FOOBAR'];
        $this->expectViewHelperException('The constant "FOOBAR" you\'re trying to use as a sortFlag is not allowed.');
        $this->executeViewHelperUsingTagContent(['a', 'b', 'c'], $arguments);
    }
}
