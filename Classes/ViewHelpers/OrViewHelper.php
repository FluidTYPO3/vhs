<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * If content is empty use alternative text (can also be LLL:labelname shortcut or LLL:EXT: file paths).
 */
class OrViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'mixed', 'Input to either use, if not empty');
        $this->registerArgument('alternative', 'mixed', 'Alternative if content is empty, can use LLL: shortcut');
        $this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
        $this->registerArgument('extensionName', 'string', 'UpperCamelCase extension name without vendor prefix');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $content = $renderChildrenClosure() ?: static::getAlternativeValue($arguments, $renderingContext);
        return $content;
    }

    /**
     * @return mixed
     */
    protected static function getAlternativeValue(array $arguments, RenderingContextInterface $renderingContext)
    {
        $alternative = $arguments['alternative'];
        $arguments = (array) $arguments['arguments'];
        if (0 === count($arguments)) {
            $arguments = null;
        }
        if (0 === strpos($alternative, 'LLL:EXT:')) {
            $alternative = LocalizationUtility::translate($alternative, null, $arguments);
        } elseif (0 === strpos($alternative, 'LLL:')) {
            $extensionName = $arguments['extensionName'];
            if (null === $extensionName) {
                $extensionName = $renderingContext->getControllerContext()->getRequest()->getControllerExtensionName();
            }
            $translated = LocalizationUtility::translate(substr($alternative, 4), $extensionName ?: 'core', $arguments);
            if (null !== $translated) {
                $alternative = $translated;
            }
        }
        return null !== $arguments && false === empty($alternative) ? vsprintf($alternative, $arguments) : $alternative;
    }
}
