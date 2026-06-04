<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Menu\BrowseViewHelper;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;

class BrowseViewHelperTest extends AbstractViewHelperTestCase
{
    public function testUsesRenderingContextRequestWhenResolvingCurrentPageUid(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createFrontendRequestForPage(111);
        $subRequest = $this->createFrontendRequestForPage(222);
        $seenPageUids = [];

        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPage', 'getMenu', 'getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getPage')->willReturnCallback(
            function (int $pageUid) use (&$seenPageUids): array {
                $seenPageUids[] = $pageUid;
                return ['uid' => $pageUid, 'pid' => 10];
            }
        );
        $pageService->method('getMenu')->willReturn([]);

        $this->renderingContext = $this->createRenderingContextWithRequest($subRequest);
        $subject = $this->buildViewHelperInstance(['usePageTitles' => true]);
        self::assertInstanceOf(BrowseViewHelper::class, $subject);
        $subject->injectPageService($pageService);

        self::assertSame('', $this->executeInstance($subject));
        self::assertSame(222, $seenPageUids[0]);
    }

    public function testReturnsEmptyStringWithoutPages(): void
    {
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPage', 'getMenu', 'getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();

        $arguments = [
            'currentPageUid' => 2,
            'usePageTitles' => true,
        ];

        $subject = $this->buildViewHelperInstance($arguments);
        self::assertInstanceOf(BrowseViewHelper::class, $subject);
        $subject->injectPageService($pageService);

        self::assertSame('', $this->executeInstance($subject, $arguments));
    }

    public function testReturnsEmptyStringWithoutPagesWithoutAsArgument(): void
    {
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPage', 'getMenu', 'getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();

        $arguments = [
            'currentPageUid' => 2,
            'usePageTitles' => true,
            'as' => '',
        ];

        $subject = $this->buildViewHelperInstance($arguments);
        self::assertInstanceOf(BrowseViewHelper::class, $subject);
        $subject->injectPageService($pageService);

        self::assertSame('', $this->executeInstance($subject, $arguments));
    }

    public function testRendersBrowseMenu(): void
    {
        $pages = [
            1 => [
                'uid' => 1,
                'pid' => 4,
                'doktype' => PageRepository::DOKTYPE_DEFAULT,
                'title' => 'First',
            ],
            2 => [
                'uid' => 2,
                'pid' => 4,
                'doktype' => PageRepository::DOKTYPE_DEFAULT,
                'title' => 'Second',
            ],
            3 => [
                'uid' => 3,
                'pid' => 4,
                'doktype' => PageRepository::DOKTYPE_DEFAULT,
                'title' => 'Third',
            ],
        ];

        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPage', 'getMenu', 'getRootLine', 'getItemLink'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getPage')->willReturn($pages[1]);
        $pageService->method('getMenu')->willReturn($pages);
        $pageService->method('getRootLine')->willReturn($pages);
        $pageService->method('getItemLink')->willReturn('link');

        $arguments = [
            'currentPageUid' => 2,
            'usePageTitles' => true,
        ];

        $subject = $this->buildViewHelperInstance($arguments);
        self::assertInstanceOf(BrowseViewHelper::class, $subject);
        $subject->injectPageService($pageService);

        $output = $this->executeInstance($subject, $arguments);

        self::assertSame(
            '<ul><li class="active">' . PHP_EOL .
            '<a href="link" title="First" class="active">First</a>' . PHP_EOL .
            '</li>' . PHP_EOL .
            '<li class="active">' . PHP_EOL .
            '<a href="link" title="First" class="active">First</a>' . PHP_EOL .
            '</li>' . PHP_EOL .
            '<li class="active">' . PHP_EOL .
            '<a href="link" title="First" class="active">First</a>' . PHP_EOL .
            '</li>' . PHP_EOL .
            '<li class="active">' . PHP_EOL .
            '<a href="link" title="Third" class="active">Third</a>' . PHP_EOL .
            '</li>' . PHP_EOL .
            '<li class="active">' . PHP_EOL .
            '<a href="link" title="Third" class="active">Third</a>' . PHP_EOL .
            '</li></ul>',
            $output
        );
    }

    private function createFrontendRequestForPage(int $pageUid): ServerRequest
    {
        $pageInformation = new PageInformation();
        $pageInformation->setId($pageUid);

        return (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.page.information', $pageInformation);
    }
}
