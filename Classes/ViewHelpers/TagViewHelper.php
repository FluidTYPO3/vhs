<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ### Tag building ViewHelper
 *
 * Creates one HTML tag of any type, with various properties
 * like class and ID applied only if arguments are not empty,
 * rather than apply them always - empty or not - if provided.
 *
 * @package Vhs
 * @subpackage ViewHelpers
 */
class TagViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerArgument('name', 'string', 'Tag name', TRUE);
		$this->registerArgument('hideIfEmpty', 'boolean', 'Hide the tag completely if there is no tag content', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		$this->arguments['class'] = trim($this->arguments['class']);
		$content = $this->renderChildren();
		$trimmedContent = trim($content);
		if (TRUE === empty($trimmedContent) && TRUE === (boolean) $this->arguments['hideIfEmpty']) {
			return '';
		}
		if ('none' === $this->arguments['name'] || TRUE === empty($this->arguments['name'])) {
			// skip building a tag if special keyword "none" is used, or tag name is empty
			return $content;
		}
		// process a few key variables to support values coming from TCEforms storage:
		if (FALSE === empty($this->arguments['class'])) {
			$class = str_replace(',', ' ', $this->arguments['class']);
			$this->tag->addAttribute('class', $class);
		}
		$this->tag->setTagName($this->arguments['name']);
		$this->tag->setContent($content);
		return $this->tag->render();
	}

	/**
	 * @param array $attributes
	 * @return void
	 */
	protected function applyAttributes($attributes) {
		if (NULL === $attributes) {
			return;
		}
		foreach ($attributes as $attributeName => $attributeValue) {
			if ('none' !== $attributeValue && (FALSE === empty($attributeValue) || 0 === $attributeValue || '0' === $attributeValue)) {
				$this->tag->addAttribute($attributeName, $attributeValue);
			}
		}
	}

}
