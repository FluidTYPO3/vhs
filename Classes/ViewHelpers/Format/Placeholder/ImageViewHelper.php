<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Placeholder;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

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

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
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
        $text = $this->arguments['text'];
        if (null === $text) {
            $text = $this->renderChildren();
        }
        $height = $this->arguments['height'] != $this->arguments['width'] ? $this->arguments['height'] : null;
        $addHeight = false === empty($height) ? 'x' . $height : null;
        $url = [
            'https://placehold.it',
            $this->arguments['width'] . $addHeight,
            $this->arguments['backgroundColor'],
            $this->arguments['textColor'],
        ];
        if (false === empty($text)) {
            array_push($url, '&text=' . urlencode($text));
        }
        $imageUrl = implode('/', $url);
        $this->tag->forceClosingTag(false);
        $this->tag->addAttribute('src', $imageUrl);
        $this->tag->addAttribute('alt', $imageUrl);
        $this->tag->addAttribute('width', $this->arguments['width']);
        $this->tag->addAttribute('height', false === empty($height) ? $height : $this->arguments['width']);
        return $this->tag->render();
    }
}
