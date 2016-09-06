<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RequestViewHelperTest
 */
class RequestViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersUrl()
    {
        $test = $this->executeViewHelper();
        $this->assertSame(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'), $test);
    }
}
