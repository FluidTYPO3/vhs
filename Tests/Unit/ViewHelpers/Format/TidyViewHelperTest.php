<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Format\TidyViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class TidyViewHelperTest
 */
class TidyViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function throwsErrorWhenNoTidyIsInstalled(): void
    {
        if (!class_exists('tidy')) {
            // CI setup has tidy on some but not all variants.
            // We can only test for exceptions on those that don't.
            $this->expectExceptionCode(1352059753);
        }
        self::assertInstanceOf(RenderingContextInterface::class, $this->renderingContext);
        $output = (string) TidyViewHelper::renderStatic(
            ['content' => 'test', 'encoding' => 'utf8'],
            function () {
            },
            $this->renderingContext
        );
        self::assertNotSame('test', $output);
    }

    /**
     * @test
     */
    public function canTidySource(): void
    {
        if (!class_exists('tidy')) {
            $this->markTestSkipped('No tidy support');
        }
        $source = '<foo> <bar>
			</bar>			</foo>';
        $test = $this->executeViewHelper(['content' => $source, 'encoding' => 'utf8']);
        $this->assertNotSame($source, $test);
    }
}
