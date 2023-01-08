<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Page;

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
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class InfoViewHelperTest
 */
class InfoViewHelperTest extends AbstractViewHelperTestCase
{
    private ?PageRepository $pageRepository;

    protected function setUp(): void
    {
        $this->pageRepository = $this->getMockBuilder(PageRepository::class)
            ->setMethods(['getPage_noCheck'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getPageRepository')->willReturn($this->pageRepository);

        $this->singletonInstances[PageService::class] = $pageService;

        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE']);
    }

    public function testUsesPageUidFromTsfe(): void
    {
        $GLOBALS['TSFE']->id = 123;
        $this->pageRepository->expects(self::once())->method('getPage_noCheck')->with(123);
        $this->executeViewHelper(['pageUid' => 0, 'field' => 'tx_foo_bar']);
    }

    public function testReturnsCorrectSingleFieldValue()
    {
        $expectedFieldValue = 42;

        $this->pageRepository->expects($this->any())->method('getPage_noCheck')->willReturn(['tx_foo_bar' => $expectedFieldValue]);
        $this->assertEquals($expectedFieldValue, $this->executeViewHelper(['pageUid' => 12, 'field' => 'tx_foo_bar']));
    }

    public function testReturnsPageRowIfNoFieldGiven()
    {
        $expectedRow = ['uid' => 42, 'tx_foo_bar' => 'baz'];

        $this->pageRepository->expects($this->any())->method('getPage_noCheck')->willReturn($expectedRow);
        $this->assertEquals($expectedRow, $this->executeViewHelper(['pageUid' => 42]));
    }
}
