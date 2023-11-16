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
 * Renders HTML code to embed a video from Vimeo.
 */
class VimeoViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Base URL for Vimeo video player
     */
    const VIMEO_BASEURL = '//player.vimeo.com/video/';

    /**
     * @var string
     */
    protected $tagName = 'iframe';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('videoId', 'string', 'Vimeo ID of the video to embed.', true);
        $this->registerArgument(
            'width',
            'integer',
            'Width of the video in pixels. Defaults to 640 for 16:9 content.',
            false,
            640
        );
        $this->registerArgument(
            'height',
            'integer',
            'Height of the video in pixels. Defaults to 360 for 16:9 content.',
            false,
            360
        );
        $this->overrideArgument('title', 'boolean', 'Show the title on the video. Defaults to TRUE.', false, true);
        $this->registerArgument(
            'byline',
            'boolean',
            'Show the user’s byline on the video. Defaults to TRUE.',
            false,
            true
        );
        $this->registerArgument(
            'portrait',
            'boolean',
            'Show the user’s portrait on the video. Defaults to TRUE.',
            false,
            true
        );
        $this->registerArgument(
            'color',
            'string',
            'Specify the color of the video controls. Defaults to 00adef. Make sure that you don’t include the #.',
            false,
            '00adef'
        );
        $this->registerArgument(
            'autoplay',
            'boolean',
            'Play the video automatically on load. Defaults to FALSE. Note that this won’t work on some devices.',
            false,
            false
        );
        $this->registerArgument(
            'loop',
            'boolean',
            'Play the video again when it reaches the end. Defaults to FALSE.',
            false,
            false
        );
        $this->registerArgument('api', 'boolean', 'Set to TRUE to enable the Javascript API.', false, false);
        $this->registerArgument(
            'playerId',
            'string',
            'An unique id for the player that will be passed back with all Javascript API responses.'
        );
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        /** @var string $videoId */
        $videoId = $this->arguments['videoId'];
        /** @var int $width */
        $width = $this->arguments['width'];
        /** @var int $height */
        $height = $this->arguments['height'];

        $src = static::VIMEO_BASEURL . $videoId . '?';

        /** @var string $color */
        $color = $this->arguments['color'];

        $queryParams = [
            'title='     . (bool) $this->arguments['title'],
            'byline='    . (bool) $this->arguments['byline'],
            'portrait='  . (bool) $this->arguments['portrait'],
            'color='     . str_replace('#', '', $color),
            'autoplay='  . (bool) $this->arguments['autoplay'],
            'loop='      . (bool) $this->arguments['loop'],
            'api='       . (bool) $this->arguments['api'],
            'player_id=' . $this->arguments['playerId'],
        ];

        $src .= implode('&', $queryParams);

        $this->tag->forceClosingTag(true);
        $this->tag->addAttribute('src', $src);
        $this->tag->addAttribute('width', (string) $width);
        $this->tag->addAttribute('height', (string) $height);
        $this->tag->addAttribute('frameborder', '0');
        $this->tag->addAttribute('webkitAllowFullScreen', 'webkitAllowFullScreen');
        $this->tag->addAttribute('mozAllowFullScreen', 'mozAllowFullScreen');
        $this->tag->addAttribute('allowFullScreen', 'allowFullScreen');

        return $this->tag->render();
    }
}
