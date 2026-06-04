<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Page\PageInformation;

class LinkViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var PageService&MockObject
     */
    protected $pageService;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->pageService = $this->getMockBuilder(PageService::class)->onlyMethods(
            [
                'getPage',
                'getShortcutTargetPage',
                'shouldUseShortcutTarget',
                'shouldUseShortcutUid',
                'hidePageForLanguageUid'
            ]
        )->getMock();
        $this->pageService->expects($this->any())->method('getShortcutTargetPage')->willReturnArgument(0);
        $pageInformation = new PageInformation();
        $pageInformation->setId(1);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('frontend.page.information', $pageInformation);
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);

        $uriBuilderMockBuilder = $this->getMockBuilder(UriBuilder::class)
            ->onlyMethods(['buildFrontendUri', 'build'])
            ->disableOriginalConstructor();
        if (!method_exists(UriBuilder::class, 'setUseCacheHash')) {
            $uriBuilderMockBuilder->addMethods(['setUseCacheHash']);
        }
        $uriBuilder = $uriBuilderMockBuilder->getMock();
        GeneralUtility::addInstance(UriBuilder::class, $uriBuilder);
    }

    protected function createInstance(): LinkViewHelper
    {
        $instance = parent::createInstance();
        self::assertInstanceOf(LinkViewHelper::class, $instance);
        $instance->injectPageService($this->pageService);
        return $instance;
    }

    /**
     * @test
     */
    public function generatesPageLinks(): void
    {
        $this->pageService->expects($this->once())->method('getPage')->willReturn(['uid' => '1', 'title' => 'test']);
        $arguments = ['pageUid' => 1];
        $result = $this->executeViewHelper($arguments, [], null, 'Vhs');
        $this->assertNotEmpty($result);
    }

    /**
     * @test
     */
    public function generatesNullLinkOnZeroPageUid(): void
    {
        $arguments = ['pageUid' => 0];
        $this->pageService->expects($this->once())->method('getPage')->willReturn([]);
        $result = $this->executeViewHelper($arguments, [], null, 'Vhs');
        $this->assertSame('', $result);
    }

    /**
     * @test
     */
    public function usesRenderingContextRequestWhenResolvingCurrentPageUid(): void
    {
        $globalPageInformation = new PageInformation();
        $globalPageInformation->setId(111);
        $subRequestPageInformation = new PageInformation();
        $subRequestPageInformation->setId(222);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.page.information',
            $globalPageInformation
        );
        $this->renderingContext = $this->createRenderingContextWithRequest(
            (new ServerRequest())->withAttribute('frontend.page.information', $subRequestPageInformation)
        );
        $seenPageUids = [];
        $this->pageService->expects($this->once())->method('getPage')->willReturnCallback(
            function (int $pageUid) use (&$seenPageUids): array {
                $seenPageUids[] = $pageUid;
                return [];
            }
        );

        self::assertSame('', $this->executeViewHelper(['pageUid' => 0], [], null, 'Vhs'));
        self::assertSame(222, $seenPageUids[0]);
    }

    /**
     * @test
     */
    public function passesRenderingContextRequestToPageService(): void
    {
        $globalPageInformation = new PageInformation();
        $globalPageInformation->setId(111);
        $subRequestPageInformation = new PageInformation();
        $subRequestPageInformation->setId(222);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.page.information',
            $globalPageInformation
        );
        $subRequest = (new ServerRequest())->withAttribute('frontend.page.information', $subRequestPageInformation);
        $this->renderingContext = $this->createRenderingContextWithRequest($subRequest);
        $this->pageService->expects($this->once())->method('getPage')->willReturn([]);

        self::assertSame('', $this->executeViewHelper(['pageUid' => 0], [], null, 'Vhs'));
        self::assertSame($subRequest, $this->callInaccessibleMethod($this->pageService, 'getRequest'));
    }

    /**
     * @disabledtest
     */
    public function generatesPageLinksWithCustomTitle(): void
    {
        $this->pageService->expects($this->never())->method('getPage');
        $arguments = ['pageUid' => 1, 'pageTitleAs' => 'title'];
        $result = $this->executeViewHelperUsingTagContent('customtitle', $arguments, [], 'Vhs');
        self::assertIsString($result);
        $this->assertStringContainsString('customtitle', $result);
    }

    /**
     * @disabledtest
     */
    public function generatesPageWizardLinks(): void
    {
        $this->pageService->expects($this->never())->method('getPage');
        $arguments = ['pageUid' => '1 2 3 4 5 foo=bar&baz=123'];
        $result = $this->executeViewHelper($arguments, [], null, 'Vhs');
        $this->assertNotEmpty($result);
    }
}
