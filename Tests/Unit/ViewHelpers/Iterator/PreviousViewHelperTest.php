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
 * Class PreviousViewHelperTest
 */
class PreviousViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function returnsPreviousElement()
    {
        $array = ['a', 'b', 'c'];
        next($array);
        $arguments = [
            'haystack' => $array,
            'needle' => 'c',
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('b', $result);
    }
}
