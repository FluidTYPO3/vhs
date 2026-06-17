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
use FluidTYPO3\Vhs\ViewHelpers\Menu\ListViewHelper;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ServerRequest;

class ListViewHelperTest extends AbstractViewHelperTestCase
{
    public function testReturnsEmptyStringWithoutPages(): void
    {
        $output = $this->executeViewHelper();
        self::assertSame('', $output);
    }

    public function testRendersMenu(): void
    {
        $page = [
            'uid' => 1,
            'title' => 'page',
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
        ];

        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getMenu', 'getPage', 'getRootLine', 'isCurrent', 'getItemLink', 'getShortcutTargetPage'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getPage')->willReturn($page);
        $pageService->method('getMenu')->willReturn([$page]);
        $pageService->method('getRootLine')->willReturn([$page]);
        $pageService->method('isCurrent')->willReturn(true);
        $pageService->method('getItemLink')->willReturn('link');
        $pageService->method('getShortcutTargetPage')->willReturnArgument(0);

        $arguments = ['pages' => [1]];

        $subject = $this->buildViewHelperInstance($arguments);
        self::assertInstanceOf(ListViewHelper::class, $subject);
        $subject->injectPageService($pageService);

        $output = $this->executeInstance($subject, $arguments);
        self::assertSame(
            '<ul><li class="active current">' . PHP_EOL .
            '<a href="link" title="page" class="active current">page</a>' . PHP_EOL .
            '</li></ul>',
            $output
        );
    }

    public function testPassesRenderingContextRequestToPageService(): void
    {
        $subRequest = new ServerRequest();
        $this->renderingContext = $this->createRenderingContextWithRequest($subRequest);
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['setRequest', 'getPage'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->expects($this->once())->method('setRequest')->with($this->identicalTo($subRequest));
        $pageService->method('getPage')->willReturn([]);

        $arguments = ['pages' => [1]];
        $subject = $this->buildViewHelperInstance($arguments);
        self::assertInstanceOf(ListViewHelper::class, $subject);
        $subject->injectPageService($pageService);

        self::assertSame('', $this->executeInstance($subject, $arguments));
    }
}
