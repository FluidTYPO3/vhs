<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * If content is empty use alternative text (can also be LLL:labelname shortcut or LLL:EXT: file paths).
 */
class OrViewHelper extends AbstractViewHelper
{

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('alternative', 'mixed', 'Alternative if content is empty, can use LLL: shortcut');
        $this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
        $this->registerArgument('extensionName', 'string', 'UpperCamelCase extension name without vendor prefix');
    }

    /**
     * @param $content string
     * @return string
     */
    public function render($content = null)
    {
        if (null === $content) {
            $content = $this->renderChildren();
        }
        if (true === empty($content)) {
            $content = $this->getAlternativeValue();
        }
        return $content;
    }

    /**
     * @return mixed
     */
    protected function getAlternativeValue()
    {
        $alternative = $this->arguments['alternative'];
        $arguments = (array) $this->arguments['arguments'];
        if (0 === count($arguments)) {
            $arguments = null;
        }
        if (0 === strpos($alternative, 'LLL:EXT:')) {
            $alternative = LocalizationUtility::translate($alternative, null, $arguments);
        } elseif (0 === strpos($alternative, 'LLL:')) {
            $extensionName = $this->arguments['extensionName'];
            if (null === $extensionName) {
                $extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
            }
            $translated = LocalizationUtility::translate(substr($alternative, 4), $extensionName, $arguments);
            if (null !== $translated) {
                $alternative = $translated;
            }
        }
        return null !== $arguments && false === empty($alternative) ? vsprintf($alternative, $arguments) : $alternative;
    }
}
