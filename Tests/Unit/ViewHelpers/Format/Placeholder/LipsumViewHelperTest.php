<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Placeholder;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class LipsumViewHelperTest
 */
class LipsumViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @var array
     */
    protected $arguments = array(
        'paragraphs' => 5,
        'skew' => 0,
        'html' => false,
        'parseFuncTSPath' => ''
    );

    /**
     * @test
     */
    public function supportsParagraphCount()
    {
        $arguments = $this->arguments;
        $firstRender = $this->executeViewHelper($arguments);
        $arguments['paragraphs'] = 6;
        $secondRender = $this->executeViewHelper($arguments);
        $this->assertLessThan(strlen($secondRender), strlen($firstRender));
    }

    /**
     * @test
     */
    public function supportsHtmlArgument()
    {
        $arguments = $this->arguments;
        $arguments['html'] = true;
        $test = $this->executeViewHelper($arguments);
        $this->assertNotEmpty($test);
    }

    /**
     * @test
     */
    public function detectsFileByShortPath()
    {
        $arguments = $this->arguments;
        $arguments['lipsum'] = 'EXT:vhs/Tests/Fixtures/Files/foo.txt';
        $test = $this->executeViewHelper($arguments);
        $this->assertNotEmpty($test);
    }

    /**
     * @test
     */
    public function canFallBackWhenUsingFileAndFileDoesNotExist()
    {
        $arguments = $this->arguments;
        $arguments['lipsum'] = 'EXT:vhs/None.txt';
        $test = $this->executeViewHelper($arguments);
        $this->assertNotEmpty($test);
    }
}
