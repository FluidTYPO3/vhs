<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * ### Page: Deferred menu rendering ViewHelper
 *
 * Place this ViewHelper inside any other ViewHelper which
 * has been configured with the `deferred` attribute set to
 * TRUE - this will cause the output of the parent to only
 * contain the content of this ViewHelper.
 */
class DeferredViewHelper extends AbstractMenuViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument(
            'as',
            'string',
            'If used, stores the menu pages as an array in a variable named according to this value and renders ' .
            'the tag content - which means automatic rendering is disabled if this attribute is used'
        );
    }

    /**
     * @return NULL|string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception
     */
    public function render()
    {
        $as = $this->arguments['as'];
        if (false === $this->viewHelperVariableContainer->exists(AbstractMenuViewHelper::class, 'deferredArray')) {
            return null;
        }
        if (false === $this->viewHelperVariableContainer->exists(AbstractMenuViewHelper::class, 'deferredString')) {
            return null;
        }
        if (null === $as) {
            $content = $this->viewHelperVariableContainer->get(AbstractMenuViewHelper::class, 'deferredString');
            $this->unsetDeferredVariableStorage();
            return $content;
        } elseif (true === empty($as)) {
            throw new Exception('An "as" attribute was used but was empty - use a proper string value', 1370096373);
        }
        if (true === $this->templateVariableContainer->exists($as)) {
            $backupVariable = $this->templateVariableContainer->get($as);
            $this->templateVariableContainer->remove($as);
        }
        $this->templateVariableContainer->add(
            $as,
            $this->viewHelperVariableContainer->get(AbstractMenuViewHelper::class, 'deferredArray')
        );
        $this->unsetDeferredVariableStorage();
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($as);
        if (true === isset($backupVariable)) {
            $this->templateVariableContainer->add($as, $backupVariable);
        }

        return $content;
    }
}
