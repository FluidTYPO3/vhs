<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\Utility\VersionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\View\FluidViewAdapter;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;
use TYPO3Fluid\Fluid\View\TemplateView;

/**
 * Class TemplateViewHelperTest
 */
class TemplateViewHelperTest extends AbstractViewHelperTestCase
{
    private TemplateView $view;

    protected function setUp(): void
    {
        parent::setUp();

        $this->view = $this->getMockBuilder(TemplateView::class)
            ->onlyMethods(['render', 'getRenderingContext'])
            ->setConstructorArgs([$this->renderingContext])
            ->getMock();
        $this->view->method('render')->willThrowException(new InvalidTemplateResourceException('test', 0));
        $this->view->method('getRenderingContext')->willReturn($this->renderingContext);

        if (VersionUtility::isCoreAtLeast13()) {
            GeneralUtility::addInstance(FluidViewAdapter::class, new FluidViewAdapter($this->view));
        } else {
            GeneralUtility::addInstance(TemplateView::class, $this->view);
        }
    }

    public function testRenderThrowsExceptionWithoutTemplatePath()
    {
        $this->expectException(InvalidTemplateResourceException::class);
        $this->executeViewHelper(['variables' => []]);
    }
}
