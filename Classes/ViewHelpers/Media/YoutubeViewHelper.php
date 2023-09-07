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
 * Renders HTML code to embed a video from YouTube.
 */
class YoutubeViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Base url
     *
     * @var string
     */
    const YOUTUBE_BASEURL = '//www.youtube.com';

    /**
     * Base url for extended privacy
     *
     * @var string
     */
    const YOUTUBE_PRIVACY_BASEURL = '//www.youtube-nocookie.com';

    /**
     * @var string
     */
    protected $tagName = 'iframe';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('videoId', 'string', 'YouTube id of the video to embed.', true);
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
            'Height of the video in pixels. Defaults to 385 for 16:9 content.',
            false,
            385
        );
        $this->registerArgument(
            'autoplay',
            'boolean',
            'Play the video automatically on load. Defaults to FALSE.',
            false,
            false
        );
        $this->registerArgument('legacyCode', 'boolean', 'Whether to use the legacy flash video code.', false, false);
        $this->registerArgument(
            'showRelated',
            'boolean',
            'Whether to show related videos after playing.',
            false,
            false
        );
        $this->registerArgument('extendedPrivacy', 'boolean', 'Whether to use cookie-less video player.', false, true);
        $this->registerArgument('hideControl', 'boolean', 'Hide video player\'s control bar.', false, false);
        $this->registerArgument('hideInfo', 'boolean', 'Hide video player\'s info bar.', false, false);
        $this->registerArgument('enableJsApi', 'boolean', 'Enable YouTube JavaScript API', false, false);
        $this->registerArgument('playlist', 'string', 'Comma seperated list of video IDs to be played.');
        $this->registerArgument('loop', 'boolean', 'Play the video in a loop.', false, false);
        $this->registerArgument('start', 'integer', 'Start playing after seconds.');
        $this->registerArgument('end', 'integer', 'Stop playing after seconds.');
        $this->registerArgument('lightTheme', 'boolean', 'Use the YouTube player\'s light theme.', false, false);
        $this->registerArgument(
            'videoQuality',
            'string',
            'Set the YouTube player\'s video quality (hd1080,hd720,highres,large,medium,small).'
        );
        $this->registerArgument(
            'windowMode',
            'string',
            'Set the Window-Mode of the YouTube player (transparent,opaque). This is necessary for ' .
            'z-index handling in IE10/11.'
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
        /** @var int height */
        $height = $this->arguments['height'];

        $this->tag->addAttribute('width', (string) $width);
        $this->tag->addAttribute('height', (string) $height);

        $src = $this->getSourceUrl($videoId);

        if (!$this->arguments['legacyCode']) {
            $this->tag->addAttribute('src', $src);
            $this->tag->addAttribute('frameborder', '0');
            $this->tag->addAttribute('allowFullScreen', 'allowFullScreen');
            $this->tag->forceClosingTag(true);
        } else {
            $this->tag->setTagName('object');

            $tagContent = '';

            $paramAttributes = [
                'movie' => $src,
                'allowFullScreen' => 'true',
                'scriptAccess' => 'always',
            ];
            foreach ($paramAttributes as $name => $value) {
                $tagContent .= $this->renderChildTag('param', [$name => $value], true);
            }

            $embedAttributes = [
                'src' => $src,
                'type' => 'application/x-shockwave-flash',
                'width' => $width,
                'height' => $height,
                'allowFullScreen' => 'true',
                'scriptAccess' => 'always',
            ];
            $tagContent .= $this->renderChildTag('embed', $embedAttributes, true);

            $this->tag->setContent($tagContent);
        }

        return $this->tag->render();
    }

    /**
     * Returns video source url according to provided arguments.
     */
    private function getSourceUrl(string $videoId): string
    {
        $src = $this->arguments['extendedPrivacy'] ? static::YOUTUBE_PRIVACY_BASEURL : static::YOUTUBE_BASEURL;

        $params = [];

        if (!$this->arguments['showRelated']) {
            $params[] = 'rel=0';
        }
        if ($this->arguments['autoplay']) {
            $params[] = 'autoplay=1';
        }
        if ($this->arguments['hideControl']) {
            $params[] = 'controls=0';
        }
        if ($this->arguments['hideInfo']) {
            $params[] = 'showinfo=0';
        }
        if ($this->arguments['enableJsApi']) {
            $params[] = 'enablejsapi=1';
        }
        if (!empty($this->arguments['playlist'])) {
            $params[] = 'playlist=' . $this->arguments['playlist'];
        }
        if ($this->arguments['loop']) {
            $params[] = 'loop=1';
        }
        if (!empty($this->arguments['start'])) {
            $params[] = 'start=' . $this->arguments['start'];
        }
        if (!empty($this->arguments['end'])) {
            $params[] = 'end=' . $this->arguments['end'];
        }
        if ($this->arguments['lightTheme']) {
            $params[] = 'theme=light';
        }
        if (!empty($this->arguments['videoQuality'])) {
            $params[] = 'vq=' . $this->arguments['videoQuality'];
        }
        if (!empty($this->arguments['windowMode'])) {
            $params[] = 'wmode=' . $this->arguments['windowMode'];
        }

        if (!$this->arguments['legacyCode']) {
            $src .= '/embed/'. $videoId;
            $seperator = '?';
        } else {
            $src .= '/v/' . $videoId . '?version=3';
            $seperator = '&';
        }

        if (!empty($params)) {
            $src .= $seperator . implode('&', $params);
        }

        return $src;
    }

    private function renderChildTag(string $tagName, array $attributes = [], bool $forceClosingTag = false): string
    {
        $tagBuilder = clone $this->tag;
        $tagBuilder->reset();
        $tagBuilder->setTagName($tagName);
        $tagBuilder->addAttributes($attributes);
        $tagBuilder->forceClosingTag($forceClosingTag);
        $childTag = $tagBuilder->render();
        unset($tagBuilder);

        return $childTag;
    }
}
