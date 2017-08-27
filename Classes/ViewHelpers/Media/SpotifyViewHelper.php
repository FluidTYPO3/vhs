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
 * Renders HTML code to embed a Spotify play button.
 */
class SpotifyViewHelper extends AbstractTagBasedViewHelper
{

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
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument(
            'spotifyUri',
            'string',
            'Spotify URI to create the play button for. Right click any song, album or playlist in Spotify and ' .
            'select Copy Spotify URI.',
            true
        );
        $this->registerArgument('width', 'int', 'Width of the play button in pixels. Defaults to 300', false, 300);
        $this->registerArgument('height', 'int', 'Height of the play button in pixels. Defaults to 380', false, 380);
        $this->registerArgument(
            'compact',
            'boolean',
            'Whether to render the compact button with a fixed height of 80px.',
            false,
            false
        );
        $this->registerArgument(
            'theme',
            'string',
            'Theme to use. Can be "black" or "white" and is not available in compact mode. Defaults to "black".',
            false,
            'black'
        );
        $this->registerArgument(
            'view',
            'string',
            'View to use. Can be "list" or "coverart" and is not available in compact mode. Defaults to "list".',
            false,
            'list'
        );
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $spotifyUri = $this->arguments['spotifyUri'];
        $width      = (integer) $this->arguments['width'];
        $height     = (integer) $this->arguments['height'];

        if (true === in_array($this->arguments['theme'], ['black', 'white'])) {
            $theme = $this->arguments['theme'];
        } else {
            $theme = 'black';
        }

        if (true === in_array($this->arguments['view'], ['coverart', 'list'])) {
            $view = $this->arguments['view'];
        } else {
            $view = 'list';
        }

        if (true === (boolean) $this->arguments['compact']) {
            $height = 80;
        }

        $src = self::SPOTIFY_BASEURL . '?uri=' . $spotifyUri . '&theme=' . $theme . '&view=' . $view;

        $this->tag->forceClosingTag(true);
        $this->tag->addAttribute('src', $src);
        $this->tag->addAttribute('width', $width);
        $this->tag->addAttribute('height', $height);
        $this->tag->addAttribute('allowtransparancy', 'true');
        $this->tag->addAttribute('frameborder', 0);

        return $this->tag->render();
    }
}
