<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Site;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class NameViewHelperTest
 */
class NameViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersSiteName()
    {
        $test = $this->executeViewHelper();
        $this->assertSame($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'], $test);
    }
}
