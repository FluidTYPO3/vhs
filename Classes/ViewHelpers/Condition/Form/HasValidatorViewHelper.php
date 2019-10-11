<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Condition\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

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
            'The name of the validator that must exist for the condition to be true.',
            true
        );
        $this->registerArgument(
            'object',
            DomainObjectInterface::class,
            'Optional object - if not specified, grabs the associated form object.',
            true
        );
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    protected static function evaluateCondition($arguments = null)
    {
        if (static::$staticReflectionService === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$staticReflectionService = $objectManager->get(ReflectionService::class);
        }

        $property = $arguments['property'];
        $validatorName = isset($arguments['validatorName']) ? $arguments['validatorName'] : null;
        $object = isset($arguments['object']) ? $arguments['object'] : null;

        $className = get_class($object);
        if (false !== strpos($property, '.')) {
            $pathSegments = explode('.', $property);
            foreach ($pathSegments as $property) {
                if (true === ctype_digit($property)) {
                    continue;
                }
                $annotations = static::$staticReflectionService->getPropertyTagValues($className, $property, 'var');
                $possibleClassName = array_pop($annotations);
                if (false !== strpos($possibleClassName, '<')) {
                    $className = array_pop(explode('<', trim($possibleClassName, '>')));
                } elseif (true === class_exists($possibleClassName)) {
                    $className = $possibleClassName;
                }
            }
        }

        // If we are on TYPO3 9.3 or above, the old validator ID is no longer possible to use and we must use the new one.
        $fluidCoreVersion = ExtensionManagementUtility::getExtensionVersion('fluid');
        if (version_compare($fluidCoreVersion, 9.3, '>=')) {
            $annotationName = 'Extbase\\Validate';
        } else {
            $annotationName = 'validate';
        }
        $annotations = static::$staticReflectionService->getPropertyTagValues($className, $property, $annotationName);
        if (empty($annotations) && $annotationName === 'validate' && version_compare($fluidCoreVersion, 9.1, '>=')) {
            // We tried looking for the legacy validator name but found none. Retry with the new way. We have to do this
            // as a retry, because we cannot assume that any site using TYPO3 9.1+ will also be using the modern
            // annotations. Hence we cannot change the validator name until we've also looked for the legacy ones (which
            // will take priority if found).
            $annotationName = 'Extbase\\Validate';
            $annotations = static::$staticReflectionService->getPropertyTagValues($className, $property, $annotationName);
        }
        return (count($annotations) && (!$validatorName || in_array($validatorName, $annotations)));
    }
}
