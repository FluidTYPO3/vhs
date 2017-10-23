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
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper;

/**
 * ### Form: Field Has Validator?
 *
 * Takes a property (dotted path supported) and renders the
 * then-child if the property at the given path has any
 * @validate annotation.
 */
class HasValidatorViewHelper extends AbstractConditionViewHelper
{
    /**
     * @var string
     */
    const ALTERNATE_FORM_VIEWHELPER_CLASSNAME = FormViewHelper::class;

    /**
     * @var ReflectionService
     */
    static protected $staticReflectionService;

    /**
     * Initialize
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'property',
            'string',
            'The property name, dotted path supported, to determine required.',
            true
        );
        $this->registerArgument(
            'validatorName',
            'string',
            'The class name of the Validator that indicates the property is required.'
        );
        $this->registerArgument(
            'object',
            DomainObjectInterface::class,
            'Optional object - if not specified, grabs the associated form object.'
        );
    }

    /**
     * @param ViewHelperVariableContainer $viewHelperVariableContainer
     * @param string $formClassName
     * @return DomainObjectInterface|NULL
     */
    protected static function getFormObject($viewHelperVariableContainer, $formClassName = FormViewHelper::class)
    {
        if (true === $viewHelperVariableContainer->exists($formClassName, 'formObject')) {
            return $viewHelperVariableContainer->get($formClassName, 'formObject');
        }
        if (self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME !== $formClassName) {
            return self::getFormObject($viewHelperVariableContainer, self::ALTERNATE_FORM_VIEWHELPER_CLASSNAME);
        }
        return null;
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    protected static function evaluateCondition($arguments = null)
    {
        if (self::$staticReflectionService === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            self::$staticReflectionService = $objectManager->get(ReflectionService::class);
        }

        $property = $arguments['property'];
        $validatorName = isset($arguments['validatorName']) ? $arguments['validatorName'] : null;
        $object = isset($arguments['object']) ? $arguments['object'] : null;

        if (null === $object) {
            $object = static::getFormObject($renderingContext->getViewHelperVariableContainer());
        }
        $className = get_class($object);
        if (false !== strpos($property, '.')) {
            $pathSegments = explode('.', $property);
            foreach ($pathSegments as $property) {
                if (true === ctype_digit($property)) {
                    continue;
                }
                $annotations = self::$staticReflectionService->getPropertyTagValues($className, $property, 'var');
                $possibleClassName = array_pop($annotations);
                if (false !== strpos($possibleClassName, '<')) {
                    $className = array_pop(explode('<', trim($possibleClassName, '>')));
                } elseif (true === class_exists($possibleClassName)) {
                    $className = $possibleClassName;
                }
            }
        }

        $annotations = self::$staticReflectionService->getPropertyTagValues($className, $property, 'validate');
        return (count($annotations) && (!$validatorName || in_array($validatorName, $annotations)));
    }
}
