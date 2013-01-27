<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Renders HTML code to embed a video from Vimeo
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_VimeoViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * Base URL for Vimeo video player
	 */
	const vimeoBaseUrl = 'http://player.vimeo.com/video/';

	/**
	 * @var string
	 */
	protected $tagName = 'iframe';

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('videoId', 'string', 'YouTube id of the video to embed.', TRUE);
		$this->registerArgument('width', 'int', 'Width of the video in pixels. Defaults to 640', FALSE, 640);
		$this->registerArgument('height', 'int', 'Height of the video in pixels. Defaults to 480', FALSE, 480);
		$this->registerArgument('title', 'boolean', 'Show the title on the video. Defaults to TRUE.', FALSE, TRUE);
		$this->registerArgument('byline', 'boolean', 'Show the user’s byline on the video. Defaults to TRUE.', FALSE, TRUE);
		$this->registerArgument('portrait', 'boolean', 'Show the user’s portrait on the video. Defaults to TRUE.', FALSE, TRUE);
		$this->registerArgument('color', 'string', 'Specify the color of the video controls. Defaults to 00adef. Make sure that you don’t include the #.', FALSE, '00adef');
		$this->registerArgument('autoplay', 'boolean', 'Play the video automatically on load. Defaults to FALSE. Note that this won’t work on some devices.', FALSE, FALSE);
		$this->registerArgument('loop', 'boolean', 'Play the video again when it reaches the end. Defaults to FALSE.', FALSE, FALSE);
		$this->registerArgument('api', 'boolean', 'Set to TRUE to enable the Javascript API.', FALSE, FALSE);
		$this->registerArgument('playerId', 'string', 'An unique id for the player that will be passed back with all Javascript API responses.', FALSE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$videoId = $this->arguments['videoId'];
		$width   = $this->arguments['width'];
		$height  = $this->arguments['height'];

		$src = self::vimeoBaseUrl . $videoId . '?';

		$queryParams = array(
			'title='     . (integer) $this->arguments['title'],
			'byline='    . (integer) $this->arguments['byline'],
			'portrait='  . (integer) $this->arguments['portrait'],
			'color='     . str_replace('#', '', $this->arguments['color']),
			'autoplay='  . (integer) $this->arguments['autoplay'],
			'loop='      . (integer) $this->arguments['loop'],
			'api='       . (integer) $this->arguments['api'],
			'player_id=' . (integer) $this->arguments['playerId'],
		);

		$src .= implode('&', $queryParams);

		$this->tag->forceClosingTag(TRUE);
		$this->tag->addAttribute('src', $src);
		$this->tag->addAttribute('width', $width);
		$this->tag->addAttribute('height', $height);
		$this->tag->addAttribute('frameborder', 0);
		$this->tag->addAttribute('webkitAllowFullScreen', 'webkitAllowFullScreen');
		$this->tag->addAttribute('mozAllowFullScreen', 'mozAllowFullScreen');
		$this->tag->addAttribute('allowFullScreen', 'allowFullScreen');

		return $this->tag->render();
	}
}
