<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
     *
     * @return void
     */
    public function registerArguments()
    {
        $this->registerUniversalTagAttributes();
    }

    /**
     * Registers all standard and HTML5 universal attributes.
     * Should be used inside registerArguments();
     *
     * @return void
     * @api
     */
    protected function registerUniversalTagAttributes()
    {
        parent::registerUniversalTagAttributes();
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
     *
     * @param string $tagName
     * @param mixed $content
     * @param array $attributes
     * @param array $nonEmptyAttributes
     * @return string
     */
    protected function renderTag(
        $tagName,
        $content = null,
        array $attributes = [],
        array $nonEmptyAttributes = ['id', 'class']
    ) {
        $trimmedContent = trim((string) $content);
        $forceClosingTag = (boolean) $this->arguments['forceClosingTag'];
        if (true === empty($trimmedContent) && true === (boolean) $this->arguments['hideIfEmpty']) {
            return '';
        }
        if ('none' === $tagName || true === empty($tagName)) {
            // skip building a tag if special keyword "none" is used, or tag name is empty
            return $trimmedContent;
        }
        $this->tag->setTagName($tagName);
        $this->tag->addAttributes($attributes);
        $this->tag->forceClosingTag($forceClosingTag);
        if (null !== $content) {
            $this->tag->setContent($content);
        }
        // process some attributes differently - if empty, remove the property:
        foreach ($nonEmptyAttributes as $propertyName) {
            $value = $this->arguments[$propertyName];
            if (true === empty($value)) {
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
     * be one of 'none', 'append' or 'prepend'
     *
     * @param string $tagName
     * @param array $attributes
     * @param boolean $forceClosingTag
     * @param string $mode
     * @return string
     */
    protected function renderChildTag($tagName, $attributes = [], $forceClosingTag = false, $mode = 'none')
    {
        $tagBuilder = clone $this->tag;
        $tagBuilder->reset();
        $tagBuilder->setTagName($tagName);
        $tagBuilder->addAttributes($attributes);
        $tagBuilder->forceClosingTag($forceClosingTag);
        $childTag = $tagBuilder->render();
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
