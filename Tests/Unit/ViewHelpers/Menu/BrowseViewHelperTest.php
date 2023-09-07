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
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

class BrowseViewHelperTest extends AbstractViewHelperTestCase
{
    public function testReturnsEmptyStringWithoutPages(): void
    {
        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['getPage', 'getMenu', 'getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();

        $arguments = [
            'currentPageUid' => 2,
            'usePageTitles' => true,
        ];

        $GLOBALS['TSFE'] = (object) ['id' => 2];

        $subject = $this->buildViewHelperInstance($arguments);
        $subject->injectPageService($pageService);

        self::assertSame('', $this->executeInstance($subject, $arguments));
    }

    public function testReturnsEmptyStringWithoutPagesWithoutAsArgument(): void
    {
        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['getPage', 'getMenu', 'getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();

        $arguments = [
            'currentPageUid' => 2,
            'usePageTitles' => true,
            'as' => '',
        ];

        $GLOBALS['TSFE'] = (object) ['id' => 2];

        $subject = $this->buildViewHelperInstance($arguments);
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
            ->setMethods(['getPage', 'getMenu', 'getRootLine', 'getItemLink'])
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

        $GLOBALS['TSFE'] = (object) ['id' => 2];

        $subject = $this->buildViewHelperInstance($arguments);
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
}
