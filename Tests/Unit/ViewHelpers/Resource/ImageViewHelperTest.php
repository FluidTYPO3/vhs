<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class ImageViewHelperTest
 */
class ImageViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender(): void
    {
        $this->assertEmpty($this->executeViewHelper());
    }

    public function testPreprocessSourceUriWithoutRequestKeepsSourceRelative(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();
        $viewHelper = $this->buildViewHelperInstance(['relative' => false]);
        self::assertInstanceOf(\FluidTYPO3\Vhs\ViewHelpers\Resource\ImageViewHelper::class, $viewHelper);

        self::assertSame('fileadmin/test.jpg', $viewHelper->preprocessSourceUri('fileadmin/test.jpg'));
    }
}
