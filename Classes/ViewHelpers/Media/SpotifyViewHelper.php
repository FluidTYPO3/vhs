<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

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

    public function initializeArguments(): void
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
     * @return string
     */
    public function render()
    {
        $spotifyUri = $this->arguments['spotifyUri'];
        /** @var int $width */
        $width = $this->arguments['width'];
        /** @var int $height */
        $height = $this->arguments['height'];
        /** @var string $theme */
        $theme = $this->arguments['theme'];
        /** @var string $view */
        $view = $this->arguments['view'];

        if (!in_array($theme, ['black', 'white'])) {
            $theme = 'black';
        }

        if (!in_array($view, ['coverart', 'list'])) {
            $view = 'list';
        }

        if ($this->arguments['compact']) {
            $height = 80;
        }

        $src = static::SPOTIFY_BASEURL . '?uri=' . $spotifyUri . '&theme=' . $theme . '&view=' . $view;

        $this->tag->forceClosingTag(true);
        $this->tag->addAttribute('src', $src);
        $this->tag->addAttribute('width', (string) $width);
        $this->tag->addAttribute('height', (string) $height);
        $this->tag->addAttribute('allowtransparancy', 'true');
        $this->tag->addAttribute('frameborder', '0');

        return $this->tag->render();
    }
}
