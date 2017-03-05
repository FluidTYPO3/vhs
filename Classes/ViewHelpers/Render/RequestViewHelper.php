<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
class RequestViewHelper extends AbstractRenderViewHelper implements CompilableInterface
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
        $this->registerArgument('vendorName', 'string', 'Vendor name scope to use in request');
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
        $arguments = is_array($arguments['arguments']) ? $arguments['arguments'] : null;
        $configurationManager = static::getConfigurationManager();
        $objectManager = static::getObjectManager();
        $contentObjectBackup = $configurationManager->getContentObject();
        $request = $renderingContext->getControllerContext()->getRequest();
        $configurationBackup = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            $request->getControllerExtensionName(),
            $request->getPluginName()
        );

        $temporaryContentObject = new ContentObjectRenderer();
        /** @var Request $request */
        $request = $objectManager->get(static::$requestType);
        $request->setControllerActionName($action);
        $request->setControllerName($controller);
        $request->setPluginName($pluginName);
        $request->setControllerExtensionName($extensionName);
        if ($arguments !== null) {
            $request->setArguments($arguments);
        }
        $request->setControllerVendorName($vendorName);

        try {
            /** @var ResponseInterface $response */
            $response = $objectManager->get(static::$responseType);
            $configurationManager->setContentObject($temporaryContentObject);
            $configurationManager->setConfiguration(
                $configurationManager->getConfiguration(
                    ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                    $extensionName,
                    $pluginName
                )
            );
            static::getDispatcher()->dispatch($request, $response);
            $configurationManager->setContentObject($contentObjectBackup);
            if (true === isset($configurationBackup)) {
                $configurationManager->setConfiguration($configurationBackup);
            }
            return $response;
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
}
