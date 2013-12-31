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
 * ************************************************************* */

/**
 * Option ViewHelper to use under vhs:form.select
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Form\Select
 */
class Tx_Vhs_ViewHelpers_Form_Select_OptionViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

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
		$this->registerArgument('selected', 'boolean', 'Set to "selected" to mark field as selected. If not present, selected status will be determined by select value');
	}

	/**
	 * @throws Exception
	 * @return string
	 */
	public function render() {
		if (!$this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'options')) {
			throw new Exception('Options can only be added inside select tags, optionally inside optgroup tag(s) inside the select tag', 1313937196);
		}
		if ($this->arguments['selected']) {
			$selected = 'selected';
		} else if ($this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'value')) {
			$value = $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'value');
			if (is_object($this->arguments['value']) === FALSE && is_array($this->arguments['value']) === FALSE) {
				if (is_array($value)) {
					$selected = in_array($this->arguments['value'], $value) ? 'selected' : '';
				} else {
					$selected = (string) $this->arguments['value'] == (string) $value ? 'selected' : '';
				}
			}
			if (is_array($this->arguments['value'])) {
				$selected = in_array($this->arguments['value'], $value) ? 'selected' : '';
			}
		}
		$tagContent = $this->renderChildren();
		$options = $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'options');
		$options[$tagContent] = $this->arguments['value'];
		$this->viewHelperVariableContainer->addOrUpdate('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'options', $options);
		if ($selected) {
			$this->tag->addAttribute('selected', 'selected');
		} else {
			$this->tag->removeAttribute('selected');
		}
		$this->tag->setContent($tagContent);
		if (isset($this->arguments['value'])) {
			$this->tag->addAttribute('value', $this->arguments['value']);
		}
		return $this->tag->render();
	}

}
