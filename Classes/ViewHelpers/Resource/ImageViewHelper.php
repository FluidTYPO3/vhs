<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;

/**
 * ViewHelper to output or assign a image from FAL
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
class ImageViewHelper extends AbstractImageViewHelper {

	use TemplateVariableViewHelperTrait;

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
		$this->registerTagAttribute('usemap', 'string', 'A hash-name reference to a map element with which to associate the image.', FALSE, NULL);
		$this->registerTagAttribute('ismap', 'string', 'Specifies that its img element provides access to a server-side image map.', FALSE, NULL);
		$this->registerTagAttribute('alt', 'string', 'Equivalent content for those who cannot process images or who have image loading disabled.', FALSE, NULL);
		$this->registerArgument('as', 'string', 'If specified, a template variable with this name containing the requested data will be inserted instead of returning it.', FALSE, NULL);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$files = $this->getFiles();

		$images = $this->preprocessImages($files, TRUE);
		if (TRUE === empty($images)) {
			return NULL;
		}

		$info = array();
		$tags = array();

		foreach ($images as &$image) {
			$source = $this->preprocessSourceUri($image['source']);
			$width = $image['info'][0];
			$height = $image['info'][1];
			$alt = $this->arguments['alt'];
			if (TRUE === empty($alt)) {
				$alt = $image['file']['alternative'];
			}

			$this->tag->addAttribute('src', $source);
			$this->tag->addAttribute('width', $width);
			$this->tag->addAttribute('height', $height);
			$this->tag->addAttribute('alt', $alt);

			$tag = $this->tag->render();
			$image['tag'] = $tag;
			$tags[] = $tag;

			$info[] = array(
				'source' => $source,
				'width' => $width,
				'height' => $height,
				'tag' => $tag
			);
		}
		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return implode('', $tags);
		}
		return $this->renderChildrenWithVariableOrReturnInput($info);
	}

}
