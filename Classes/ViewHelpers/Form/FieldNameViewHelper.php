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
 * Form Field Name View Helper
 *
 * This viewhelper returns the properly prefixed name of the given
 * form field and generates the corresponding HMAC to allow posting
 * of dynamically added fields.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Form
 */
class Tx_Vhs_ViewHelpers_Form_FieldNameViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Persistence_Manager
	 */
	protected $persistenceManager;

	/**
	 * @param Tx_Extbase_Persistence_Manager $persistenceManager
	 */
	public function injectPersistenceManager(Tx_Extbase_Persistence_Manager $persistenceManager) {
		$this->persistenceManager = $persistenceManager;
	}

	/**
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of the form field to generate the HMAC for.');
		$this->registerArgument('property', 'string', 'Name of object property. If used in conjunction with <f:form object="...">, "name" argument will be ignored.');
	}

	/**
	 * @return string
	 */
	public function render() {
		$variableNameSpace = $this->getVariableNameSpace();
		if ($this->isObjectAccessorMode()) {
			$formObjectName = $this->viewHelperVariableContainer->get($variableNameSpace, 'formObjectName');
			if (!empty($formObjectName)) {
				$propertySegments = explode('.', $this->arguments['property']);
				$propertyPath = '';
				foreach ($propertySegments as $segment) {
					$propertyPath .= '[' . $segment . ']';
				}
				$name = $formObjectName . $propertyPath;
			} else {
				$name = $this->arguments['property'];
			}
		} else {
			$name = $this->arguments['name'];
		}
		if ($this->hasArgument('value') && is_object($this->arguments['value'])) {
			if (NULL !== $this->persistenceManager->getIdentifierByObject($this->arguments['value'])) {
				$name .= '[__identity]';
			}
		}
		if (NULL === $name || '' === $name) {
			return '';
		}
		if (FALSE === $this->viewHelperVariableContainer->exists($variableNameSpace, 'fieldNamePrefix')) {
			return $name;
		}
		$fieldNamePrefix = (string) $this->viewHelperVariableContainer->get($variableNameSpace, 'fieldNamePrefix');
		if ('' === $fieldNamePrefix) {
			return $name;
		}
		$fieldNameSegments = explode('[', $name, 2);
		$name = $fieldNamePrefix . '[' . $fieldNameSegments[0] . ']';
		if (count($fieldNameSegments) > 1) {
			$name .= '[' . $fieldNameSegments[1];
		}
		if ($this->viewHelperVariableContainer->exists($variableNameSpace, 'formFieldNames')) {
			$formFieldNames = $this->viewHelperVariableContainer->get($variableNameSpace, 'formFieldNames');
		} else {
			$formFieldNames = array();
		}
		$formFieldNames[] = $name;
		$this->viewHelperVariableContainer->addOrUpdate($variableNameSpace, 'formFieldNames', $formFieldNames);
		return $name;
	}

	/**
	 * @return boolean
	 */
	protected function isObjectAccessorMode() {
		$variableNameSpace = $this->getVariableNameSpace();
		return TRUE === $this->hasArgument('property') && TRUE === $this->viewHelperVariableContainer->exists($variableNameSpace, 'formObjectName');
	}

	/**
	 * @return string
	 */
	protected function getVariableNameSpace() {
		if (TRUE === $this->viewHelperVariableContainer->exists('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'formFieldNames')) {
			return 'TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper';
		}
		return 'Tx_Fluid_ViewHelpers_FormViewHelper';
	}
}
