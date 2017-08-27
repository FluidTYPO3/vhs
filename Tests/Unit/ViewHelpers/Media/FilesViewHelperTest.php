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
 * Class FilesViewHelperTest
 */
class FilesViewHelperTest extends AbstractViewHelperTest
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
        $this->fixturesPath = dirname(__FILE__) . '/../../../Fixtures/Files';
    }

    /**
     * @test
     */
    public function returnsEmtpyArrayWhenArgumentsAreNotSet()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $viewHelper->setArguments([]);
        $this->assertEquals([], $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsEmptyArrayWhenPathIsInaccessible()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('/this/path/hopefully/does/not/exist'));
        $viewHelper->setArguments([]);
        $this->assertEquals([], $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfAllFoundFiles()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $actualFiles = glob($this->fixturesPath . '/*');
        $actualFilesCount = count($actualFiles);
        $viewHelper->setArguments([]);
        $this->assertCount($actualFilesCount, $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfFilteredFiles()
    {
        $viewHelper = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));
        $viewHelper->setArguments(['extensionList' => 'txt']);
        if (method_exists($viewHelper, 'injectReflectionService')) {
            $viewHelper->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        $viewHelper->setRenderingContext($this->objectManager->get(RenderingContext::class));
        $actualFiles = glob($this->fixturesPath . '/*.txt');
        $actualFilesCount = count($actualFiles);

        $this->assertCount($actualFilesCount, $viewHelper->render());
    }
}
