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
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
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
    protected static $staticReflectionService;

    /**
     * Initialize
     *
     * @return void
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
        if (!is_array($arguments)) {
            return false;
        }

        /** @var ReflectionService $reflectionService */
        $reflectionService = GeneralUtility::makeInstance(ReflectionService::class);

        $property = $arguments['property'];
        $validatorName = $arguments['validatorName'] ?? null;
        $object = $arguments['object'] ?? null;

        $path = null;
        if (strpos($property, '.') !== false) {
            $parts = explode('.', $property);
            $property = array_pop($parts);
            $path = implode('.', $parts);
            $object = ObjectAccess::getPropertyPath($object, $path);
        }

        if (!is_object($object)) {
            return false;
        }

        if (!method_exists($reflectionService, 'getPropertyTagValues')) {
            // TYPO3 version no longer contains the raw property tag value extractor. Instead, we can check for a given
            // validator by extracting the validators and analysing those.
            $validators = $reflectionService->getClassSchema($object)->getProperty($property)->getValidators();
            foreach ($validators as $validatorConfiguration) {
                if ($validatorConfiguration['name'] === $validatorName) {
                    return true;
                }
            }
            return false;
        }

        $className = get_class($object);
        $annotations = $reflectionService->getPropertyTagValues($className, $property, 'validate');
        if (empty($annotations)) {
            // We tried looking for the legacy validator name but found none. Retry with the new way. We have to do this
            // as a retry, because we cannot assume that any site using TYPO3 9.1+ will also be using the modern
            // annotations. Hence we cannot change the validator name until we've also looked for the legacy ones (which
            // will take priority if found).
            $annotations = $reflectionService->getPropertyTagValues($className, $property, 'Extbase\\Validate');
        }
        return (count($annotations) && (!$validatorName || in_array($validatorName, $annotations)));
    }
}
