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
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\ExtensionViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $this->assertEquals('', $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsExpectedExtensionForProvidedPath()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\ExtensionViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/foo.txt'));
        $this->assertEquals('txt', $viewHelper->render());
    }

    /**
     * @test
     */
    public function returnsEmptyStringForFileWithoutExtension()
    {
        $viewHelper = $this->getMock('FluidTYPO3\Vhs\ViewHelpers\Media\ExtensionViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($this->fixturesPath . '/noext'));
        $this->assertEquals('', $viewHelper->render());
    }
}
