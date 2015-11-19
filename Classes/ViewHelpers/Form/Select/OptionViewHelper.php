<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Form\Select;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * Option ViewHelper to use under vhs:form.select
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Form\Select
 */
class OptionViewHelper extends AbstractFormFieldViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'option';

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerArgument('selected', 'boolean', 'Set to TRUE to mark field as selected; otherwise detected from field value');
	}

	/**
	 * @throws \RuntimeException
	 * @return string
	 */
	public function render() {
		if (!$this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Form\SelectViewHelper', 'options')) {
			throw new \RuntimeException(
				'Options can only be added inside select tags, optionally inside optgroup tag(s) inside the select tag',
				1313937196
			);
		}
		if (TRUE === (boolean) $this->arguments['selected']) {
			$selected = 'selected';
		} else if (TRUE === $this->viewHelperVariableContainer->exists('FluidTYPO3\\Vhs\\ViewHelpers\\Form\\SelectViewHelper', 'value')) {
			$value = $this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\Form\\SelectViewHelper', 'value');
			if (FALSE === is_object($this->arguments['value']) && FALSE === is_array($this->arguments['value'])) {
				if (TRUE === is_array($value)) {
					$selected = TRUE === in_array($this->arguments['value'], $value) ? 'selected' : '';
				} else {
					$selected = (string) $this->arguments['value'] == (string) $value ? 'selected' : '';
				}
			}
		}
		$tagContent = $this->renderChildren();
		$options = $this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\Form\\SelectViewHelper', 'options');
		$options[$tagContent] = $this->arguments['value'];
		$this->viewHelperVariableContainer->addOrUpdate('FluidTYPO3\\Vhs\\ViewHelpers\\Form\\SelectViewHelper', 'options', $options);
		if (FALSE === empty($selected)) {
			$this->tag->addAttribute('selected', 'selected');
		} else {
			$this->tag->removeAttribute('selected');
		}
		$this->tag->setContent($tagContent);
		if (TRUE === isset($this->arguments['value'])) {
			$this->tag->addAttribute('value', $this->arguments['value']);
		}
		return $this->tag->render();
	}

}
