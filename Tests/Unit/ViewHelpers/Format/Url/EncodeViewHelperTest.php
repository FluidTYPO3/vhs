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
 * Class EncodeViewHelperTest
 */
class EncodeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function encodesUrlDecodedStrings()
    {
        $decoded = 'Url Decoded';
        $result1 = $this->executeViewHelper(array('content' => $decoded));
        $result2 = $this->executeViewHelperUsingTagContent('Text', $decoded);
        $this->assertEquals(rawurlencode($decoded), $result1);
        $this->assertEquals($result1, $result2);
    }
}
