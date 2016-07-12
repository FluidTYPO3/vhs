<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

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
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\FilesViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));

        $this->assertEquals(array(), $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsEmptyArrayWhenPathIsInaccessible()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\FilesViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('/this/path/hopefully/does/not/exist'));

        $this->assertEquals(array(), $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfAllFoundFiles()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\FilesViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));
        $actualFiles = glob($this->fixturesPath . '/*');
        $actualFilesCount = count($actualFiles);

        $this->assertCount($actualFilesCount, $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsPopulatedArrayOfFilteredFiles()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\FilesViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));
        $viewHelper->setArguments(array('extensionList' => 'txt'));
        $actualFiles = glob($this->fixturesPath . '/*.txt');
        $actualFilesCount = count($actualFiles);

        $this->assertCount($actualFilesCount, $viewHelper->render());
    }
}
