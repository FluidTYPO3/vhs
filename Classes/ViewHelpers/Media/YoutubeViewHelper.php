<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Renders HTML code to embed a video from YouTube
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_YoutubeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

	/**
	 * Base url
	 *
	 * @var string
	 */
	const YOUTUBE_BASEURL = 'http://www.youtube.com';

	/**
	 * Base url for extended privacy
	 *
	 * @var string
	 */
	const YOUTUBE_PRIVACY_BASEURL = 'http://www.youtube-nocookie.com';

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
		$this->registerArgument('width', 'integer', 'Width of the video in pixels. Defaults to 640 for 16:9 content.', FALSE, 640);
		$this->registerArgument('height', 'integer', 'Height of the video in pixels. Defaults to 385 for 16:9 content.', FALSE, 385);
		$this->registerArgument('autoplay', 'boolean', 'Play the video automatically on load. Defaults to FALSE.', FALSE, FALSE);
		$this->registerArgument('legacyCode', 'boolean', 'Whether to use the legacy flash video code.', FALSE, FALSE);
		$this->registerArgument('showRelated', 'boolean', 'Whether to show related videos after playing.', FALSE, FALSE);
		$this->registerArgument('extendedPrivacy', 'boolean', 'Whether to use cookie-less video player.', FALSE, TRUE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$videoId = $this->arguments['videoId'];
		$width = $this->arguments['width'];
		$height = $this->arguments['height'];

		$this->tag->addAttribute('width', $width);
		$this->tag->addAttribute('height', $height);

		$src = $this->getSourceUrl($videoId);

		if (FALSE === (boolean) $this->arguments['legacyCode']) {
			$this->tag->addAttribute('src', $src);
			$this->tag->addAttribute('frameborder', 0);
			$this->tag->addAttribute('allowFullScreen', 'allowFullScreen');
			$this->tag->forceClosingTag(TRUE);
		} else {
			$this->tag->setTagName('object');

			$tagContent = '';

			$paramAttributes = array(
				'movie' => $src,
				'allowFullScreen' => 'true',
				'scriptAccess' => 'always',
			);
			foreach ($paramAttributes as $name => $value) {
				$tagContent .= $this->renderChildTag('param', array($name => $value), TRUE);
			}

			$embedAttributes = array(
				'src' => $src,
				'type' => 'application/x-shockwave-flash',
				'width' => $width,
				'height' => $height,
				'allowFullScreen' => 'true',
				'scriptAccess' => 'always',
			);
			$tagContent .= $this->renderChildTag('embed', $embedAttributes, TRUE);

			$this->tag->setContent($tagContent);
		}

		return $this->tag->render();
	}

	/**
	 * Returns video source url according to provided arguments
	 *
	 * @param string $videoId
	 * @return string
	 */
	private function getSourceUrl($videoId) {
		$src = (boolean) TRUE === $this->arguments['extendedPrivacy'] ? self::YOUTUBE_PRIVACY_BASEURL : self::YOUTUBE_BASEURL;
		if (FALSE === $this->arguments['legacyCode']) {
			$src .= '/embed/'. $videoId;
			if (FALSE === (boolean) $this->arguments['showRelated'] || TRUE === (boolean) $this->arguments['autoplay']) {
				$src .= '?';
			}
			if (FALSE === (boolean) $this->arguments['showRelated']) {
				$src .= 'rel=0&';
			}
			if (TRUE === (boolean) $this->arguments['autoplay']) {
				$src .= 'autoplay=1';
			}
		} else {
			$src .= '/v/' . $this->arguments['videoId'] . '?version=3';
			if (FALSE === (boolean) $this->arguments['showRelated']) {
				$src .= '&rel=0';
			}
			if (TRUE === (boolean) $this->arguments['autoplay']) {
				$src .= '&autoplay=1';
			}
		}

		return $src;
	}

	/**
	 * Renders the provided tag and its attributes
	 *
	 * @param string $tagName
	 * @param array $attributes
	 * @param boolean $forceClosingTag
	 * @return string
	 */
	private function renderChildTag($tagName, $attributes = array(), $forceClosingTag = FALSE) {
		$tagBuilder = clone($this->tag);
		$tagBuilder->reset();
		$tagBuilder->setTagName($tagName);
		$tagBuilder->addAttributes($attributes);
		$tagBuilder->forceClosingTag($forceClosingTag);
		$childTag = $tagBuilder->render();
		unset($tagBuilder);

		return $childTag;
	}
}
