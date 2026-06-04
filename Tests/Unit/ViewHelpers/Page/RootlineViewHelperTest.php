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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RootlineViewHelperTest
 */
class RootlineViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var PageService&MockObject
     */
    private PageService $pageService;

    protected function setUp(): void
    {
        $this->pageService = $this->singletonInstances[PageService::class] = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();
    }

    public function testRenderReturnsRootLine(): void
    {
        $rootLine = [['uid' => 1], ['uid' => 2]];
        $this->pageService->method('getRootLine')->willReturn($rootLine);
        $this->assertSame($rootLine, $this->executeViewHelper(['pageUid' => 123]));
    }

    public function testRenderClearsPageServiceRequestWithoutRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $rootLine = [['uid' => 1], ['uid' => 2]];
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getRootLine', 'setRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->expects(self::once())->method('setRequest')->with(null);
        $pageService->expects(self::once())->method('getRootLine')->with(123)->willReturn($rootLine);
        GeneralUtility::setSingletonInstance(PageService::class, $pageService);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();

        $this->assertSame($rootLine, $this->executeViewHelper(['pageUid' => 123]));
    }

    public function testRenderDelegatesCurrentPageResolutionToPageService(): void
    {
        $rootLine = [['uid' => 1], ['uid' => 2]];
        $this->pageService->expects(self::once())->method('getRootLine')->with(null)->willReturn($rootLine);
        $this->assertSame($rootLine, $this->executeViewHelper(['pageUid' => 0]));
    }
}
