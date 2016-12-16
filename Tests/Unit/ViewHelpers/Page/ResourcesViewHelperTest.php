<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class ResourcesViewHelperTest
 */
class ResourcesViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function testRenderFailsWithoutFieldArgument()
    {
        $this->expectViewHelperException('The "field" argument was not found');
        $this->executeViewHelper();
    }
}
