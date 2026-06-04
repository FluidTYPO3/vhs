<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Page\Header;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Page\Header\AlternateViewHelper;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Class AlternateViewHelperTest
 */
class AlternateViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function returnsEmptyStringWithoutRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();
        $viewHelper = $this->buildViewHelperInstance(['languages' => ['x-default']]);
        self::assertInstanceOf(AlternateViewHelper::class, $viewHelper);

        self::assertSame('', $viewHelper->render());
    }

    /**
     * @test
     */
    public function usesRenderingContextRequestWhenResolvingCurrentPageUid(): void
    {
        $globalRequest = $this->createExtbaseRequestForPage(111);
        $subRequest = $this->createExtbaseRequestForPage(222);
        $GLOBALS['TYPO3_REQUEST'] = $globalRequest;

        $uriBuilder = $this->getMockBuilder(UriBuilder::class)
            ->onlyMethods(
                [
                    'setRequest',
                    'reset',
                    'setTargetPageUid',
                    'setCreateAbsoluteUri',
                    'setAddQueryString',
                    'setArguments',
                    'build',
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $uriBuilder->expects($this->once())->method('setRequest')->with($subRequest)->willReturnSelf();
        $uriBuilder->expects($this->once())->method('reset')->willReturnSelf();
        $uriBuilder->expects($this->once())->method('setTargetPageUid')->with(222)->willReturnSelf();
        $uriBuilder->method('setCreateAbsoluteUri')->willReturnSelf();
        $uriBuilder->method('setAddQueryString')->willReturnSelf();
        $uriBuilder->method('setArguments')->willReturnSelf();
        $uriBuilder->method('build')->willReturn('https://example.test/subrequest-page');
        GeneralUtility::addInstance(UriBuilder::class, $uriBuilder);

        GeneralUtility::setSingletonInstance(
            PageRenderer::class,
            $this->getMockBuilder(PageRenderer::class)->disableOriginalConstructor()->getMock()
        );

        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['hidePageForLanguageUid'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->expects($this->once())->method('hidePageForLanguageUid')->with(222, 0, false)->willReturn(false);

        $viewHelper = $this->buildViewHelperInstance(['languages' => ['x-default']]);
        self::assertInstanceOf(AlternateViewHelper::class, $viewHelper);
        $viewHelper->injectPageService($pageService);
        $this->setInaccessiblePropertyValue($viewHelper, 'tagBuilder', new TagBuilder());
        $viewHelper->setRenderingContext($this->createRenderingContextWithRequest($subRequest));

        $viewHelper->render();
    }

    private function createExtbaseRequestForPage(int $pageUid): Request
    {
        $pageInformation = new PageInformation();
        $pageInformation->setId($pageUid);

        $request = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.page.information', $pageInformation)
            ->withAttribute('extbase', new ExtbaseRequestParameters());

        return new Request($request);
    }

}
