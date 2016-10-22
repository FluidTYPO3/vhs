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
 * Class YoutubeViewHelperTest
 */
class YoutubeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @var array
     */
    protected $arguments = [
        'videoId' => '',
        'width' => 640,
        'height' => 385,
        'autoplay' => false,
        'legacyCode' => false,
        'showRelated' => false,
        'extendedPrivacy' => true,
        'hideControl' => false,
        'hideInfo' => false,
        'playlist' => '',
        'loop' => false,
        'start' => 30,
        'end' => '',
        'lightTheme' => false,
        'videoQuality' => ''
    ];

    /**
     * @test
     */
    public function compareResult()
    {
        $this->arguments['videoId']  = 'M7lc1UVf-VE';
        $this->arguments['hideInfo'] = true;
        $this->arguments['start']    = 30;

        preg_match('#src="([^"]*)"#', $this->executeViewHelper($this->arguments), $actualSource);
        $expectedSource = '//www.youtube-nocookie.com/embed/M7lc1UVf-VE?rel=0&amp;showinfo=0&amp;start=30';

        $this->assertSame($expectedSource, $actualSource[1]);
    }
}
