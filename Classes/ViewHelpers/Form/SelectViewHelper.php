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
 * Select ViewHelper (with support for Optgroup and Option subnodes)
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Form
 */
class Tx_Vhs_ViewHelpers_Form_SelectViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'select';

	/**
	 * @var mixed
	 */
	protected $selectedValue = NULL;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('size', 'string', 'Size of input field');
		$this->registerTagAttribute('disabled', 'string', 'Specifies that the input element should be disabled when the page loads');
		$this->registerArgument('multiple', 'boolean', 'if set, multiple select field', FALSE, FALSE);
		$this->registerArgument('options', 'array', 'Associative array with internal IDs as key, and the values are displayed in the select box');
		$this->registerArgument('optionValueField', 'string', 'If specified, will call the appropriate getter on each object to determine the value.');
		$this->registerArgument('optionLabelField', 'string', 'If specified, will call the appropriate getter on each object to determine the label.');
		$this->registerArgument('sortByOptionLabel', 'boolean', 'If true, List will be sorted by label.', FALSE, FALSE);
		$this->registerArgument('selectAllByDefault', 'boolean', 'If specified options are selected if none was set before.', FALSE, FALSE);
		$this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this view helper', FALSE, 'f3-form-error');
	}

	/**
	 * Render the tag.
	 *
	 * @return string rendered tag.
	 * @api
	 */
	public function render() {
		$name = $this->getName();
		if (TRUE === (boolean) $this->arguments['multiple']) {
			$name .= '[]';
		}

		$this->tag->addAttribute('name', $name);

		if (TRUE === isset($this->arguments['options']) && FALSE === empty($this->arguments['options'])) {
			$options = $this->getOptions();
			if (TRUE === empty($options)) {
				$options = array('' => '');
			}
			$this->tag->setContent($this->renderOptionTags($options));
		} else {
			$this->viewHelperVariableContainer->add('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'options', array());
			$this->viewHelperVariableContainer->add('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'value', $this->getValue());
			$tagContent = $this->renderChildren();
			$options = $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'options');
			$this->tag->setContent($tagContent);
			$this->viewHelperVariableContainer->remove('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'options');
			if (TRUE === $this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'value')) {
				$this->viewHelperVariableContainer->remove('Tx_Vhs_ViewHelpers_Form_SelectViewHelper', 'value');
			}
		}

		$this->setErrorClassAttribute();

		$content = '';

		// register field name for token generation.
		// in case it is a multi-select, we need to register the field name
		// as often as there are elements in the box
		if (TRUE === (boolean) $this->arguments['multiple']) {
			$content .= $this->renderHiddenFieldForEmptyValue();
			$length = count($options);
			for ($i = 0; $i < $length; $i++) {
				$this->registerFieldNameForFormTokenGeneration($name);
			}
			$this->tag->addAttribute('multiple', 'multiple');
		} else {
			$this->registerFieldNameForFormTokenGeneration($name);
			$this->tag->removeAttribute('multiple');
		}

		$content .= $this->tag->render();
		return $content;
	}

	/**
	 * Render the option tags.
	 *
	 * @param array $options the options for the form.
	 * @return string rendered tags.
	 */
	protected function renderOptionTags($options) {
		$output = '';

		foreach ($options as $value => $label) {
			$isSelected = $this->isSelected($value);
			$output .= $this->renderOptionTag($value, $label, $isSelected) . chr(10);
		}
		return $output;
	}

	/**
	 * Render the option tags.
	 *
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 * @return array
	 */
	protected function getOptions() {
		if (FALSE === is_array($this->arguments['options']) && FALSE === ($this->arguments['options'] instanceof Traversable)) {
			return array();
		}
		$options = array();
		$optionsArgument = $this->arguments['options'];
		foreach ($optionsArgument as $key => $value) {
			if (is_object($value)) {

				if (TRUE === isset($this->arguments['optionValueField']) && FALSE === empty($this->arguments['optionValueField'])) {
					$key = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($value, $this->arguments['optionValueField']);
					if (TRUE === is_object($key)) {
						if (TRUE === method_exists($key, '__toString')) {
							$key = (string) $key;
						} else {
							throw new Tx_Fluid_Core_ViewHelper_Exception('Identifying value for object of class "' . get_class($value) . '" was an object.', 1247827428);
						}
					}
				} elseif (NULL !== $this->persistenceManager->getBackend()->getIdentifierByObject($value)) {
					$key = $this->persistenceManager->getBackend()->getIdentifierByObject($value);
				} elseif (TRUE === method_exists($value, '__toString')) {
					$key = (string) $value;
				} else {
					throw new Tx_Fluid_Core_ViewHelper_Exception('No identifying value for object of class "' . get_class($value) . '" found.', 1247826696);
				}

				if (TRUE === isset($this->arguments['optionLabelField']) && FALSE === empty($this->arguments['optionLabelField'])) {
					$value = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($value, $this->arguments['optionLabelField']);
					if (TRUE === is_object($value)) {
						if (TRUE === method_exists($value, '__toString')) {
							$value = (string) $value;
						} else {
							throw new Tx_Fluid_Core_ViewHelper_Exception('Label value for object of class "' . get_class($value) . '" was an object without a __toString() method.', 1247827553);
						}
					}
				} elseif (TRUE === method_exists($value, '__toString')) {
					$value = (string) $value;
				} elseif (NULL !== $this->persistenceManager->getBackend()->getIdentifierByObject($value)) {
					$value = $this->persistenceManager->getBackend()->getIdentifierByObject($value);
				}
			}
			$options[$key] = $value;
		}
		if (TRUE === isset($this->arguments['sortByOptionLabel']) && FALSE === empty($this->arguments['sortByOptionLabel'])) {
			asort($options);
		}
		return $options;
	}

	/**
	 * Render the option tags.
	 *
	 * @param mixed $value Value to check for
	 * @return boolean TRUE if the value should be marked a s selected; FALSE otherwise
	 */
	protected function isSelected($value) {
		$selectedValue = $this->getSelectedValue();
		if ($value === $selectedValue || (string) $value === $selectedValue) {
			return TRUE;
		}
		if (TRUE === isset($this->arguments['multiple']) && FALSE === empty($this->arguments['multiple'])) {
			if (TRUE === is_null($selectedValue) && TRUE === (boolean) $this->arguments['selectAllByDefault']) {
				return TRUE;
			} elseif (TRUE === is_array($selectedValue) && TRUE === in_array($value, $selectedValue)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Retrieves the selected value(s)
	 *
	 * @return mixed value string or an array of strings
	 */
	protected function getSelectedValue() {
		$value = $this->getValue();
		if (FALSE === isset($this->arguments['optionValueField']) || TRUE === empty($this->arguments['optionValueField'])) {
			return $value;
		}
		if (FALSE === is_array($value) && FALSE === ($value instanceof Iterator)) {
			if (TRUE === is_object($value)) {
				return \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($value, $this->arguments['optionValueField']);
			} else {
				return $value;
			}
		}
		$selectedValues = array();
		foreach ($value as $selectedValueElement) {
			if (TRUE === is_object($selectedValueElement)) {
				$selectedValues[] = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($selectedValueElement, $this->arguments['optionValueField']);
			} else {
				$selectedValues[] = $selectedValueElement;
			}
		}
		return $selectedValues;
	}

	/**
	 * Render one option tag
	 *
	 * @param string $value value attribute of the option tag (will be escaped)
	 * @param string $label content of the option tag (will be escaped)
	 * @param boolean $isSelected specifies wheter or not to add selected attribute
	 * @return string the rendered option tag
	 */
	protected function renderOptionTag($value, $label, $isSelected) {
		$output = '<option value="' . htmlspecialchars($value) . '"';
		if (TRUE === (boolean) $isSelected) {
			$output .= ' selected="selected"';
		}
		$output .= '>' . htmlspecialchars($label) . '</option>';

		return $output;
	}
}
