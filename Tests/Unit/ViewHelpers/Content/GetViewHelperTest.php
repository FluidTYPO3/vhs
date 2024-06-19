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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class GetViewHelperTest
 */
class GetViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $GLOBALS['TSFE']->cObj = $this->getMockBuilder(ContentObjectRenderer::class)->setMethods(['getRecords'])->disableOriginalConstructor()->getMock();
        $GLOBALS['TSFE']->cObj->method('getRecords')->willReturn([]);
    }

    public function testRender()
    {
        $this->assertEmpty($this->executeViewHelper());
    }
}
