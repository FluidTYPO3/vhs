<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Placeholder;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Placeholder Image ViewHelper
 *
 * Inserts a placeholder image from http://placehold.it/
 */
class ImageViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'img';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('text', 'string', 'Text to render as image');
        $this->registerArgument('width', 'integer', 'Width of rendered placeholder image', false, 640);
        $this->registerArgument('height', 'integer', 'Height of rendered placeholder image', false, false);
        $this->registerArgument('backgroundColor', 'string', 'Background color', false, '333333');
        $this->registerArgument('textColor', 'string', 'Text color', false, 'FFFFFF');
    }

    /**
     * @return string
     */
    public function render()
    {
        /** @var int $width */
        $width = $this->arguments['width'];
        /** @var string|null $text */
        $text = $this->arguments['text'];
        if (null === $text) {
            /** @var string $text */
            $text = $this->renderChildren();
        }
        /** @var int $height */
        $height = $this->arguments['height'] != $this->arguments['width'] ? $this->arguments['height'] : null;
        $addHeight = !empty($height) ? 'x' . $height : null;
        $url = [
            'https://via.placeholder.com',
            $this->arguments['width'] . $addHeight,
            $this->arguments['backgroundColor'],
            $this->arguments['textColor'],
        ];
        if (!empty($text)) {
            $url[] = '?text=' . urlencode($text);
        }
        $imageUrl = implode('/', $url);
        $this->tag->forceClosingTag(false);
        $this->tag->addAttribute('src', $imageUrl);
        $this->tag->addAttribute('alt', $imageUrl);
        $this->tag->addAttribute('width', (string) $width);
        $this->tag->addAttribute('height', (string) (!empty($height) ? $height : $width));
        return $this->tag->render();
    }
}
