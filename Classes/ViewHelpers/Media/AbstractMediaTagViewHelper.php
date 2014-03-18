<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Base class for media related tag based view helpers which mostly
 * adds HTML5 tag attributes.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
abstract class Tx_Vhs_ViewHelpers_Media_AbstractMediaTagViewHelper extends Tx_Vhs_ViewHelpers_Media_AbstractMediaViewHelper {

	/**
	 * Registers all standard and HTML5 universal attributes.
	 * Should be used inside registerArguments();
	 *
	 * @return void
	 * @api
	 */
	protected function registerUniversalTagAttributes() {
		parent::registerUniversalTagAttributes();
		$this->registerTagAttribute('contenteditable', 'string', 'Specifies whether the contents of the element are editable.');
		$this->registerTagAttribute('contextmenu', 'string', 'The value of the id attribute on the menu with which to associate the element as a context menu.');
		$this->registerTagAttribute('draggable', 'string', 'Specifies whether the element is draggable.');
		$this->registerTagAttribute('dropzone', 'string', 'Specifies what types of content can be dropped on the element, and instructs the UA about which actions to take with content when it is dropped on the element.');
		$this->registerTagAttribute('translate', 'string', 'Specifies whether an element’s attribute values and contents of its children are to be translated when the page is localized, or whether to leave them unchanged.');
		$this->registerTagAttribute('spellcheck', 'string', 'Specifies whether the element represents an element whose contents are subject to spell checking and grammar checking.');
		$this->registerTagAttribute('hidden', 'string', 'Specifies that the element represents an element that is not yet, or is no longer, relevant.');
	}

	/**
	 * Renders the provided tag and optionally appends or prepends
	 * it to the main tag's content depending on 'mode' which can
	 * be one of 'none', 'append' or 'prepend'
	 *
	 * @param string $tagName
	 * @param array $attributes
	 * @param boolean $forceClosingTag
	 * @param string $mode
	 * @return string
	 */
	public function renderChildTag($tagName, $attributes = array(), $forceClosingTag = FALSE, $mode = 'none') {
		$tagBuilder = clone($this->tag);
		$tagBuilder->reset();
		$tagBuilder->setTagName($tagName);
		$tagBuilder->addAttributes($attributes);
		$tagBuilder->forceClosingTag($forceClosingTag);
		$childTag = $tagBuilder->render();
		unset($tagBuilder);
		if ($mode === 'append' || $mode === 'prepend') {
			$content = $this->tag->getContent();
			if ($mode === 'append') {
				$content = $content . $childTag;
			} else {
				$content = $childTag . $content;
			}
			$this->tag->setContent($content);
		}
		return $childTag;
	}
}
