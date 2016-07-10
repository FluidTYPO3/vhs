<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class RenderViewHelperTest
 */
class RenderViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        $this->assertEmpty($this->executeViewHelper());
    }
}
