<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

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
        $this->setExpectedException(Exception::class, 'Cannot read extension name from ControllerContext and value not manually specified');
        $this->executeViewHelper();
    }
}
