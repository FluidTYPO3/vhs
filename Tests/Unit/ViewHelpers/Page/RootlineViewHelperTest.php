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
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class RootlineViewHelperTest
 */
class RootlineViewHelperTest extends AbstractViewHelperTestCase
{
    private ?PageService $pageService;

    protected function setUp(): void
    {
        $this->pageService = $this->singletonInstances[PageService::class] = $this->getMockBuilder(PageService::class)
            ->setMethods(['getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();

        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TSFE']->id = 123;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE']);
    }

    public function testRenderReturnsRootLine(): void
    {
        $rootLine = [['uid' => 1], ['uid' => 2]];
        $this->pageService->method('getRootLine')->willReturn($rootLine);
        $this->assertSame($rootLine, $this->executeViewHelper(['pageUid' => 123]));
    }

    public function testRenderUsesPageUidFromTsfe(): void
    {
        $rootLine = [['uid' => 1], ['uid' => 2]];
        $this->pageService->method('getRootLine')->willReturn($rootLine);
        $this->assertSame($rootLine, $this->executeViewHelper(['pageUid' => 0]));
    }
}
