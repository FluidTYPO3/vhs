<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Condition\Page\HasSubpagesViewHelper;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class HasSubpagesViewHelperTest
 */
class HasSubpagesViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRenderWithAPageThatHasSubpages(): void
    {
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getMenu'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->expects($this->any())->method('getMenu')->will($this->returnValue(['childpage']));

        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 1
        ];
        $instance = $this->buildViewHelperInstance($arguments);
        self::assertInstanceOf(HasSubpagesViewHelper::class, $instance);
        $instance::setPageService($pageService);
        $result = $instance->initializeArgumentsAndRender();
        $this->assertEquals('then', $result);
    }

    public function testRenderWithoutRequestAndWithoutPageUidRendersElse(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getMenu'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->expects(self::never())->method('getMenu');

        $instance = $this->buildViewHelperInstance(['then' => 'then', 'else' => 'else', 'pageUid' => 0]);
        self::assertInstanceOf(HasSubpagesViewHelper::class, $instance);
        $instance->setRenderingContext($this->createRenderingContextWithoutRequest());
        $instance::setPageService($pageService);

        $this->assertEquals('else', $instance->initializeArgumentsAndRender());
    }

    public function testRenderWithExplicitPageUidClearsPageServiceRequestWithoutRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getMenu', 'setRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->expects(self::once())->method('setRequest')->with(null);
        $pageService->expects(self::once())->method('getMenu')->willReturn(['childpage']);

        $instance = $this->buildViewHelperInstance(['then' => 'then', 'else' => 'else', 'pageUid' => 1]);
        self::assertInstanceOf(HasSubpagesViewHelper::class, $instance);
        $instance->setRenderingContext($this->createRenderingContextWithoutRequest());
        $instance::setPageService($pageService);

        $this->assertEquals('then', $instance->initializeArgumentsAndRender());
    }

    public function testRenderWithAPageWithoutSubpages(): void
    {
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getMenu'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->expects($this->any())->method('getMenu')->will($this->returnValue([]));

        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 1
        ];
        $instance = $this->buildViewHelperInstance($arguments);
        self::assertInstanceOf(HasSubpagesViewHelper::class, $instance);
        $instance::setPageService($pageService);
        $result = $instance->initializeArgumentsAndRender();
        $this->assertEquals('else', $result);
    }

    public function testUsesRenderingContextRequestWhenResolvingCurrentPageUid(): void
    {
        $globalPageInformation = new PageInformation();
        $globalPageInformation->setId(111);
        $subRequestPageInformation = new PageInformation();
        $subRequestPageInformation->setId(222);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.page.information',
            $globalPageInformation
        );
        $this->renderingContext = $this->createRenderingContextWithRequest(
            (new ServerRequest())->withAttribute('frontend.page.information', $subRequestPageInformation)
        );

        $seenPageUids = [];
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getMenu'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getMenu')->willReturnCallback(
            function (int $pageUid) use (&$seenPageUids): array {
                $seenPageUids[] = $pageUid;
                return ['childpage'];
            }
        );

        $instance = $this->buildViewHelperInstance(['then' => 'then', 'else' => 'else', 'pageUid' => 0]);
        self::assertInstanceOf(HasSubpagesViewHelper::class, $instance);
        $instance::setPageService($pageService);
        $instance->initializeArgumentsAndRender();

        self::assertSame(222, $seenPageUids[0]);
    }
}
