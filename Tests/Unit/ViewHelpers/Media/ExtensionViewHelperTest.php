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
 * Class ExtensionViewHelperTest
 */
class ExtensionViewHelperTest extends AbstractViewHelperTest
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
    public function returnsEmptyStringForEmptyArguments()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments([]);
        $this->assertEquals('', $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsExpectedExtensionForProvidedPath()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/foo.txt'));
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->setArguments([]);
        $this->assertEquals('txt', $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsEmptyStringForFileWithoutExtension()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/noext'));
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->setArguments([]);
        $this->assertEquals('', $viewHelper->render());
    }
}
