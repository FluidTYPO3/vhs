<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class IsChildPageViewHelperTest
 */
class IsChildPageViewHelperTest extends AbstractViewHelperTestCase
{
    private ?PageRepository $pageRepository;

    protected function setUp(): void
    {
        $this->pageRepository = $this->getMockBuilder(PageRepository::class)
            ->onlyMethods(['getPage'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getPageRepository')->willReturn($this->pageRepository);

        $this->singletonInstances[PageService::class] = $pageService;

        parent::setUp();

        $pageInformation = new PageInformation();
        $pageInformation->setId(1);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('frontend.page.information', $pageInformation);
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
    }

    public function testRendersElseWithoutRequestAndWithoutPageUid(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();

        $result = $this->executeViewHelper(['pageUid' => 0, 'then' => 'then', 'else' => 'else']);

        $this->assertEquals('else', $result);
    }

    public function testRendersThenIfChildPageAndIsSiteRootNotRespected(): void
    {
        $arguments = ['pageUid' => 0, 'then' => 'then', 'else' => 'else', 'respectSiteRoot' => false];
        self::assertNotNull($this->pageRepository);
        $this->pageRepository->method('getPage')->willReturn(['is_siteroot' => false, 'pid' => 1]);
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    public function testRendersElseIfChildPageAndIsSiteRootRespected(): void
    {
        $arguments = ['pageUid' => 0, 'then' => 'then', 'else' => 'else', 'respectSiteRoot' => true];
        self::assertNotNull($this->pageRepository);
        $this->pageRepository->method('getPage')->willReturn(['is_siteroot' => false, 'pid' => 1]);
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    public function testRendersElseIfSiteRootAndIsSiteRootRespected(): void
    {
        $arguments = ['pageUid' => 0, 'then' => 'then', 'else' => 'else', 'respectSiteRoot' => true];
        self::assertNotNull($this->pageRepository);
        $this->pageRepository->method('getPage')->willReturn(['is_siteroot' => true, 'pid' => 1]);
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    public function testUsesRenderingContextRequestWhenResolvingCurrentPageUid(): void
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
        self::assertNotNull($this->pageRepository);
        $this->pageRepository->method('getPage')->willReturnCallback(
            function (int $pageUid) use (&$seenPageUids): array {
                $seenPageUids[] = $pageUid;
                return ['is_siteroot' => false, 'pid' => 1];
            }
        );

        $this->executeViewHelper(['pageUid' => 0, 'then' => 'then', 'else' => 'else']);

        self::assertSame(222, $seenPageUids[0]);
    }
}
