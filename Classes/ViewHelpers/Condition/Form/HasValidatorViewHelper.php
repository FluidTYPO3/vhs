<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use FluidTYPO3\Vhs\Traits\ConditionViewHelperTrait;

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

	use ConditionViewHelperTrait;

	/**
	 * @var string
	 */
	const ALTERNATE_FORM_VIEWHELPER_CLASSNAME = 'TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper';

	/**
	 * @var ReflectionService
	 */
	static protected $staticReflectionService;

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
		return static::renderStatic(
			$this->arguments,
			$this->buildRenderChildrenClosure(),
			$this->renderingContext
		);
	}

	/**
	 * Default implementation for use in compiled templates
	 *
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 * @return mixed
	 */
	static public function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {

		if (self::$staticReflectionService === NULL) {
			$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
			self::$staticReflectionService = $objectManager->get('TYPO3\CMS\Extbase\Reflection\ReflectionService');
		}

		$property = $arguments['property'];
		$validatorName = isset($arguments['validatorName']) ? $arguments['validatorName'] : NULL;
		$object = isset($arguments['object']) ? $arguments['object'] : NULL;

		if (NULL === $object) {
			$object = self::getFormObject($renderingContext->getViewHelperVariableContainer());
		}
		$className = get_class($object);
		if (FALSE !== strpos($property, '.')) {
			$pathSegments = explode('.', $property);
			foreach ($pathSegments as $property) {
				if (TRUE === ctype_digit($property)) {
					continue;
				}
				$annotations = self::$staticReflectionService->getPropertyTagValues($className, $property, 'var');
				$possibleClassName = array_pop($annotations);
				if (FALSE !== strpos($possibleClassName, '<')) {
					$className = array_pop(explode('<', trim($possibleClassName, '>')));
				} elseif (TRUE === class_exists($possibleClassName)) {
					$className = $possibleClassName;
				}
			}
		}

		$annotations = self::$staticReflectionService->getPropertyTagValues($className, $property, 'validate');
		$hasEvaluated = TRUE;
		if (0 < count($annotations) && (NULL === $validatorName || TRUE === in_array($validatorName, $annotations))) {
			return static::renderStaticThenChild($arguments, $hasEvaluated);
		}
		return static::renderStaticElseChild($arguments, $hasEvaluated);
	}

	/**
	 * @param ViewHelperVariableContainer $viewHelperVariableContainer
	 * @param string $formClassName
	 * @return DomainObjectInterface|NULL
	 */
	static protected function getFormObject($viewHelperVariableContainer, $formClassName = 'Tx_Fluid_ViewHelpers_FormViewHelper') {
		if (TRUE === $viewHelperVariableContainer->exists($formClassName, 'formObject')) {
			return $viewHelperVariableContainer->get($formClassName, 'formObject');
		}
		if (self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME !== $formClassName) {
			return self::getFormObject($viewHelperVariableContainer, self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME);
		}
		return NULL;
	}
}
