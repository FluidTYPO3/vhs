<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Media\Image\AbstractImageViewHelper;
use FluidTYPO3\Vhs\Traits\SourceSetViewHelperTrait;

/**
 * Renders an image tag for the given resource including all valid
 * HTML5 attributes. Derivates of the original image are rendered
 * if the provided (optional) dimensions differ.
 *
 * ## rendering responsive Images variants
 *
 * You can use the srcset argument to generate several differently sized
 * versions of this image that will be added as a srcset argument to the img tag.
 * enter a list of widths in the srcset to genereate copies of the same crop +
 * ratio but in the specified widths.
 *
 * ### Example
 *
 *     <v:media.image src="fileadmin/some-image.png" srcset="480,768,992,1200" />
 *
 * ### Browser Support
 *
 * To have the widest Browser-Support you should consider using a polyfill like:
 * http://scottjehl.github.io/picturefill/
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class ImageViewHelper extends AbstractImageViewHelper {

	use SourceSetViewHelperTrait;

	/**
	 * name of the tag to be created by this view helper
	 *
	 * @var string
	 * @api
	 */
	protected $tagName = 'img';

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('usemap', 'string', 'A hash-name reference to a map element with which to associate the image.', FALSE);
		$this->registerTagAttribute('ismap', 'string', 'Specifies that its img element provides access to a server-side image map.', FALSE, '');
		$this->registerTagAttribute('alt', 'string', 'Equivalent content for those who cannot process images or who have image loading disabled.', TRUE);
		$this->registerArgument('srcset', 'mixed', 'list of width used for the srcset variants (either CSV, array or implementing Traversable)', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$this->preprocessImage();

		if (NULL !== $this->arguments['srcset']) {
			$this->addSourceSet($this->tag, $this->mediaSource);
		} else {
			$src = $this->preprocessSourceUri($this->mediaSource);
			$this->tag->addAttribute('src', $src);
		}

		$this->tag->addAttribute('width', $this->imageInfo[0]);
		$this->tag->addAttribute('height', $this->imageInfo[1]);
		if ('' === $this->arguments['title']) {
			$this->tag->addAttribute('title', $this->arguments['alt']);
		}
		return $this->tag->render();
	}

}
