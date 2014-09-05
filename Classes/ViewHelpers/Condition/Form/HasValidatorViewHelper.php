<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Form;

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
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

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
class HasValidatorViewHelper extends AbstractConditionViewHelper {

	/**
	 * @var string
	 */
	const ALTERNATE_FORM_VIEWHELPER_CLASSNAME = 'TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper';

	/**
	 * Note: property name is "ownReflectionService" because "reflectionService"
	 * is used by the parent class - but is, quite unfriendly and needlessly, set
	 * with "private" access.
	 *
	 * @var ReflectionService
	 */
	protected $ownReflectionService;

	/**
	 * @param ReflectionService $reflectionService
	 * @return void
	 */
	public function injectOwnReflectionService(ReflectionService $reflectionService) {
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
	 * @param DomainObjectInterface $object Optional object - if not specified, grabs the associated form object
	 * @return string
	 */
	public function render($property, $validatorName = NULL, DomainObjectInterface $object = NULL) {
		if (NULL === $object) {
			$object = $this->getFormObject();
			$className = get_class($object);
		}
		if (FALSE !== strpos($property, '.')) {
			$pathSegments = explode('.', $property);
			foreach ($pathSegments as $property) {
				if (TRUE === ctype_digit($property)) {
					continue;
				}
				$annotations = $this->ownReflectionService->getPropertyTagValues($className, $property, 'var');
				$possibleClassName = array_pop($annotations);
				if (FALSE !== strpos($possibleClassName, '<')) {
					$className = array_pop(explode('<', trim($possibleClassName, '>')));
				} elseif (TRUE === class_exists($possibleClassName)) {
					$className = $possibleClassName;
				}
			}
		}
		$annotations = $this->ownReflectionService->getPropertyTagValues($className, $property, 'validate');
		if (0 < count($annotations) && (NULL === $validatorName || TRUE === in_array($validatorName, $annotations))) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

	/**
	 * @param string $formClassName
	 * @return DomainObjectInterface|NULL
	 */
	protected function getFormObject($formClassName = 'Tx_Fluid_ViewHelpers_FormViewHelper') {
		if (TRUE === $this->viewHelperVariableContainer->exists($formClassName, 'formObject')) {
			return $this->viewHelperVariableContainer->get($formClassName, 'formObject');
		}
		if (self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME !== $formClassName) {
			return $this->getFormObject(self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME);
		}
		return NULL;
	}

}
