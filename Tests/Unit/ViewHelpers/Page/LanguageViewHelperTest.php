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
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class LanguageViewHelperTest
 */
class LanguageViewHelperTest extends AbstractViewHelperTestCase
{
    private PageService&MockObject $pageService;

    protected function setUp(): void
    {
        $this->pageService = $this->singletonInstances[PageService::class] = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['hidePageForLanguageUid'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();

        $pageInformation = new PageInformation();
        $pageInformation->setId(1);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.page.information', $pageInformation);
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
    }

    public function testRender(): void
    {
        $this->pageService->method('hidePageForLanguageUid')->willReturn(false);
        $this->assertEmpty($this->executeViewHelper());
    }

    public function testRenderWithoutRequestAndWithoutPageUidReturnsEmptyString(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();

        self::assertSame('', $this->executeViewHelper(['languages' => [0 => 'Default'], 'pageUid' => 0]));
    }

    public function testRenderUsesRenderingContextRequestWhenResolvingCurrentPageUid(): void
    {
        $globalPageInformation = new PageInformation();
        $globalPageInformation->setId(111);
        $subRequestPageInformation = new PageInformation();
        $subRequestPageInformation->setId(222);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.page.information', $globalPageInformation);
        $this->renderingContext = $this->createRenderingContextWithRequest(
            (new ServerRequest())
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
                ->withAttribute('frontend.page.information', $subRequestPageInformation)
        );
        $seenPageUids = [];
        $this->pageService->method('hidePageForLanguageUid')->willReturnCallback(
            function (int $pageUid) use (&$seenPageUids): bool {
                $seenPageUids[] = $pageUid;
                return false;
            }
        );

        $this->executeViewHelper(['languages' => [0 => 'Default'], 'pageUid' => 0]);

        self::assertSame(222, $seenPageUids[0]);
    }
}
