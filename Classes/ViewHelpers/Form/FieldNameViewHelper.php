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
use TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper;

/**
 * Form Field Name View Helper
 *
 * This viewhelper returns the properly prefixed name of the given
 * form field and generates the corresponding HMAC to allow posting
 * of dynamically added fields.
 */
class FieldNameViewHelper extends AbstractViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @param PersistenceManagerInterface $persistenceManager
     */
    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('name', 'string', 'Name of the form field to generate the HMAC for.');
        $this->registerArgument(
            'property',
            'string',
            'Name of object property. If used in conjunction with <f:form object="...">, "name" argument will ' .
            'be ignored.'
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($this->isObjectAccessorMode()) {
            $formObjectName = $this->viewHelperVariableContainer->get(FormViewHelper::class, 'formObjectName');
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
        if (null === $name || '' === $name) {
            return '';
        }
        if (!$this->viewHelperVariableContainer->exists(FormViewHelper::class, 'fieldNamePrefix')) {
            return $name;
        }
        $fieldNamePrefix = (string) $this->viewHelperVariableContainer->get(FormViewHelper::class, 'fieldNamePrefix');
        if ('' === $fieldNamePrefix) {
            return $name;
        }
        $fieldNameSegments = explode('[', $name, 2);
        $name = $fieldNamePrefix . '[' . $fieldNameSegments[0] . ']';
        if (1 < count($fieldNameSegments)) {
            $name .= '[' . $fieldNameSegments[1];
        }
        if ($this->viewHelperVariableContainer->exists(FormViewHelper::class, 'formFieldNames')) {
            $formFieldNames = $this->viewHelperVariableContainer->get(FormViewHelper::class, 'formFieldNames');
        } else {
            $formFieldNames = [];
        }
        $formFieldNames[] = $name;
        $this->viewHelperVariableContainer->addOrUpdate(FormViewHelper::class, 'formFieldNames', $formFieldNames);
        return $name;
    }

    /**
     * @return boolean
     */
    protected function isObjectAccessorMode()
    {
        return (
            $this->hasArgument('property')
            && $this->viewHelperVariableContainer->exists(FormViewHelper::class, 'formObjectName')
        );
    }
}
