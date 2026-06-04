<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Uri;

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

/**
 * Class RequestViewHelperTest
 */
class RequestViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function rendersUrl(): void
    {
        $expectedUrl = 'https://example.test/?foo=1';
        $normalizedParams = $this->getMockBuilder(NormalizedParams::class)
            ->disableOriginalConstructor()
            ->getMock();
        $normalizedParams->method('getRequestUrl')->willReturn($expectedUrl);

        $serverRequest = new ServerRequest($expectedUrl);
        $serverRequest = $serverRequest->withAttribute('normalizedParams', $normalizedParams);
        $GLOBALS['TYPO3_REQUEST'] = $serverRequest;
        $this->renderingContext = $this->createRenderingContextWithRequest($serverRequest);

        $test = $this->executeViewHelper();
        $this->assertSame($expectedUrl, $test);
    }
}
