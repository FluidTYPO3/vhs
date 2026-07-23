<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\SiteFinderProxy;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use FluidTYPO3\Vhs\Utility\VersionUtility;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class FrontendSimulationUtilityTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        unset($GLOBALS['TSFE'], $GLOBALS['LANG']);

        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['FE']['enable_mount_pids'] = false;

        $siteLanguage = $this->getMockBuilder(SiteLanguage::class)->disableOriginalConstructor()->getMock();

        $site = $this->getMockBuilder(Site::class)
            ->setMethods(['getDefaultLanguage'])
            ->disableOriginalConstructor()
            ->getMock();
        $site->method('getDefaultLanguage')->willReturn($siteLanguage);

        $siteFinder = $this->getMockBuilder(SiteFinderProxy::class)
            ->setMethods(['getAllSites'])
            ->disableOriginalConstructor()
            ->getMock();
        $siteFinder->method('getAllSites')->willReturn([$site]);

        $frontendUserAuthentication = $this->getMockBuilder(FrontendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        GeneralUtility::addInstance(SiteFinderProxy::class, $siteFinder);
        GeneralUtility::addInstance(FrontendUserAuthentication::class, $frontendUserAuthentication);
        GeneralUtility::addInstance(
            TypoScriptFrontendController::class,
            $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock()
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($GLOBALS['TSFE'], $GLOBALS['LANG']);
    }

    public function testDoesNotSimulateWhenAlreadyInFrontendContext(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_FE);
        self::assertSame(null, FrontendSimulationUtility::simulateFrontendEnvironment());
    }

    public function testSimulatesInBackendContext(): void
    {
        if (VersionUtility::isCoreAtLeast14()) {
            self::markTestSkipped('TypoScriptFrontendController simulation is not available on TYPO3 v14');
        }
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_BE);

        FrontendSimulationUtility::simulateFrontendEnvironment();
        self::assertInstanceOf(TypoScriptFrontendController::class, $GLOBALS['TSFE']);

        unset($GLOBALS['TSFE'], $GLOBALS['LANG']);
    }

    public function testResetDoesNotRemoveInstanceInFrontendContext(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_FE);
        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
        FrontendSimulationUtility::resetFrontendEnvironment(null);
        self::assertNotNull($GLOBALS['TSFE']);
    }

    public function testResetRemovesSimulatedInstanceInBackendContext(): void
    {
        if (VersionUtility::isCoreAtLeast14()) {
            self::markTestSkipped('TypoScriptFrontendController simulation is not available on TYPO3 v14');
        }
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
        FrontendSimulationUtility::resetFrontendEnvironment(null);
        self::assertNull($GLOBALS['TSFE'] ?? null);
    }

    private function createRequestMock(int $requestType, ?TypoScriptFrontendController $tsfe = null): ServerRequest
    {
        $tsfe ??= $this->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
        return (new ServerRequest())
            ->withAttribute('applicationType', $requestType)
            ->withAttribute('frontend.controller', $tsfe);
    }
}
