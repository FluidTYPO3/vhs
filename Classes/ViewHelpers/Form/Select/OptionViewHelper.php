<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Form\Select;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Form\SelectViewHelper;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * Option ViewHelper to use under vhs:form.select.
 */
class OptionViewHelper extends AbstractFormFieldViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'option';

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument(
            'selected',
            'boolean',
            'Set to TRUE to mark field as selected; otherwise detected from field value'
        );
    }

    /**
     * @throws \RuntimeException
     * @return string
     */
    public function render()
    {
        if (!$this->viewHelperVariableContainer->exists(SelectViewHelper::class, 'options')) {
            throw new \RuntimeException(
                'Options can only be added inside select tags, optionally inside optgroup tag(s) inside the select tag',
                1313937196
            );
        }
        if (true === (boolean) $this->arguments['selected']) {
            $selected = 'selected';
        } elseif (true === $this->viewHelperVariableContainer->exists(SelectViewHelper::class, 'value')) {
            $value = $this->viewHelperVariableContainer->get(SelectViewHelper::class, 'value');
            if (false === is_object($this->arguments['value']) && false === is_array($this->arguments['value'])) {
                if (true === is_array($value)) {
                    $selected = true === in_array($this->arguments['value'], $value) ? 'selected' : '';
                } else if (true === ($value instanceof ObjectStorage) && true === is_numeric($this->arguments['value'])) {
                    // Requires that the option values are UIDs of objects in ObjectStorage
                    foreach ($value as $object) {
                        if($object->getUid() === (integer) $this->arguments['value']) {
                            $selected = 'selected';
                            break;
                        }
                    }
                } else {
                    $selected = (string) $this->arguments['value'] == (string) $value ? 'selected' : '';
                }
            }
        }
        $tagContent = $this->renderChildren();
        $options = $this->viewHelperVariableContainer->get(SelectViewHelper::class, 'options');
        $options[$tagContent] = $this->arguments['value'];
        $this->viewHelperVariableContainer->addOrUpdate(SelectViewHelper::class, 'options', $options);
        if (false === empty($selected)) {
            $this->tag->addAttribute('selected', 'selected');
        } else {
            $this->tag->removeAttribute('selected');
        }
        $this->tag->setContent($tagContent);
        if (true === isset($this->arguments['value'])) {
            $this->tag->addAttribute('value', $this->arguments['value']);
        }
        return $this->tag->render();
    }
}
