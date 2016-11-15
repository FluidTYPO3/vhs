<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class CookieViewHelperTest
 */
class CookieViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @return void
     */
    public function testAssertShouldSkip()
    {
        $this->assertNull($this->executeViewHelper(['identifier' => 'test']));
    }
}
