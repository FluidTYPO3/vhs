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
 * ### Form: Field Has Validator?
 *
 * Takes a property (dotted path supported) and renders the
 * then-child if the property at the given path has any
 * @validate annotation.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\If\Form
 */
class Tx_Vhs_ViewHelpers_If_Form_HasValidatorViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * @var string
	 */
	const ALTERNATE_FORM_VIEWHELPER_CLASSNAME = 'TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper';

	/**
	 * Note: property name is "ownReflectionService" because "reflectionService"
	 * is used by the parent class - but is, quite unfriendly and needlessly, set
	 * with "private" access.
	 *
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $ownReflectionService;

	/**
	 * @param Tx_Extbase_Reflection_Service $reflectionService
	 * @return void
	 */
	public function injectOwnReflectionService(Tx_Extbase_Reflection_Service $reflectionService) {
		$this->ownReflectionService = $reflectionService;
	}

	/**
	 * Render
	 *
	 * Renders the then-child if the property at $property of the
	 * object at $object (or the associated form object if $object
	 * is not specified) uses a certain @validate validator.
	 *
	 * @param string $property The property name, dotted path supported, to determine required
	 * @param string $validatorName The class name of the Validator that indicates the property is required
	 * @param Tx_Extbase_DomainObject_DomainObjectInterface $object Optional object - if not specified, grabs the associated form object
	 * @return string
	 */
	public function render($property, $validatorName = NULL, Tx_Extbase_DomainObject_DomainObjectInterface $object = NULL) {
		if ($object === NULL) {
			$object = $this->getFormObject();
			$className = get_class($object);
		}
		if (strpos($property, '.') !== FALSE) {
			$pathSegments = explode('.', $property);
			foreach ($pathSegments as $property) {
				if (ctype_digit($property)) {
					continue;
				}
				$annotations = $this->ownReflectionService->getPropertyTagValues($className, $property, 'var');
				$possibleClassName = array_pop($annotations);
				if (strpos($possibleClassName, '<') !== FALSE) {
					$className = array_pop(explode('<', trim($possibleClassName, '>')));
				} elseif (class_exists($possibleClassName) === TRUE) {
					$className = $possibleClassName;
				}
			}
		}
		$annotations = $this->ownReflectionService->getPropertyTagValues($className, $property, 'validate');
		if (0 < count($annotations) && (NULL === $validatorName || in_array($validatorName, $annotations) === TRUE)) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

	/**
	 * @param string $formClassName
	 * @return Tx_Extbase_DomainObject_DomainObjectInterface|NULL
	 */
	protected function getFormObject($formClassName = 'Tx_Fluid_ViewHelpers_FormViewHelper') {
		if ($this->viewHelperVariableContainer->exists($formClassName, 'formObject')) {
			return $this->viewHelperVariableContainer->get($formClassName, 'formObject');
		}
		if ($formClassName !== self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME) {
			return $this->getFormObject(self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME);
		}
		return NULL;
	}

}
