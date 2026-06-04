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
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Class StaticPrefixViewHelperTest
 */
class StaticPrefixViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender(): void
    {
        $this->renderingContext = $this->createRenderingContextWithRequest(new ServerRequest());

        $this->assertEmpty($this->executeViewHelper());
    }

    public function testRenderWithoutRequestReturnsEmptyString(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();

        self::assertSame('', $this->executeViewHelper());
    }

    public function testRenderReturnsConfiguredPrefix(): void
    {
        $frontendTypoScript = $this->createFrontendTypoScript('/static/');
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('frontend.typoscript', $frontendTypoScript);
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);

        self::assertSame('/static/', $this->executeViewHelper());
    }

    public function testRenderUsesRenderingContextRequest(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'frontend.typoscript',
            $this->createFrontendTypoScript('/outer/')
        );
        $this->renderingContext = $this->createRenderingContextWithRequest(
            (new ServerRequest())->withAttribute('frontend.typoscript', $this->createFrontendTypoScript('/inner/'))
        );

        self::assertSame('/inner/', $this->executeViewHelper());
    }

    private function createFrontendTypoScript(string $prependPath): object
    {
        return new class ($prependPath) {
            public function __construct(private readonly string $prependPath)
            {
            }

            public function hasSetup(): bool
            {
                return true;
            }

            public function getSetupArray(): array
            {
                return ['plugin.' => ['tx_vhs.' => ['settings.' => ['prependPath' => $this->prependPath]]]];
            }
        };
    }
}
