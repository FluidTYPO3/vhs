<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * @protection on
 * @package Vhs
 */
class LinkViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->pageRepository = $this->getMock(PageRepository::class, array('getPage'));
        $GLOBALS['TSFE'] = (object) array('sys_page' => $this->pageRepository);
    }

    /**
     * @test
     */
    public function generatesPageLinks()
    {
        $this->pageRepository->expects($this->once())->method('getPage')->willReturn(array('uid' => '1', 'title' => 'test'));
        $arguments = array('pageUid' => 1);
        $result = $this->executeViewHelper($arguments, array(), null, 'Vhs');
        $this->assertNotEmpty($result);
    }

    /**
     * @test
     */
    public function generatesNullLinkOnZeroPageUid()
    {
        $arguments = array('pageUid' => 0);
        $this->pageRepository->expects($this->once())->method('getPage')->willReturn(null);
        $result = $this->executeViewHelper($arguments, array(), null, 'Vhs');
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function generatesPageLinksWithCustomTitle()
    {
        $this->pageRepository->expects($this->never())->method('getPage');
        $arguments = array('pageUid' => 1, 'pageTitleAs' => 'title');
        $result = $this->executeViewHelperUsingTagContent('Text', 'customtitle', $arguments, array(), 'Vhs');
        $this->assertContains('customtitle', $result);
    }

    /**
     * @test
     */
    public function generatesPageWizardLinks()
    {
        $this->pageRepository->expects($this->never())->method('getPage');
        $arguments = array('pageUid' => '1 2 3 4 5 foo=bar&baz=123');
        $result = $this->executeViewHelper($arguments, array(), null, 'Vhs');
        $this->assertNotEmpty($result);
    }
}
