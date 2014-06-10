<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Resource;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * ViewHelper to output or assign a image from FAL
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Resource
 */
use FluidTYPO3\Vhs\Utility\ViewHelperUtility;

class ImageViewHelper extends AbstractImageViewHelper {

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

		// Return if no assign
		$as = $this->arguments['as'];
		if (TRUE === empty($as)) {
			return implode('', $tags);
		}

		$variables = array($as => $info);
		$output = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		return $output;
	}

}
