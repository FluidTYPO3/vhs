<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Class ImageViewHelperTest
 */
class ImageViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function preprocessSourceUriWithoutRequestKeepsSourceRelative(): void
    {
        self::assertNotSame(
            '',
            AbstractMediaViewHelper::preprocessSourceUri('fileadmin/test.mp4', ['relative' => false], null)
        );
    }

    /**
     * @test
     */
    public function usesRenderingContextRequestWhenPreprocessingSourceUri(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://outer.example/outer/page-111'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);
        $subRequest = (new ServerRequest('https://inner.example/sub/page-222'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $viewHelper = $this->buildViewHelperInstance(
            [
                'src' => '/fileadmin/image.jpg',
                'relative' => false,
                'alt' => 'Example image',
            ]
        );
        $viewHelper->setRenderingContext($this->createRenderingContextWithRequest($subRequest));
        $this->setInaccessiblePropertyValue($viewHelper, 'mediaSource', '/fileadmin/image.jpg');
        $this->setInaccessiblePropertyValue($viewHelper, 'imageInfo', [640, 480]);

        self::assertStringContainsString(
            'src="https://inner.example/sub/fileadmin/image.jpg"',
            $viewHelper->renderTag()
        );
    }
}
