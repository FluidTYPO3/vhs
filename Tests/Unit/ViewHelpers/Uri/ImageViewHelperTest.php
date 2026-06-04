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
use FluidTYPO3\Vhs\ViewHelpers\Uri\ImageViewHelper;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Class ImageViewHelperTest
 */
class ImageViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function callsExpectedMethodSequence(): void
    {
        /** @var class-string<ImageViewHelper> $viewHelperClassName */
        $viewHelperClassName = $this->getViewHelperClassName();
        /** @var ImageViewHelper&MockObject $mock */
        $mock = $this->getMockBuilder($viewHelperClassName)
            ->onlyMethods(['preprocessImage'])
            ->getMock();
        $arguments = $this->buildViewHelperArguments($mock, ['src' => 'foobar']);
        $mock->setArguments($arguments);
        $mock->setRenderingContext($this->createRenderingContextWithRequest(new ServerRequest()));
        $output = $mock->render();
        $this->assertSame('', $output);
    }
}
