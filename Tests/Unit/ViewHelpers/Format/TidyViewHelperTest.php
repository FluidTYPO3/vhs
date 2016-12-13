<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\ViewHelpers\Format\TidyViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * Class TidyViewHelperTest
 */
class TidyViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function throwsErrorWhenNoTidyIsInstalled()
    {
        if (!class_exists('tidy')) {
            // Note: CI setup has tidy on some but not all variants. We can only test for exceptions on those that don't.
            $this->setExpectedException('RuntimeException', null, 1352059753);
        }
        TidyViewHelper::renderStatic(['content' => 'test', 'encoding' => 'utf8'], function () {}, $this->objectManager->get(RenderingContext::class));
    }

    /**
     * @test
     */
    public function canTidySource()
    {
        $instance = $this->createInstance();
        if (false === class_exists('tidy')) {
            $this->markTestSkipped('No tidy support');
            return;
        }
        $source = '<foo> <bar>
			</bar>			</foo>';
        $test = $this->executeViewHelper(['content' => $source, 'encoding' => 'utf8']);
        $this->assertNotSame($source, $test);
    }
}
