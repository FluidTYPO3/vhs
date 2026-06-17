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

/**
 * Class AbsoluteUrlViewHelperTest
 */
class AbsoluteUrlViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender(): void
    {
        $expectedUrl = 'https://example.test/sub/page?a=1';
        $normalizedParams = $this->getMockBuilder(NormalizedParams::class)
            ->disableOriginalConstructor()
            ->getMock();
        $normalizedParams->method('getRequestUrl')->willReturn($expectedUrl);

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest($expectedUrl))->withAttribute(
            'normalizedParams',
            $normalizedParams
        );
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);

        $this->assertSame($expectedUrl, $this->executeViewHelper());
    }
}
