<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * Base class for media related tag based view helpers which is actually
 * an adapted version of Fluid's original abstract base class
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_Media_AbstractMediaTagViewHelper extends Tx_Vhs_ViewHelpers_Media_AbstractMediaViewHelper {

	/**
	 * Names of all registered tag attributes
	 * @var array
	 */
	static private $tagAttributes = array();

	/**
	 * Tag builder instance
	 *
	 * @var Tx_Fluid_Core_ViewHelper_TagBuilder
	 * @api
	 */
	protected $tag = NULL;

	/**
	 * name of the tag to be created by this view helper
	 *
	 * @var string
	 * @api
	 */
	protected $tagName = 'div';

	/**
	 * Inject a TagBuilder
	 *
	 * @param Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder Tag builder
	 * @return void
	 */
	public function injectTagBuilder(Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder) {
		$this->tag = $tagBuilder;
	}

	/**
	 * Sets the tag name to $this->tagName.
	 * Additionally, sets all tag attributes which were registered in
	 * $this->tagAttributes and additionalArguments.
	 *
	 * Will be invoked just before the render method.
	 *
	 * @return void
	 * @api
	 */
	public function initialize() {
		parent::initialize();
		$this->tag->reset();
		$this->tag->setTagName($this->tagName);
		if ($this->hasArgument('additionalAttributes') && is_array($this->arguments['additionalAttributes'])) {
			$this->tag->addAttributes($this->arguments['additionalAttributes']);
		}

		if (isset(self::$tagAttributes[get_class($this)])) {
			foreach (self::$tagAttributes[get_class($this)] as $attributeName) {
				if ($this->hasArgument($attributeName) && $this->arguments[$attributeName] !== '') {
					$this->tag->addAttribute($attributeName, $this->arguments[$attributeName]);
				}
			}
		}
	}

	/**
	 * Register a new tag attribute. Tag attributes are all arguments which will be directly appended to a tag if you call $this->initializeTag()
	 *
	 * @param string $name Name of tag attribute
	 * @param string $type Type of the tag attribute
	 * @param string $description Description of tag attribute
	 * @param boolean $required set to TRUE if tag attribute is required. Defaults to FALSE.
	 * @return void
	 * @api
	 */
	protected function registerTagAttribute($name, $type, $description, $required = FALSE) {
		$this->registerArgument($name, $type, $description, $required, NULL);
		self::$tagAttributes[get_class($this)][$name] = $name;
	}

	/**
	 * Registers all standard HTML universal attributes.
	 * Should be used inside registerArguments();
	 *
	 * @return void
	 * @api
	 */
	protected function registerUniversalTagAttributes() {
		$this->registerTagAttribute('class', 'string', 'CSS class(es) for this element');
		$this->registerTagAttribute('id', 'string', 'Unique (in this file) identifier for this HTML element.');
		$this->registerTagAttribute('lang', 'string', 'Language for this element. Use short names specified in RFC 1766');
		$this->registerTagAttribute('style', 'string', 'Individual CSS styles for this element');
		$this->registerTagAttribute('title', 'string', 'Tooltip text of element');
		$this->registerTagAttribute('accesskey', 'string', 'Keyboard shortcut to access this element');
		$this->registerTagAttribute('contenteditable', 'string', 'Specifies whether the contents of the element are editable.');
		$this->registerTagAttribute('contextmenu', 'string', 'The value of the id attribute on the menu with which to associate the element as a context menu.');
		$this->registerTagAttribute('draggable', 'string', 'Specifies whether the element is draggable.');
		$this->registerTagAttribute('dropzone', 'string', 'Specifies what types of content can be dropped on the element, and instructs the UA about which actions to take with content when it is dropped on the element.');
		$this->registerTagAttribute('tabindex', 'integer', 'Specifies the tab order of this element');
		$this->registerTagAttribute('translate', 'string', 'Specifies whether an element’s attribute values and contents of its children are to be translated when the page is localized, or whether to leave them unchanged.');
		$this->registerTagAttribute('spellcheck', 'string', 'Specifies whether the element represents an element whose contents are subject to spell checking and grammar checking.');
		$this->registerTagAttribute('hidden', 'string', 'Specifies that the element represents an element that is not yet, or is no longer, relevant.');
	}

}
