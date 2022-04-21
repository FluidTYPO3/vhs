<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
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
     * @var string
     */
    protected static $requestType = Request::class;

    /**
     * @var string
     */
    protected static $responseType = Response::class;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('action', 'string', 'Controller action to call in request');
        $this->registerArgument('controller', 'string', 'Controller name to call in request');
        $this->registerArgument('extensionName', 'string', 'Extension name scope to use in request');
        $this->registerArgument('vendorName', 'string', 'Vendor name scope to use in request. WARNING: only applies to TYPO3 versions below 10.4');
        $this->registerArgument('pluginName', 'string', 'Plugin name scope to use in request');
        $this->registerArgument('arguments', 'array', 'Arguments to use in request');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string|ResponseInterface
     * @throws \Exception
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $action = $arguments['action'];
        $controller = $arguments['controller'];
        $extensionName = $arguments['extensionName'];
        $pluginName = $arguments['pluginName'];
        $vendorName = $arguments['vendorName'];
        $requestArguments = is_array($arguments['arguments']) ? $arguments['arguments'] : [];
        $configurationManager = static::getConfigurationManager();
        $objectManager = static::getObjectManager();
        $contentObjectBackup = $configurationManager->getContentObject();
        $request = $renderingContext->getControllerContext()->getRequest();
        $configurationBackup = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            $request->getControllerExtensionName(),
            $request->getPluginName()
        );

        $temporaryContentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        try {
            $targetConfiguration = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                $extensionName,
                $pluginName
            );

            /** @var ResponseInterface|\TYPO3\CMS\Core\Http\Response $response */
            $response = $objectManager->get(class_exists(static::$responseType) ? static::$responseType : \TYPO3\CMS\Core\Http\Response::class);
            $configurationManager->setContentObject($temporaryContentObject);
            $configurationManager->setConfiguration($targetConfiguration);

            if (version_compare(TYPO3_version, 10.0, '<')) {
                /** @var Request $request */
                $request = $objectManager->get(static::$requestType);
                $request->setControllerActionName($action ?? reset(reset($targetConfiguration['controllerConfiguration'])['actions']));
                $request->setControllerName($controller ?? key($targetConfiguration['controllerConfiguration']));
                $request->setPluginName($pluginName);
                $request->setControllerExtensionName($extensionName);
                if (!empty($requestArguments)) {
                    $request->setArguments($requestArguments);
                }
                $request->setControllerVendorName($vendorName);
            } else {
                $request = self::loadDefaultValues($extensionName, $pluginName, $controller, $action, $requestArguments);
            }
            $possibleResponse = static::getDispatcher()->dispatch($request, $response);
            if ($possibleResponse) {
                $response = $possibleResponse;
            }
            $configurationManager->setContentObject($contentObjectBackup);
            if (true === isset($configurationBackup)) {
                $configurationManager->setConfiguration($configurationBackup);
            }
            return $response instanceof ResponseInterface ? (string) $response : $response->getBody()->getContents();
        } catch (\Exception $error) {
            if (false === (boolean) $arguments['graceful']) {
                throw $error;
            }
            if (false === empty($arguments['onError'])) {
                return sprintf($arguments['onError'], [$error->getMessage()], $error->getCode());
            }
            return $error->getMessage() . ' (' . $error->getCode() . ')';
        }
    }

    /**
     * @return ObjectManagerInterface
     */
    protected static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return Dispatcher
     */
    protected static function getDispatcher()
    {
        return static::getObjectManager()->get(Dispatcher::class);
    }

    /**
     * @return ConfigurationManagerInterface
     */
    protected static function getConfigurationManager()
    {
        return static::getObjectManager()->get(ConfigurationManagerInterface::class);
    }

    /**
     * @param string $extensionName
     * @param string $pluginName
     * @param string|null $controllerName
     * @param string|null $actionName
     * @param array $parameters
     * @throws MvcException\InvalidActionNameException
     * @throws MvcException\InvalidArgumentNameException
     * @throws MvcException\InvalidControllerNameException
     * @throws MvcException\InvalidExtensionNameException
     * @see \TYPO3\CMS\Extbase\Core\Bootstrap::initializeConfiguration
     */
    protected static function loadDefaultValues($extensionName, $pluginName, $controllerName, $actionName, $parameters)
    {
        $configurationManager = static::getConfigurationManager();
        $configuration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, $extensionName, $pluginName);
        $defaultActionName = reset(reset($configuration['controllerConfiguration'])['actions']);

        $controllerAliasToClassMapping = [];
        foreach ($configuration['controllerConfiguration'] as $controllerClassName => $controllerConfiguration) {
            $controllerAliasToClassMapping[$controllerConfiguration['alias']] = $controllerConfiguration['className'];
            $controllerClassToAliasMapping[$controllerConfiguration['className']] = $controllerConfiguration['alias'];
        }

        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Request|\TYPO3\CMS\Extbase\Mvc\Request $request */
        $request = static::getObjectManager()->get(class_exists(Request::class) ? Request::class : \TYPO3\CMS\Extbase\Mvc\Request::class);
        $request->setPluginName($pluginName);
        $request->setControllerExtensionName($extensionName);
        $request->setControllerAliasToClassNameMapping($controllerAliasToClassMapping);
        $request->setControllerName($controllerName ?? $controllerClassToAliasMapping[$controllerClassName]);
        $request->setControllerActionName($actionName ?? $defaultActionName);
        if (!empty($configuration['format'])) {
            $request->setFormat($configuration['format']);
        }

        foreach ($parameters as $argumentName => $argumentValue) {
            $request->setArgument($argumentName, $argumentValue);
        }

        return $request;
    }
}
