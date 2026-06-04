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
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class FrontendSimulationUtilityTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        unset($GLOBALS['TSFE'], $GLOBALS['LANG']);

        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['FE']['enable_mount_pids'] = false;

        $siteLanguage = $this->getMockBuilder(SiteLanguage::class)->disableOriginalConstructor()->getMock();

        $site = $this->getMockBuilder(Site::class)
            ->onlyMethods(['getDefaultLanguage'])
            ->disableOriginalConstructor()
            ->getMock();
        $site->method('getDefaultLanguage')->willReturn($siteLanguage);

        $siteFinder = $this->getMockBuilder(SiteFinderProxy::class)
            ->onlyMethods(['getAllSites'])
            ->disableOriginalConstructor()
            ->getMock();
        $siteFinder->method('getAllSites')->willReturn([$site]);

        $frontendUserAuthentication = $this->getMockBuilder(FrontendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        GeneralUtility::addInstance(SiteFinderProxy::class, $siteFinder);
        GeneralUtility::addInstance(FrontendUserAuthentication::class, $frontendUserAuthentication);
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
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_BE);

        self::assertNull(FrontendSimulationUtility::simulateFrontendEnvironment());
        self::assertIsObject($GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.controller'));
        self::assertInstanceOf(
            ContentObjectRenderer::class,
            $GLOBALS['TYPO3_REQUEST']->getAttribute('currentContentObject')
        );
    }

    public function testResetDoesNotChangeRequestInFrontendContext(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_FE);
        $request = $GLOBALS['TYPO3_REQUEST'];

        FrontendSimulationUtility::resetFrontendEnvironment(null);
        self::assertSame($request, $GLOBALS['TYPO3_REQUEST']);
    }

    public function testResetRestoresBackendRequest(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $request = $GLOBALS['TYPO3_REQUEST'];
        FrontendSimulationUtility::simulateFrontendEnvironment();

        FrontendSimulationUtility::resetFrontendEnvironment(null);
        self::assertSame($request, $GLOBALS['TYPO3_REQUEST']);
    }

    public function testResetIgnoresLegacyTsfeBackupArgument(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestMock(SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $request = $GLOBALS['TYPO3_REQUEST'];
        $toBeRestored = new \stdClass();

        FrontendSimulationUtility::simulateFrontendEnvironment();
        FrontendSimulationUtility::resetFrontendEnvironment($toBeRestored);

        self::assertSame($request, $GLOBALS['TYPO3_REQUEST']);
        self::assertArrayNotHasKey('TSFE', $GLOBALS);
    }

    private function createRequestMock(int $requestType): ServerRequest
    {
        return (new ServerRequest())->withAttribute('applicationType', $requestType);
    }
}
