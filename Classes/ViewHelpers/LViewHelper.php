<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
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
 * ```
 * <v:l>some.label</v:l>
 * <v:l key="some.label" />
 * <v:l arguments="{0: 'foo', 1: 'bar'}">some.label</v:l>
 * ```
 */
class LViewHelper extends AbstractViewHelper
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

    public function initializeArguments(): void
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
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var RenderingContext $renderingContext */
        /** @var string|null $default */
        $default = $arguments['default'];
        $htmlEscape = (boolean) $arguments['htmlEscape'];
        /** @var string|null $extensionName */
        $extensionName = $arguments['extensionName'];
        /** @var array|null $translationArguments */
        $translationArguments = $arguments['arguments'];
        /** @var string $id */
        $id = $renderChildrenClosure();
        if (empty($default)) {
            $default = $id;
        }
        if (empty($extensionName)) {
            $extensionName = RequestResolver::resolveRequestFromRenderingContext($renderingContext)
                ->getControllerExtensionName();
        }
        /** @var string|null $value */
        $value = LocalizationUtility::translate((string) $id, $extensionName, $translationArguments);
        if (empty($value)) {
            $value = $default;
            if (!empty($translationArguments)) {
                $value = vsprintf($value, $translationArguments);
            }
        } elseif ($htmlEscape) {
            $value = htmlspecialchars((string) $value);
        }
        return $value;
    }
}
