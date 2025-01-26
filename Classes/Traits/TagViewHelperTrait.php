<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class TagViewHelperTrait
 *
 * Trait implemented by ViewHelpers which require access
 * to functions dealing with tag generation.
 *
 * Has the following main responsibilities:
 *
 * - register additional HTML5-specific attributes for tag
 *   based ViewHelpers
 * - custom rendering method which applies those attributes.
 */
trait TagViewHelperTrait
{
    /**
     * Default implementation to register only the tag
     * arguments along with universal attributes.
     */
    public function registerArguments(): void
    {
        $this->registerUniversalTagAttributes();
    }

    /**
     * Register a new tag attribute. Tag attributes are all arguments which will be directly appended to a tag if you
     * call $this->initializeTag()
     *
     * @param string $name Name of tag attribute
     * @param string $type Type of the tag attribute
     * @param string $description Description of tag attribute
     * @param bool $required set to true if tag attribute is required. Defaults to false.
     * @param mixed $defaultValue Optional, default value of attribute if one applies
     * @return void
     * @api
     */
    protected function registerTagAttribute($name, $type, $description, $required = false, $defaultValue = null)
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '13.4', '>=')) {
            $this->registerArgument($name, $type, $description, $required, $defaultValue);
            return;
        }
        parent::registerTagAttribute($name, $type, $description, $required, $defaultValue);
    }

    /**
     * Registers all standard and HTML5 universal attributes.
     * Should be used inside registerArguments();
     */
    protected function registerUniversalTagAttributes(): void
    {
        $this->registerTagAttribute('class', 'string', 'CSS class(es) for this element');
        $this->registerTagAttribute(
            'dir',
            'string',
            'Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)'
        );
        $this->registerTagAttribute('id', 'string', 'Unique (in this file) identifier for this HTML element.');
        $this->registerTagAttribute(
            'lang',
            'string',
            'Language for this element. Use short names specified in RFC 1766'
        );
        $this->registerTagAttribute('style', 'string', 'Individual CSS styles for this element');
        $this->registerTagAttribute('title', 'string', 'Tooltip text of element');
        $this->registerTagAttribute('accesskey', 'string', 'Keyboard shortcut to access this element');
        $this->registerTagAttribute('tabindex', 'integer', 'Specifies the tab order of this element');
        $this->registerTagAttribute('onclick', 'string', 'JavaScript evaluated for the onclick event');

        $this->registerArgument(
            'forceClosingTag',
            'boolean',
            'If TRUE, forces the created tag to use a closing tag. If FALSE, allows self-closing tags.',
            false,
            false
        );
        $this->registerArgument(
            'hideIfEmpty',
            'boolean',
            'Hide the tag completely if there is no tag content',
            false,
            false
        );
        $this->registerTagAttribute(
            'contenteditable',
            'string',
            'Specifies whether the contents of the element are editable.'
        );
        $this->registerTagAttribute(
            'contextmenu',
            'string',
            'The value of the id attribute on the menu with which to associate the element as a context menu.'
        );
        $this->registerTagAttribute(
            'draggable',
            'string',
            'Specifies whether the element is draggable.'
        );
        $this->registerTagAttribute(
            'dropzone',
            'string',
            'Specifies what types of content can be dropped on the element, and instructs the UA about which ' .
            'actions to take with content when it is dropped on the element.'
        );
        $this->registerTagAttribute(
            'translate',
            'string',
            'Specifies whether an element’s attribute values and contents of its children are to be translated ' .
            'when the page is localized, or whether to leave them unchanged.'
        );
        $this->registerTagAttribute(
            'spellcheck',
            'string',
            'Specifies whether the element represents an element whose contents are subject to spell checking and ' .
            'grammar checking.'
        );
        $this->registerTagAttribute(
            'hidden',
            'string',
            'Specifies that the element represents an element that is not yet, or is no longer, relevant.'
        );
    }

    /**
     * Renders the provided tag with the given name and any
     * (additional) attributes not already provided as arguments.
     */
    protected function renderTag(
        string $tagName,
        ?string $content = null,
        array $attributes = [],
        array $nonEmptyAttributes = ['id', 'class']
    ): string {
        $trimmedContent = trim((string) $content);
        $forceClosingTag = (boolean) ($this->arguments['forceClosingTag'] ?? false);
        if (empty($trimmedContent) && ($this->arguments['hideIfEmpty'] ?? false)) {
            return '';
        }
        if ('none' === $tagName || empty($tagName)) {
            // skip building a tag if special keyword "none" is used, or tag name is empty
            return $trimmedContent;
        }
        $this->tag->setTagName($tagName);
        $this->tag->addAttributes($attributes);
        $this->tag->forceClosingTag($forceClosingTag);
        if (null !== $content) {
            $this->tag->setContent($trimmedContent);
        }
        // process some attributes differently - if empty, remove the property:
        foreach ($nonEmptyAttributes as $propertyName) {
            /** @var string|null $value */
            $value = $this->arguments[$propertyName] ?? null;
            if (empty($value)) {
                $this->tag->removeAttribute($propertyName);
            } else {
                $this->tag->addAttribute($propertyName, $value);
            }
        }
        return $this->tag->render();
    }

    /**
     * Renders the provided tag and optionally appends or prepends
     * it to the main tag's content depending on 'mode' which can
     * be one of 'none', 'append' or 'prepend'.
     */
    protected function renderChildTag(
        string $tagName,
        array $attributes = [],
        bool $forceClosingTag = false,
        string $mode = 'none'
    ): string {
        $content = $this->tag->getContent();

        $tagBuilder = clone $this->tag;
        $tagBuilder->reset();
        $tagBuilder->setTagName($tagName);
        $tagBuilder->addAttributes($attributes);
        $tagBuilder->forceClosingTag($forceClosingTag);
        $childTag = $tagBuilder->render();
        if ('append' === $mode || 'prepend' === $mode) {
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
