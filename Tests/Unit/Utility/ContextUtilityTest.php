<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\Utility\ContextUtility;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;

class ContextUtilityTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function explicitRequestWinsOverGlobalRequest(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'applicationType',
            SystemEnvironmentBuilder::REQUESTTYPE_FE
        );
        $activeRequest = (new ServerRequest())->withAttribute(
            'applicationType',
            SystemEnvironmentBuilder::REQUESTTYPE_BE
        );

        self::assertFalse(ContextUtility::isFrontend($activeRequest));
        self::assertTrue(ContextUtility::isBackend($activeRequest));
    }
}
