<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyViewHelperNode;
use FluidTYPO3\Vhs\ViewHelpers\TryViewHelper;

/**
 * Class TryViewHelperTest
 */
class TryViewHelperTest extends AbstractViewHelperTestCase
{
    public function testRender()
    {
        $this->executeViewHelper();
        $this->assertNull(null);
    }

    public function testRenderStaticWithException(): void
    {
        $arguments['__then'] = function() { throw new \Exception('test'); };
        $arguments['__else'] = function() { return 'else case'; };
        $output = TryViewHelper::renderStatic($arguments, function() { return ''; }, $this->renderingContext);
        self::assertSame('else case', $output);
    }

    public function testRenderStaticWithExceptionAndElseArgument(): void
    {
        $arguments['__then'] = function() { throw new \Exception('test'); };
        $arguments['else'] = 'else case';
        $output = TryViewHelper::renderStatic($arguments, function() { return ''; }, $this->renderingContext);
        self::assertSame('else case', $output);
    }

    public function testRenderWithException(): void
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderElseChild', 'renderChildren'])->getMock();
        $instance->setRenderingContext($this->renderingContext);
        $instance->setArguments([]);
        $instance->expects($this->once())->method('renderChildren')->willThrowException(new \RuntimeException('testerror'));
        $instance->expects($this->once())->method('renderElseChild')->willReturn('testerror');
        $result = $instance->render();
        $this->assertEquals('testerror', $result);
    }

    public function testRenderWithExceptionAndElseArgument(): void
    {
        $instance = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $instance->setRenderingContext($this->renderingContext);
        $instance->setArguments(['else' => 'else']);

        $node = new DummyViewHelperNode($instance);
        $node->setArguments(['else' => 'else']);
        $instance->setViewHelperNode($node);

        $instance->method('renderChildren')->willThrowException(new \RuntimeException('testerror'));
        $result = $instance->render();
        $this->assertEquals('else', $result);
    }
}
