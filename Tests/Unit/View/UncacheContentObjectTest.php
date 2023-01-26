<?php
namespace FluidTYPO3\Vhs\Tests\Unit\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\View\UncacheContentObject;
use FluidTYPO3\Vhs\View\UncacheTemplateView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class UncacheContentObjectTest extends AbstractTestCase
{
    public function testDelegatesToUncacheTemplateView(): void
    {
        $uncacheTemplateView = $this->getMockBuilder(UncacheTemplateView::class)
            ->setMethods(['callUserFunction'])
            ->disableOriginalConstructor()
            ->getMock();
        $uncacheTemplateView->method('callUserFunction')->willReturn('rendered');
        GeneralUtility::addInstance(UncacheTemplateView::class, $uncacheTemplateView);

        $subject = new UncacheContentObject(
            $this->getMockBuilder(ContentObjectRenderer::class)->disableOriginalConstructor()->getMock()
        );
        self::assertSame('rendered', $subject->render([]));
    }
}
