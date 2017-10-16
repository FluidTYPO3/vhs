<?php
namespace FluidTYPO3\Vhs\Tests\Unit\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Development\AbstractTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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
        $mock = $this->getMockBuilder($this->getClassName())->setMethods(['prepareContextsForUncachedRendering'])->getMock();
        $configuration = ['partial' => ''];
        $mock->expects($this->never())->method('prepareContextsForUncachedRendering');
        $mock->callUserFunction('', $configuration, '');
    }

    /**
     * @test
     */
    public function callUserFunctionReturnsCallsExpectedMethodSequence()
    {
        $mock = $this->getMockBuilder($this->getClassName())->setMethods(['prepareContextsForUncachedRendering', 'setControllerContext', 'renderPartialUncached'])->getMock();
        $mock->injectObjectManager(GeneralUtility::makeInstance(ObjectManager::class));
        $context = new ControllerContext();
        $configuration = ['partial' => 'dummy', 'section' => 'dummy', 'controllerContext' => $context];
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
        $renderingContext = $this->getMockBuilder(RenderingContext::class)->setMethods(['setControllerContext'])->getMock();
        $renderingContext->expects($this->once())->method('setControllerContext')->with($controllerContext);
        $mock = $this->getMockBuilder($this->getClassName())->setMethods(['setRenderingContext'])->getMock();
        $mock->expects($this->once())->method('setRenderingContext')->with($renderingContext);
        $this->callInaccessibleMethod($mock, 'prepareContextsForUncachedRendering', $renderingContext, $controllerContext);
    }

    /**
     * @test
     */
    public function renderPartialUncachedDelegatesToRenderPartial()
    {
        $renderingContext = new RenderingContext();
        $mock = $this->getMockBuilder($this->getClassName())->setMethods(['renderPartial'])->getMock();
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
}
