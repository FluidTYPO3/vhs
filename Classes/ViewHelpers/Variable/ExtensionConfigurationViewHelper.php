<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ### ExtConf ViewHelper
 *
 * Reads settings from ext_conf_template.txt
 *
 * ### Examples
 *
 * ```
 * {v:variable.extensionConfiguration(extensionKey:'foo',path:'bar.baz')}
 * ```
 *
 * Returns setting `bar.baz` from extension 'foo' located in `ext_conf_template.txt`.
 */
class ExtensionConfigurationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var array
     */
    protected static $configurations = [];

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'extensionKey',
            'string',
            'Extension key (lowercase_underscored format) to read configuration from'
        );
        $this->registerArgument(
            'path',
            'string',
            'Configuration path to read - if NULL, returns all configuration as array'
        );
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
        /** @var string|null $extensionKey */
        $extensionKey = $arguments['extensionKey'];
        /** @var string $path */
        $path = $arguments['path'];

        if (null === $extensionKey) {
            $extensionName = RequestResolver::resolveRequestFromRenderingContext($renderingContext)
                ->getControllerExtensionName();
            $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
        }

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey]) &&
            !isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey])) {
            return null;
        } elseif (!array_key_exists($extensionKey, static::$configurations)) {
            if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey])) {
                $extConf = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$extensionKey];
            } elseif (is_string($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey])) {
                $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]);
            } else {
                $extConf = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey];
            }
            static::$configurations[$extensionKey] = GeneralUtility::removeDotsFromTS($extConf);
        }

        if (!$path) {
            return static::$configurations[$extensionKey];
        }

        return ObjectAccess::getPropertyPath(static::$configurations[$extensionKey], $path);
    }
}
