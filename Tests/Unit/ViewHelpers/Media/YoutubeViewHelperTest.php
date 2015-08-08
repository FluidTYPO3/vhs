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
 * @protection off
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class YoutubeViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @var array
	 */
	protected $arguments = array(
		'videoId' => '',
		'width' => 640,
		'height' => 385,
		'autoplay' => FALSE,
		'legacyCode' => FALSE,
		'showRelated' => FALSE,
		'extendedPrivacy' => TRUE,
		'hideControl' => FALSE,
		'hideInfo' => FALSE,
		'playlist' => '',
		'loop' => FALSE,
		'start' => 30,
		'end' => '',
		'lightTheme' => FALSE,
		'videoQuality' => ''
	);

	/**
	 * @test
	 */
	public function compareResult() {
		$this->arguments['videoId']  = 'M7lc1UVf-VE';
		$this->arguments['hideInfo'] = TRUE;
		$this->arguments['start']    = 30;

		preg_match('#src="([^"]*)"#', $this->executeViewHelper($this->arguments), $actualSource);
		$expectedSource = '//www.youtube-nocookie.com/embed/M7lc1UVf-VE?rel=0&amp;showinfo=0&amp;start=30';

		$this->assertSame($expectedSource, $actualSource[1]);
	}
}
