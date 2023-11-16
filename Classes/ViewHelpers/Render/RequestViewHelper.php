<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\DispatcherProxy;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ### Render: Request
 *
 * Renders a sub-request to the desired Extension, Plugin,
 * Controller and action with the desired arguments.
 *
 * Note: arguments must not be wrapped with the prefix used
 * in GET/POST parameters but must be provided as if the
 * arguments were sent directly to the Controller action.
 */
class RequestViewHelper extends AbstractRenderViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var class-string
     */
    protected static string $requestType = Request::class;

    /**
     * @var class-string
     */
    protected static string $responseType = Response::class;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('action', 'string', 'Controller action to call in request');
        $this->registerArgument('controller', 'string', 'Controller name to call in request');
        $this->registerArgument('extensionName', 'string', 'Extension name scope to use in request');
        $this->registerArgument(
            'vendorName',
            'string',
            'Vendor name scope to use in request. WARNING: only applies to TYPO3 versions below 10.4'
        );
        $this->registerArgument('pluginName', 'string', 'Plugin name scope to use in request');
        $this->registerArgument('arguments', 'array', 'Arguments to use in request');
    }

    /**
     * @return string|ResponseInterface
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var RenderingContext $renderingContext */
        /** @var string|null $action */
        $action = $arguments['action'];
        /** @var string|null $controller */
        $controller = $arguments['controller'];
        /** @var string|null $extensionName */
        $extensionName = $arguments['extensionName'];
        /** @var string|null $pluginName */
        $pluginName = $arguments['pluginName'];
        $requestArguments = is_array($arguments['arguments']) ? $arguments['arguments'] : [];
        $configurationManager = static::getConfigurationManager();
        /** @var ContentObjectRenderer $contentObjectBackup */
        $contentObjectBackup = $configurationManager->getContentObject();
        $configurationBackup = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        /** @var ContentObjectRenderer $temporaryContentObject */
        $temporaryContentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        try {
            $targetConfiguration = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                $extensionName,
                $pluginName
            );

            /** @var ResponseInterface|\TYPO3\CMS\Core\Http\Response $response */
            $response = GeneralUtility::makeInstance(
                class_exists(static::$responseType)
                    ? static::$responseType
                    : \TYPO3\CMS\Core\Http\Response::class
            );
            $configurationManager->setContentObject($temporaryContentObject);
            $configurationManager->setConfiguration($targetConfiguration);

            $request = self::loadDefaultValues(
                $renderingContext,
                $extensionName,
                $pluginName,
                $controller,
                $action,
                $requestArguments
            );

            /** @var ResponseInterface|null $possibleResponse */
            $possibleResponse = static::getDispatcher()->dispatch(
                $request instanceof RequestInterface ? $request : new \TYPO3\CMS\Extbase\Mvc\Request($request),
                $response instanceof Response ? $response : null
            );
            if ($possibleResponse) {
                $response = $possibleResponse;
            }
            $configurationManager->setContentObject($contentObjectBackup);
            $configurationManager->setConfiguration($configurationBackup);
            if (method_exists($response, 'getBody')) {
                $response->getBody()->rewind();
                return $response->getBody()->getContents();
            }
            if (method_exists($response, 'getContent')) {
                return $response->getContent();
            }
            return '';
        } catch (\Exception $error) {
            if (!$arguments['graceful']) {
                throw $error;
            }
            /** @var string|null $onError */
            $onError = $arguments['onError'];
            if ($onError !== null) {
                return sprintf($onError, $error->getMessage(), $error->getCode());
            }
            return $error->getMessage() . ' (' . $error->getCode() . ')';
        }
    }

    protected static function getDispatcher(): DispatcherProxy
    {
        /** @var DispatcherProxy $dispatcher */
        $dispatcher = GeneralUtility::makeInstance(DispatcherProxy::class);
        return $dispatcher;
    }

    protected static function getConfigurationManager(): ConfigurationManagerInterface
    {
        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        return $configurationManager;
    }

    /**
     * @return RequestInterface|ServerRequestInterface
     * @see \TYPO3\CMS\Extbase\Core\Bootstrap::initializeConfiguration
     */
    protected static function loadDefaultValues(
        RenderingContextInterface $renderingContext,
        ?string $extensionName,
        ?string $pluginName,
        ?string $controllerName,
        ?string $actionName,
        array $arguments
    ) {
        $configurationManager = static::getConfigurationManager();
        $configuration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            $extensionName,
            $pluginName
        );
        /** @var string $defaultActionName */
        $defaultActionName = reset(reset($configuration['controllerConfiguration'])['actions']);

        $firstControllerName = null;
        $controllerAliasToClassMapping = [];
        $controllerClassToAliasMapping = [];
        foreach ($configuration['controllerConfiguration'] as $controllerClassName => $controllerConfiguration) {
            $firstControllerName = $firstControllerName ?? $controllerClassName;
            $controllerAliasToClassMapping[$controllerConfiguration['alias']] = $controllerConfiguration['className'];
            $controllerClassToAliasMapping[$controllerConfiguration['className']] = $controllerConfiguration['alias'];
        }

        if (class_exists(ExtbaseRequestParameters::class)) {
            /** @var ExtbaseRequestParameters $parameters */
            $parameters = GeneralUtility::makeInstance(ExtbaseRequestParameters::class);

            $parameters->setControllerAliasToClassNameMapping($controllerAliasToClassMapping);
            if ($pluginName !== null) {
                $parameters->setPluginName($pluginName);
            }
            if ($extensionName !== null) {
                $parameters->setControllerExtensionName($extensionName);
            }
            $parameters->setControllerName($controllerName ?? $controllerClassToAliasMapping[$firstControllerName]);
            $parameters->setControllerActionName($actionName ?? $defaultActionName);

            if (!empty($configuration['format'])) {
                $parameters->setFormat($configuration['format']);
            }

            foreach ($arguments as $argumentName => $argumentValue) {
                $parameters->setArgument($argumentName, $argumentValue);
            }

            return $GLOBALS['TYPO3_REQUEST']->withAttribute('extbase', $parameters);
        }

        $request = RequestResolver::resolveRequestFromRenderingContext($renderingContext);

        if (method_exists($request, 'setControllerAliasToClassNameMapping')) {
            $request->setControllerAliasToClassNameMapping($controllerAliasToClassMapping);
        }

        if ($pluginName !== null && method_exists($request, 'setPluginName')) {
            $request->setPluginName($pluginName);
        }

        if ($extensionName !== null && method_exists($request, 'setControllerExtensionName')) {
            $request->setControllerExtensionName($extensionName);
        }

        if (method_exists($request, 'setControllerName')) {
            $request->setControllerName($controllerName ?? $controllerClassToAliasMapping[$firstControllerName]);
        }

        if (method_exists($request, 'setControllerActionName')) {
            $request->setControllerActionName($actionName ?? $defaultActionName);
        }

        return $request;
    }
}
