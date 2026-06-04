<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class LanguageMenuViewHelperTest
 */
class LanguageMenuViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function usesRenderingContextRequestForFallbackUriAndCurrentPageUid(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequest('/outer/page-111', 111);
        $subRequest = $this->createRequest('/inner/page-222', 222);

        $viewHelper = $this->createInstance();
        $viewHelper->setRenderingContext($this->createRenderingContextWithRequest($subRequest));

        self::assertSame('/inner/page-222', $this->callInaccessibleMethod($viewHelper, 'getFallbackRequestUri'));
        self::assertSame(222, $this->callInaccessibleMethod($viewHelper, 'getCurrentPageUid'));
    }

    private function createRequest(string $requestUri, int $pageUid): ServerRequest
    {
        $pageInformation = new PageInformation();
        $pageInformation->setId($pageUid);

        $normalizedParams = $this->getMockBuilder(NormalizedParams::class)
            ->onlyMethods(['getRequestUri'])
            ->disableOriginalConstructor()
            ->getMock();
        $normalizedParams->method('getRequestUri')->willReturn($requestUri);

        return (new ServerRequest())
            ->withAttribute('normalizedParams', $normalizedParams)
            ->withAttribute('frontend.page.information', $pageInformation);
    }
}
