<?php
namespace FluidTYPO3\Vhs\Tests\Unit\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;

/**
 * Class UncacheTemplateViewTest
 */
class UncacheTemplateViewTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        if (class_exists(RenderingContextFactory::class)) {
            self::markTestSkipped('Skipping test with RenderingContextFactory dependency');
        }
        /*
         * [ControllerContext::class, new ControllerContext()],
            [Request::class, new Request()],
            [UriBuilder::class, new UriBuilder()],
         */
        GeneralUtility::addInstance(
            RenderingContext::class,
            $this->getMockBuilder(RenderingContext::class)->disableOriginalConstructor()->getMock()
        );
        parent::setUp();
    }

    /**
     * @test
     */
    public function callUserFunctionReturnsEarlyIfPartialEmpty()
    {
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['prepareContextsForUncachedRendering'])
            ->disableOriginalConstructor()
            ->getMock();

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
            ->setMethods(['prepareContextsForUncachedRendering', 'renderPartialUncached'])
            ->disableOriginalConstructor()
            ->getMock();

        $configuration = ['partial' => 'dummy', 'section' => 'dummy', 'controllerContext' => []];
        $mock->expects($this->once())->method('prepareContextsForUncachedRendering');
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
            ->disableOriginalConstructor()
            ->getMock();
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
}
