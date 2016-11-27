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
use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * @protection on
 * @package Vhs
 */
class LinkViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @var PageService
     */
    protected $pageService;

    /**
     * @return void
     */
    public function setUp()
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
        #$GLOBALS['TSFE'] = (object) array('sys_page' => $this->pageService);
        #$GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->setMethods('exec_SELECTgetSingleRow')->getMock();
        #$GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTgetSingleRow')->willReturn(null);
    }

    /**
     * @return AbstractViewHelper
     */
    protected function createInstance()
    {
        $className = $this->getViewHelperClassName();
        /** @var AbstractViewHelper $instance */
        $instance = $this->objectManager->get($className);
        $instance->initialize();
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
