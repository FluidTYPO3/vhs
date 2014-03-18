<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 ***************************************************************/

/**
 * Placeholder Image ViewHelper
 *
 * Inserts a placeholder image from http://placehold.it/
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Format\Placeholder
 */
class Tx_Vhs_ViewHelpers_Format_Placeholder_ImageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

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
		if ($text === NULL) {
			$text = $this->renderChildren();
		}
		$height =  ($this->arguments['height'] != $this->arguments['width'] ? $this->arguments['height'] : NULL);
		$url = array(
			'http://placehold.it',
			$this->arguments['width'] . ($height ? 'x' . $height : NULL),
			$this->arguments['backgroundColor'],
			$this->arguments['textColor'],
		);
		if ($text) {
			array_push($url, '&text=' . urlencode($text));
		}
		$imageUrl = implode('/', $url);
		$this->tag->forceClosingTag(FALSE);
		$this->tag->addAttribute('src', $imageUrl);
		$this->tag->addAttribute('alt', $imageUrl);
		$this->tag->addAttribute('width', $this->arguments['width']);
		$this->tag->addAttribute('height', $height ? $height : $this->arguments['width']);
		return $this->tag->render();
	}
}