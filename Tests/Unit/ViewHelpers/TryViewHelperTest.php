<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;

/**
 * Class TryViewHelperTest
 */
class TryViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        $this->assertEmpty($this->executeViewHelper());
    }

    public function testRenderWithException()
    {
        $renderingContext = new RenderingContext();
        if (method_exists($renderingContext, 'injectTemplateVariableContainer')) {
            $renderingContext->injectTemplateVariableContainer(new TemplateVariableContainer());
        }
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderThenChild', 'renderChildren'])->getMock();
        $instance->setRenderingContext($renderingContext);
        $instance->setArguments([]);
        $instance->expects($this->once())->method('renderThenChild')->willThrowException(new \RuntimeException('testerror'));
        $instance->expects($this->once())->method('renderChildren')->willReturn('testerror');
        $result = $instance->render();
        $this->assertEquals('testerror', $result);
    }
}
