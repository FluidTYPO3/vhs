<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\System;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class TimestampViewHelperTest
 */
class TimestampViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsIntegerAtOrAboveNowAsMeasuredInTest()
    {
        $now = time();
        $result = $this->executeViewHelper();
        $this->assertGreaterThanOrEqual($now, $result);
    }
}
