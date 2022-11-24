<?php
namespace FluidTYPO3\Vhs\Tests\Unit\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * Class UncacheTemplateViewTest
 */
class UncacheTemplateViewTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function callUserFunctionReturnsEarlyIfPartialEmpty()
    {
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['prepareContextsForUncachedRendering'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->injectObjectManager($this->objectManager);

        $configuration = ['partial' => ''];
        $mock->expects($this->never())->method('prepareContextsForUncachedRendering');
        $mock->callUserFunction('', $configuration, '');
    }

    /**
     * @test
     */
    public function callUserFunctionReturnsCallsExpectedMethodSequence()
    {
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['prepareContextsForUncachedRendering', 'setControllerContext', 'renderPartialUncached'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->injectObjectManager($this->objectManager);

        $configuration = ['partial' => 'dummy', 'section' => 'dummy', 'controllerContext' => []];
        $mock->expects($this->once())->method('prepareContextsForUncachedRendering');
        $mock->expects($this->once())->method('setControllerContext');
        $mock->expects($this->once())->method('renderPartialUncached');
        $mock->callUserFunction('', $configuration, '');
    }

    /**
     * @test
     */
    public function prepareContextsForUncachedRenderingCallsExpectedMethodSequence()
    {
        $controllerContext = new ControllerContext();
        $renderingContext = $this->getMockBuilder(RenderingContext::class)
            ->setMethods(['setControllerContext'])
            ->disableOriginalConstructor()
            ->getMock();
        $renderingContext->expects($this->once())->method('setControllerContext')->with($controllerContext);
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['setRenderingContext'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('setRenderingContext')->with($renderingContext);
        $this->callInaccessibleMethod($mock, 'prepareContextsForUncachedRendering', $renderingContext, $controllerContext);
    }

    /**
     * @test
     */
    public function renderPartialUncachedDelegatesToRenderPartial()
    {
        $renderingContext = $this->getMockBuilder(RenderingContext::class)->disableOriginalConstructor()->getMock();
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['renderPartial'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('renderPartial')->will($this->returnValue('test'));
        $result = $this->callInaccessibleMethod($mock, 'renderPartialUncached', $renderingContext, 'dummy');
        $this->assertEquals('test', $result);
    }

    /**
     * @return mixed|string
     */
    protected function getClassName()
    {
        $class = substr(get_class($this), 0, -4);
        $class = str_replace('Tests\\Unit\\', '', $class);
        return $class;
    }

    protected function createObjectManagerInstance(): ObjectManagerInterface
    {
        $instance = parent::createObjectManagerInstance();
        $instance->method('get')->willReturnMap(
            [
                [ControllerContext::class, new ControllerContext()],
                [Request::class, new Request()],
                [UriBuilder::class, new UriBuilder()],
                [RenderingContext::class, $this->getMockBuilder(RenderingContext::class)->disableOriginalConstructor()->getMock()],
            ]
        );
        return $instance;
    }
}
