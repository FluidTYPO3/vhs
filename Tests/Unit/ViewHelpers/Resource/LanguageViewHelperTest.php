<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class LanguageViewHelperTest
 */
class LanguageViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function testRenderFailsWhenUnableToResolveExtensionName()
    {
        $this->expectViewHelperException('Cannot read extension name from ControllerContext and value not manually specified');
        $this->executeViewHelper();
    }
}
