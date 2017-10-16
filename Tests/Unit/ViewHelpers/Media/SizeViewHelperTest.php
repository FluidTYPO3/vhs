<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * Class SizeViewHelperTest
 */
class SizeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @var string
     */
    protected $fixturesPath;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->fixturesPath = 'EXT:vhs/Tests/Fixtures/Files';
    }

    /**
     * @test
     */
    public function returnsZeroForEmptyArguments()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments([]);
        $this->assertEquals(0, $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsFileSizeAsInteger()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/typo3_logo.jpg'));
        $viewHelper->setArguments([]);
        $this->assertEquals(7094, $viewHelper->render());
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileNotFound()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('/this/path/hopefully/does/not/exist.txt'));
        $viewHelper->setArguments([]);
        $this->expectViewHelperException();
        $viewHelper->render();
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileIsNotAccessibleOrIsADirectory()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));
        $viewHelper->setArguments([]);
        $this->expectViewHelperException();
        $viewHelper->render();
    }
}
