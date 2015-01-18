<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Placeholder;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Placeholder Image ViewHelper
 *
 * Inserts a placeholder image from http://placehold.it/
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Format\Placeholder
 */
class ImageViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'img';

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerArgument('width', 'integer', 'Width of rendered placeholder image', FALSE, 640);
		$this->registerArgument('height', 'integer', 'Height of rendered placeholder image', FALSE, FALSE);
		$this->registerArgument('backgroundColor', 'string', 'Background color', FALSE, '333333');
		$this->registerArgument('textColor', 'string', 'Text color', FALSE, 'FFFFFF');
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function render($text = NULL) {
		if (NULL === $text) {
			$text = $this->renderChildren();
		}
		$height = $this->arguments['height'] != $this->arguments['width'] ? $this->arguments['height'] : NULL;
		$addHeight = FALSE === empty($height) ? 'x' . $height : NULL;
		$url = array(
			'http://placehold.it',
			$this->arguments['width'] . $addHeight,
			$this->arguments['backgroundColor'],
			$this->arguments['textColor'],
		);
		if (FALSE === empty($text)) {
			array_push($url, '&text=' . urlencode($text));
		}
		$imageUrl = implode('/', $url);
		$this->tag->forceClosingTag(FALSE);
		$this->tag->addAttribute('src', $imageUrl);
		$this->tag->addAttribute('alt', $imageUrl);
		$this->tag->addAttribute('width', $this->arguments['width']);
		$this->tag->addAttribute('height', FALSE === empty($height) ? $height : $this->arguments['width']);
		return $this->tag->render();
	}

}
