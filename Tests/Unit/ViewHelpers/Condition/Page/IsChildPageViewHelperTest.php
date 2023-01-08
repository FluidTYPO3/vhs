<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyTypoScriptFrontendController;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class IsChildPageViewHelperTest
 */
class IsChildPageViewHelperTest extends AbstractViewHelperTestCase
{
    private ?PageRepository $pageRepository;

    protected function setUp(): void
    {
        $this->pageRepository = $this->getMockBuilder(PageRepository::class)
            ->setMethods(['getPage'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getPageRepository')->willReturn($this->pageRepository);

        $this->singletonInstances[PageService::class] = $pageService;

        $GLOBALS['TSFE'] = new DummyTypoScriptFrontendController();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE']);
    }

    public function testRendersThenIfChildPageAndIsSiteRootNotRespected(): void
    {
        $arguments = ['pageUid' => 0, 'then' => 'then', 'else' => 'else', 'respectSiteRoot' => false];
        $this->pageRepository->method('getPage')->willReturn(['is_siteroot' => false, 'pid' => 1]);
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    public function testRendersElseIfChildPageAndIsSiteRootRespected(): void
    {
        $arguments = ['pageUid' => 0, 'then' => 'then', 'else' => 'else', 'respectSiteRoot' => true];
        $this->pageRepository->method('getPage')->willReturn(['is_siteroot' => false, 'pid' => 1]);
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    public function testRendersElseIfSiteRootAndIsSiteRootRespected(): void
    {
        $arguments = ['pageUid' => 0, 'then' => 'then', 'else' => 'else', 'respectSiteRoot' => true];
        $this->pageRepository->method('getPage')->willReturn(['is_siteroot' => true, 'pid' => 1]);
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }
}
