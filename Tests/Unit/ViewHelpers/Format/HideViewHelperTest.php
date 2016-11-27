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
 * Class HideViewHelperTest
 */
class HideViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function hidesTagContent()
    {
        $test = $this->executeViewHelperUsingTagContent('this is hidden');
        $this->assertNull($test);
    }

    /**
     * @test
     */
    public function canBeDisabled()
    {
        $test = $this->executeViewHelperUsingTagContent('this is shown', ['disabled' => true]);
        $this->assertSame('this is shown', $test);
    }
}
