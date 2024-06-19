<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class TryViewHelperTest
 */
class TryViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender()
    {
        $this->assertEmpty($this->executeViewHelper());
    }

    public function testRenderWithException()
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderElseChild', 'renderChildren'])->getMock();
        $instance->setRenderingContext($this->renderingContext);
        $instance->setArguments([]);
        $instance->expects($this->once())->method('renderChildren')->willThrowException(new \RuntimeException('testerror'));
        $instance->expects($this->once())->method('renderElseChild')->willReturn('testerror');
        $result = $instance->render();
        $this->assertEquals('testerror', $result);
    }
}
