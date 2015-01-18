<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Base class for media related tag based view helpers which mostly
 * adds HTML5 tag attributes.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
abstract class AbstractMediaTagViewHelper extends AbstractMediaViewHelper {

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
		$tagBuilder = clone $this->tag;
		$tagBuilder->reset();
		$tagBuilder->setTagName($tagName);
		$tagBuilder->addAttributes($attributes);
		$tagBuilder->forceClosingTag($forceClosingTag);
		$childTag = $tagBuilder->render();
		unset($tagBuilder);
		if ('append' === $mode || 'prepend' === $mode) {
			$content = $this->tag->getContent();
			if ('append' === $mode) {
				$content = $content . $childTag;
			} else {
				$content = $childTag . $content;
			}
			$this->tag->setContent($content);
		}
		return $childTag;
	}

}
