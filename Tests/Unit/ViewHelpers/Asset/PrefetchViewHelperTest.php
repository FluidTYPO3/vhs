<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Asset;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class PrefetchViewHelperTest
 */
class PrefetchViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function buildReturnsMetaTag()
    {
        $instance = $this->buildViewHelperInstance(['domains' => 'test.com,test2.com', 'force' => true]);
        $instance->render();
        $result = $instance->build();
        $this->assertStringStartsWith('<meta', $result);
    }
}
