<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * @protection on
 * @package Vhs
 */
class LinkViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var PageService
     */
    protected $pageService;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->pageService = $this->getMockBuilder(PageService::class)->setMethods(
            [
                'getPage',
                'getShortcutTargetPage',
                'shouldUseShortcutTarget',
                'shouldUseShortcutUid',
                'hidePageForLanguageUid'
            ]
        )->getMock();
        $this->pageService->expects($this->any())->method('getShortcutTargetPage')->willReturnArgument(0);
        $GLOBALS['TSFE'] = new DummyTypoScriptFrontendController();

        $uriBuilder = $this->getMockBuilder(UriBuilder::class)
            ->setMethods(['buildFrontendUri', 'build', 'setUseCacheHash'])
            ->disableOriginalConstructor()
            ->getMock();
        GeneralUtility::addInstance(UriBuilder::class, $uriBuilder);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE']);
    }

    /**
     * @return AbstractViewHelper
     */
    protected function createInstance()
    {
        $instance = parent::createInstance();
        $instance->injectPageService($this->pageService);
        return $instance;
    }

    /**
     * @test
     */
    public function generatesPageLinks()
    {
        $this->pageService->expects($this->once())->method('getPage')->willReturn(['uid' => '1', 'title' => 'test']);
        $arguments = ['pageUid' => 1];
        $result = $this->executeViewHelper($arguments, [], null, 'Vhs');
        $this->assertNotEmpty($result);
    }

    /**
     * @test
     */
    public function generatesNullLinkOnZeroPageUid()
    {
        $arguments = ['pageUid' => 0];
        $this->pageService->expects($this->once())->method('getPage')->willReturn(null);
        $result = $this->executeViewHelper($arguments, [], null, 'Vhs');
        $this->assertNull($result);
    }

    /**
     * @disabledtest
     */
    public function generatesPageLinksWithCustomTitle()
    {
        $this->pageService->expects($this->never())->method('getPage');
        $arguments = ['pageUid' => 1, 'pageTitleAs' => 'title'];
        $result = $this->executeViewHelperUsingTagContent('customtitle', $arguments, [], 'Vhs');
        $this->assertContains('customtitle', $result);
    }

    /**
     * @disabledtest
     */
    public function generatesPageWizardLinks()
    {
        $this->pageService->expects($this->never())->method('getPage');
        $arguments = ['pageUid' => '1 2 3 4 5 foo=bar&baz=123'];
        $result = $this->executeViewHelper($arguments, [], null, 'Vhs');
        $this->assertNotEmpty($result);
    }
}
