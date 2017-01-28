<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Url;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class DecodeViewHelperTest
 */
class DecodeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function decodesUrlEncodedStrings()
    {
        $encoded = 'Url%20Encoded';
        $result = $this->executeViewHelper(['content' => $encoded]);
        $this->assertEquals(rawurldecode($encoded), $result);
    }
}
