<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

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
     * @return string
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function render()
    {
        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();
        $as = $this->arguments['as'];
        if (!$viewHelperVariableContainer->exists(AbstractMenuViewHelper::class, 'deferredArray')) {
            return '';
        }
        if (!$viewHelperVariableContainer->exists(AbstractMenuViewHelper::class, 'deferredString')) {
            return '';
        }
        if (null === $as) {
            $content = $viewHelperVariableContainer->get(AbstractMenuViewHelper::class, 'deferredString');
            $this->unsetDeferredVariableStorage();
            return is_scalar($content) ? (string) $content : '';
        } elseif (true === empty($as)) {
            throw new Exception('An "as" attribute was used but was empty - use a proper string value', 1370096373);
        }
        if (true === $this->renderingContext->getVariableProvider()->exists($as)) {
            $backupVariable = $this->renderingContext->getVariableProvider()->get($as);
            $this->renderingContext->getVariableProvider()->remove($as);
        }
        $this->renderingContext->getVariableProvider()->add(
            $as,
            $viewHelperVariableContainer->get(AbstractMenuViewHelper::class, 'deferredArray')
        );
        $this->unsetDeferredVariableStorage();
        $content = $this->renderChildren();
        $this->renderingContext->getVariableProvider()->remove($as);
        if (true === isset($backupVariable)) {
            $this->renderingContext->getVariableProvider()->add($as, $backupVariable);
        }

        return is_scalar($content) ? (string) $content : '';
    }
}
