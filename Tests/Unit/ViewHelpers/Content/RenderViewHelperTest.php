<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Content\RenderViewHelper;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class RenderViewHelperTest
 */
class RenderViewHelperTest extends AbstractViewHelperTestCase
{
    protected function createInstance(): ViewHelperInterface&MockObject
    {
        return $this->getMockBuilder(RenderViewHelper::class)
            ->onlyMethods(['getContentRecords'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCallsGetcontentRecords(): void
    {
        $instance = $this->createInstance();
        $instance->expects(self::once())->method('getContentRecords')->willReturn([]);
        $instance->render();
    }
}
