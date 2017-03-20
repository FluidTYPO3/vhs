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
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * ### L (localisation) ViewHelper
 *
 * An extremely shortened and much more dev-friendly
 * alternative to f:translate. Automatically outputs
 * the name of the LLL reference if it is not found
 * and the default value is not set, making it much
 * easier to identify missing labels when translating.
 *
 * ### Examples
 *
 *     <v:l>some.label</v:l>
 *     <v:l key="some.label" />
 *     <v:l arguments="{0: 'foo', 1: 'bar'}">some.label</v:l>
 */
class LViewHelper extends AbstractViewHelper implements CompilableInterface
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
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('key', 'string', 'Translation Key');
        $this->registerArgument(
            'default',
            'string',
            'if the given locallang key could not be found, this value is used. If this argument is not set, ' .
            'child nodes will be used to render the default'
        );
        $this->registerArgument(
            'htmlEscape',
            'boolean',
            'TRUE if the result should be htmlescaped. This won\'t have an effect for the default value'
        );
        $this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
        $this->registerArgument('extensionName', 'string', 'UpperCamelCased extension key (for example BlogExample)');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments, 
        \Closure $renderChildrenClosure, 
        RenderingContextInterface $renderingContext
    ) {
        $default = $arguments['default'];
        $htmlEscape = (boolean) $arguments['htmlEscape'];
        $extensionName = $arguments['extensionName'];
        $translationArguments = $arguments['arguments'];
        $id = $renderChildrenClosure();
        if (true === empty($default)) {
            $default = $id;
        }
        if (true === empty($extensionName)) {
            $extensionName = $renderingContext->getControllerContext()->getRequest()->getControllerExtensionName();
        }
        $value = LocalizationUtility::translate($id, $extensionName, $translationArguments);
        if (true === empty($value)) {
            $value = $default;
            if (true === is_array($translationArguments)) {
                $value = vsprintf($value, $translationArguments);
            }
        } elseif (true === $htmlEscape) {
            $value = htmlspecialchars($value);
        }
        return $value;
    }

}
