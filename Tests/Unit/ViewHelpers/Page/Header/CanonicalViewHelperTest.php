<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Page\Header;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Page\Header\CanonicalViewHelper;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class CanonicalViewHelperTest
 */
class CanonicalViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function returnsEmptyStringWithoutRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();
        $viewHelper = $this->buildViewHelperInstance(['pageUid' => 0]);
        self::assertInstanceOf(CanonicalViewHelper::class, $viewHelper);

        self::assertSame('', $viewHelper->render());
    }

    #[Test]
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
                    'setArgumentsToBeExcludedFromQueryString',
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
        $uriBuilder->method('setArgumentsToBeExcludedFromQueryString')->willReturnSelf();
        $uriBuilder->method('build')->willReturn('https://example.test/subrequest-page');
        GeneralUtility::addInstance(UriBuilder::class, $uriBuilder);

        GeneralUtility::setSingletonInstance(
            PageRenderer::class,
            $this->getMockBuilder(PageRenderer::class)->disableOriginalConstructor()->getMock()
        );

        $viewHelper = $this->buildViewHelperInstance(['pageUid' => 0]);
        self::assertInstanceOf(CanonicalViewHelper::class, $viewHelper);
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
