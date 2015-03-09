<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
class FieldNameViewHelper extends AbstractViewHelper {

	/**
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @param PersistenceManagerInterface $persistenceManager
	 */
	public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager) {
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
		if (TRUE === $this->isObjectAccessorMode()) {
			$formObjectName = $this->viewHelperVariableContainer->get('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'formObjectName');
			if (FALSE === empty($formObjectName)) {
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
		if (NULL === $name || '' === $name) {
			return '';
		}
		if (FALSE === $this->viewHelperVariableContainer->exists('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'fieldNamePrefix')) {
			return $name;
		}
		$fieldNamePrefix = (string) $this->viewHelperVariableContainer->get('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'fieldNamePrefix');
		if ('' === $fieldNamePrefix) {
			return $name;
		}
		$fieldNameSegments = explode('[', $name, 2);
		$name = $fieldNamePrefix . '[' . $fieldNameSegments[0] . ']';
		if (1 < count($fieldNameSegments)) {
			$name .= '[' . $fieldNameSegments[1];
		}
		if (TRUE === $this->viewHelperVariableContainer->exists('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'formFieldNames')) {
			$formFieldNames = $this->viewHelperVariableContainer->get('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'formFieldNames');
		} else {
			$formFieldNames = array();
		}
		$formFieldNames[] = $name;
		$this->viewHelperVariableContainer->addOrUpdate('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'formFieldNames', $formFieldNames);
		return $name;
	}

	/**
	 * @return boolean
	 */
	protected function isObjectAccessorMode() {
		return (boolean) (TRUE === $this->hasArgument('property') && TRUE === $this->viewHelperVariableContainer->exists('TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper', 'formObjectName'));
	}

}
