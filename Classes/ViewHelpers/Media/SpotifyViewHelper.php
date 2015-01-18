<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Renders HTML code to embed a Spotify play button
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class SpotifyViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * Play button base url
	 */
	const SPOTIFY_BASEURL = 'https://embed.spotify.com/';

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
		$this->registerArgument('spotifyUri', 'string', 'Spotify URI to create the play button for. Right click any song, album or playlist in Spotify and select Copy Spotify URI.', TRUE);
		$this->registerArgument('width', 'int', 'Width of the play button in pixels. Defaults to 300', FALSE, 300);
		$this->registerArgument('height', 'int', 'Height of the play button in pixels. Defaults to 380', FALSE, 380);
		$this->registerArgument('compact', 'boolean', 'Whether to render the compact button with a fixed height of 80px.', FALSE, FALSE);
		$this->registerArgument('theme', 'string', 'Theme to use. Can be "black" or "white" and is not available in compact mode. Defaults to "black".', FALSE, 'black');
		$this->registerArgument('view', 'string', 'View to use. Can be "list" or "coverart" and is not available in compact mode. Defaults to "list".', FALSE, 'list');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$spotifyUri = $this->arguments['spotifyUri'];
		$width      = (integer) $this->arguments['width'];
		$height     = (integer) $this->arguments['height'];

		if (TRUE === in_array($this->arguments['theme'], array('black', 'white'))) {
			$theme = $this->arguments['theme'];
		} else {
			$theme = 'black';
		}

		if (TRUE === in_array($this->arguments['view'], array('coverart', 'list'))) {
			$view = $this->arguments['view'];
		} else {
			$view = 'list';
		}

		if (TRUE === (boolean) $this->arguments['compact']) {
			$height = 80;
		}

		$src = self::SPOTIFY_BASEURL . '?uri=' . $spotifyUri . '&theme=' . $theme . '&view=' . $view;

		$this->tag->forceClosingTag(TRUE);
		$this->tag->addAttribute('src', $src);
		$this->tag->addAttribute('width', $width);
		$this->tag->addAttribute('height', $height);
		$this->tag->addAttribute('allowtransparancy', 'true');
		$this->tag->addAttribute('frameborder', 0);

		return $this->tag->render();
	}

}
