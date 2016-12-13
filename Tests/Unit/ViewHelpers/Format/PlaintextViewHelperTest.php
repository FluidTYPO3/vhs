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
 * Class PlaintextViewHelperTest
 */
class PlaintextViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function formatsToPlaintext()
    {
        $input = "	This string\n	is plain-text formatted";
        $expected = "This string\nis plain-text formatted";
        $result = $this->executeViewHelper(['content' => $input]);
        $this->assertEquals($expected, $result);
    }
}
