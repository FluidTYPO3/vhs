<?php
namespace FluidTYPO3\Vhs\Tests\Unit\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\View\TemplatePaths;

class UncacheTemplateViewTest extends AbstractTestCase
{
    private ?RenderingContext $renderingContext = null;

    protected function setUp(): void
    {
        $this->renderingContext = $this->getMockBuilder(RenderingContext::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->renderingContext->setTemplatePaths(
            $this->getMockBuilder(TemplatePaths::class)->disableOriginalConstructor()->getMock()
        );
        GeneralUtility::addInstance(RenderingContext::class, $this->renderingContext);
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['getAttribute', 'withAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('withAttribute')->willReturnSelf();
        if (class_exists(ExtbaseRequestParameters::class)) {
            $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturn(new ExtbaseRequestParameters());
        }
        parent::setUp();
    }

    /**
     * @test
     */
    public function callUserFunctionReturnsEarlyIfPartialEmpty()
    {
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['prepareContextsForUncachedRendering', 'createRenderingContextWithRenderingContextFactory'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->method('createRenderingContextWithRenderingContextFactory')->willReturn($this->renderingContext);

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
            ->setMethods(
                [
                    'setRenderingContext',
                    'renderPartialUncached',
                    'createRenderingContextWithRenderingContextFactory'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $mock->method('createRenderingContextWithRenderingContextFactory')->willReturn($this->renderingContext);

        $configuration = [
            'partial' => 'dummy',
            'section' => 'dummy',
            'controllerContext' => [],
            'partialRootPaths' => ['foo']
        ];
        $mock->expects($this->once())->method('setRenderingContext');
        $mock->expects($this->once())->method('renderPartialUncached');
        $mock->callUserFunction('', $configuration, '');
    }

    /**
     * @test
     */
    public function prepareContextsForUncachedRenderingCallsExpectedMethodSequence()
    {
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['setRenderingContext'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('setRenderingContext')->with($this->renderingContext);
        $this->callInaccessibleMethod($mock, 'prepareContextsForUncachedRendering', $this->renderingContext);
    }

    /**
     * @test
     */
    public function renderPartialUncachedDelegatesToRenderPartial()
    {
        $mock = $this->getMockBuilder($this->getClassName())
            ->setMethods(['renderPartial'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('renderPartial')->will($this->returnValue('test'));
        $result = $this->callInaccessibleMethod($mock, 'renderPartialUncached', $this->renderingContext, 'dummy');
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
