<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Site;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class NameViewHelperTest
 */
class NameViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function rendersSiteName()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = 'test';
        $test = $this->executeViewHelper();
        unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']);
        $this->assertSame('test', $test);
    }
}
