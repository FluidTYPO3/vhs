<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Context;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class GetViewHelperTest
 */
class GetViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsContext()
    {
        $valid = ['Development', 'Testing', 'Production'];
        $result = $this->executeViewHelper([]);
        $this->assertContains($result, $valid);
    }
}
