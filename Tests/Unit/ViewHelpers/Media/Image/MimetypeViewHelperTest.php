<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Media\Image;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class MimetypeViewHelperTest
 */
class MimetypeViewHelperTest extends AbstractViewHelperTest
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
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\MimetypeViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));

        $this->assertEquals('', $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsFileMimetypeAsString()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\MimetypeViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/typo3_logo.jpg'));

        $this->assertEquals('image/jpeg', $viewHelper->render());
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileNotFound()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\MimetypeViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue('/this/path/hopefully/does/not/exist.txt'));

        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception');
        $viewHelper->render();
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileIsNotAccessibleOrIsADirectory()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\Image\MimetypeViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath));

        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception');
        $viewHelper->render();
    }
}
