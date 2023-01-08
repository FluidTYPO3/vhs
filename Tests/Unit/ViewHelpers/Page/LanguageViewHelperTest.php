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

/**
 * Class LanguageViewHelperTest
 */
class LanguageViewHelperTest extends AbstractViewHelperTestCase
{
    private ?PageService $pageService;

    protected function setUp(): void
    {
        $this->pageService = $this->singletonInstances[PageService::class] = $this->getMockBuilder(PageService::class)
            ->setMethods(['hidePageForLanguageUid'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();

        $GLOBALS['TSFE'] = new DummyTypoScriptFrontendController();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TSFE']);
    }

    public function testRender()
    {
        $this->pageService->method('hidePageForLanguageUid')->willReturn(false);
        $this->assertEmpty($this->executeViewHelper());
    }
}
