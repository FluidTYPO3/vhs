<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * Select ViewHelper (with support for Optgroup and Option subnodes).
 */
class SelectViewHelper extends AbstractFormFieldViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'select';

    /**
     * @var mixed
     */
    protected $selectedValue = null;

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('size', 'string', 'Size of input field');
        $this->registerTagAttribute(
            'disabled',
            'string',
            'Specifies that the input element should be disabled when the page loads'
        );
        $this->registerArgument('multiple', 'boolean', 'if set, multiple select field', false, false);
        $this->registerArgument(
            'options',
            'array',
            'Associative array with internal IDs as key, and the values are displayed in the select box'
        );
        $this->registerArgument(
            'optionValueField',
            'string',
            'If specified, will call the appropriate getter on each object to determine the value.'
        );
        $this->registerArgument(
            'optionLabelField',
            'string',
            'If specified, will call the appropriate getter on each object to determine the label.'
        );
        $this->registerArgument(
            'sortByOptionLabel',
            'boolean',
            'If true, List will be sorted by label.',
            false,
            false
        );
        $this->registerArgument(
            'selectAllByDefault',
            'boolean',
            'If specified options are selected if none was set before.',
            false,
            false
        );
        $this->registerArgument(
            'errorClass',
            'string',
            'CSS class to set if there are errors for this view helper',
            false,
            'f3-form-error'
        );
    }

    /**
     * Render the tag.
     *
     * @return string rendered tag.
     * @api
     */
    public function render()
    {
        $name = $this->getName();
        if (true === (boolean) $this->arguments['multiple']) {
            $name .= '[]';
        }

        $this->tag->addAttribute('name', $name);

        if (true === isset($this->arguments['options']) && false === empty($this->arguments['options'])) {
            $options = $this->getOptions();
            if (true === empty($options)) {
                $options = ['' => ''];
            }
            $this->tag->setContent($this->renderOptionTags($options));
        } else {
            $this->viewHelperVariableContainer->add(SelectViewHelper::class, 'options', []);
            $this->viewHelperVariableContainer->add(SelectViewHelper::class, 'value', $this->getValue());
            $tagContent = $this->renderChildren();
            $options = $this->viewHelperVariableContainer->get(SelectViewHelper::class, 'options');
            $this->tag->setContent($tagContent);
            $this->viewHelperVariableContainer->remove(SelectViewHelper::class, 'options');
            if (true === $this->viewHelperVariableContainer->exists(SelectViewHelper::class, 'value')) {
                $this->viewHelperVariableContainer->remove(SelectViewHelper::class, 'value');
            }
        }

        $this->setErrorClassAttribute();

        $content = '';

        // register field name for token generation.
        // in case it is a multi-select, we need to register the field name
        // as often as there are elements in the box
        if (true === (boolean) $this->arguments['multiple']) {
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
     * Get the value of this form element.
     * Either returns arguments['value'], or the correct value for Object Access.
     *
     * @param boolean $convertObjects whether or not to convert objects to identifiers
     * @return mixed Value
     */
    protected function getValue($convertObjects = true)
    {
        $value = null;

        if ($this->hasArgument('value')) {
            $value = $this->arguments['value'];
        } elseif ($this->isObjectAccessorMode()) {
            if ($this->hasMappingErrorOccurred()) {
                $value = $this->getLastSubmittedFormData();
            } else {
                $value = $this->getPropertyValue();
            }
            $this->addAdditionalIdentityPropertiesIfNeeded();
        }

        if ($convertObjects) {
            $value = $this->convertToPlainValue($value);
        }
        return $value;
    }

    /**
     * Render the option tags.
     *
     * @param array $options the options for the form.
     * @return string rendered tags.
     */
    protected function renderOptionTags($options)
    {
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
     * @return array
     * @throws Exception
     */
    protected function getOptions()
    {
        if (!is_array($this->arguments['options']) && !$this->arguments['options'] instanceof \Traversable) {
            return [];
        }
        $options = [];
        $optionsArgument = $this->arguments['options'];
        foreach ($optionsArgument as $key => $value) {
            if (is_object($value)) {
                if (isset($this->arguments['optionValueField']) && !empty($this->arguments['optionValueField'])) {
                    $key = ObjectAccess::getProperty($value, $this->arguments['optionValueField']);
                    if (true === is_object($key)) {
                        if (true === method_exists($key, '__toString')) {
                            $key = (string) $key;
                        } else {
                            ErrorUtility::throwViewHelperException(
                                'Identifying value for object of class "' . get_class($value) . '" was an object.',
                                1247827428
                            );
                        }
                    }
                } elseif (null !== $this->persistenceManager->getBackend()->getIdentifierByObject($value)) {
                    $key = $this->persistenceManager->getBackend()->getIdentifierByObject($value);
                } elseif (true === method_exists($value, '__toString')) {
                    $key = (string) $value;
                } else {
                    ErrorUtility::throwViewHelperException(
                        'No identifying value for object of class "' . get_class($value) . '" found.',
                        1247826696
                    );
                }

                if (isset($this->arguments['optionLabelField']) && !empty($this->arguments['optionLabelField'])) {
                    $value = ObjectAccess::getProperty($value, $this->arguments['optionLabelField']);
                    if (true === is_object($value)) {
                        if (true === method_exists($value, '__toString')) {
                            $value = (string) $value;
                        } else {
                            ErrorUtility::throwViewHelperException(
                                'Label value for object of class "' . get_class($value) . '" was an object without ' .
                                'a __toString() method.',
                                1247827553
                            );
                        }
                    }
                } elseif (true === method_exists($value, '__toString')) {
                    $value = (string) $value;
                } elseif (null !== $this->persistenceManager->getBackend()->getIdentifierByObject($value)) {
                    $value = $this->persistenceManager->getBackend()->getIdentifierByObject($value);
                }
            }
            $options[$key] = $value;
        }
        if (isset($this->arguments['sortByOptionLabel']) && !empty($this->arguments['sortByOptionLabel'])) {
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
    protected function isSelected($value)
    {
        $selectedValue = $this->getSelectedValue();
        if ($value === $selectedValue || (string) $value === $selectedValue) {
            return true;
        }
        if (true === isset($this->arguments['multiple']) && false === empty($this->arguments['multiple'])) {
            if (null === $selectedValue && true === (boolean) $this->arguments['selectAllByDefault']) {
                return true;
            } elseif (true === is_array($selectedValue) && true === in_array($value, $selectedValue)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieves the selected value(s)
     *
     * @return mixed value string or an array of strings
     */
    protected function getSelectedValue()
    {
        $value = $this->getValue();
        if (!isset($this->arguments['optionValueField']) || empty($this->arguments['optionValueField'])) {
            return $value;
        }
        if (!is_array($value) && !$value instanceof \Iterator) {
            if (is_object($value)) {
                return ObjectAccess::getProperty($value, $this->arguments['optionValueField']);
            } else {
                return $value;
            }
        }
        $selectedValues = [];
        foreach ($value as $selectedValueElement) {
            if (is_object($selectedValueElement)) {
                $selectedValues[] = ObjectAccess::getProperty(
                    $selectedValueElement,
                    $this->arguments['optionValueField']
                );
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
    protected function renderOptionTag($value, $label, $isSelected)
    {
        $output = '<option value="' . htmlspecialchars($value) . '"';
        if (true === (boolean) $isSelected) {
            $output .= ' selected="selected"';
        }
        $output .= '>' . htmlspecialchars($label) . '</option>';

        return $output;
    }
}
