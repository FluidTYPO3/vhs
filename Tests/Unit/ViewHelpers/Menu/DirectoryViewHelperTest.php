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

class DirectoryViewHelperTest extends AbstractViewHelperTestCase
{
    public function testReturnsNullWithoutPages(): void
    {
        $output = $this->executeViewHelper();
        self::assertNull($output);
    }

    public function testRendersMenu(): void
    {
        $page = [
            'uid' => 1,
            'title' => 'page',
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
        ];

        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['getMenu', 'getRootLine', 'isCurrent', 'getItemLink'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getMenu')->willReturn([$page]);
        $pageService->method('getRootLine')->willReturn([$page]);
        $pageService->method('isCurrent')->willReturn(true);
        $pageService->method('getItemLink')->willReturn('link');

        $arguments = ['pages' => [1]];

        $subject = $this->buildViewHelperInstance($arguments);
        $subject->injectPageService($pageService);

        $output = $this->executeInstance($subject, $arguments);
        self::assertSame(
            '<ul><li class="active current">' . PHP_EOL .
            '<a href="link" title="page" class="active current">page</a>' . PHP_EOL .
            '</li></ul>',
            $output
        );
    }
}
