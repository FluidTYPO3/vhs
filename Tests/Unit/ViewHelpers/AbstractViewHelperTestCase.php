<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyViewHelperNode;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\DummyController;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ErrorHandler\ErrorHandlerInterface;
use TYPO3Fluid\Fluid\Core\ErrorHandler\StandardErrorHandler;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper as FluidAbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\StrictArgumentProcessor;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInvoker;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * AbstractViewHelperTestCase
 */
abstract class AbstractViewHelperTestCase extends AbstractTestCase
{
    protected ?RenderingContextInterface $renderingContext;
    protected ?ViewHelperResolver $viewHelperResolver;
    protected ?ViewHelperInvoker $viewHelperInvoker;
    protected ?ViewHelperVariableContainer $viewHelperVariableContainer;
    protected ?StandardVariableProvider $templateVariableContainer;
    protected ?ControllerContext $controllerContext;
    protected ?ErrorHandlerInterface $errorHandler;
    protected ?TemplateParser $templateParser;
    protected array $templateProcessors = [];
    protected array $expressionTypes = [];
    protected array $defaultArguments = [
        'name' => 'test',
    ];

    protected function setUp(): void
    {
        $extbaseParameters = null;
        if (class_exists(ExtbaseRequestParameters::class)) {
            $extbaseParameters = new ExtbaseRequestParameters(DummyController::class);
        }
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->onlyMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturnMap(
            [
                ['applicationType', null, SystemEnvironmentBuilder::REQUESTTYPE_FE],
                ['extbase', null, $extbaseParameters],
            ]
        );

        $legacyRequestClassName = 'TYPO3\\CMS\\Extbase\\Web\\Request';
        if (class_exists($legacyRequestClassName)) {
            $requestClassName = $legacyRequestClassName;
        } else {
            $requestClassName = \TYPO3\CMS\Extbase\Mvc\Request::class;
        }
        if (!class_exists($requestClassName)) {
            throw new \RuntimeException('Unable to resolve Extbase request class name.', 1780000291);
        }

        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '11.0', '<')) {
            $request = $this->getMockBuilder($requestClassName)
                ->onlyMethods(['getControllerExtensionName', 'getControllerName', 'getControllerActionName'])
                ->setConstructorArgs([DummyController::class])
                ->getMock();
        } else {
            $request = $this->getMockBuilder($requestClassName)
                ->onlyMethods(['getControllerExtensionName', 'getControllerName', 'getControllerActionName'])
                ->setConstructorArgs([$GLOBALS['TYPO3_REQUEST']])
                ->getMock();
        }

        $request->method('getControllerExtensionName')->willReturn('Vhs');
        $request->method('getControllerName')->willReturn('Controller');
        $request->method('getControllerActionName')->willReturn('action');

        $this->viewHelperResolver = $this->getMockBuilder(ViewHelperResolver::class)
            ->addMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewHelperVariableContainer = $this->getMockBuilder(ViewHelperVariableContainer::class)
            ->addMethods(['dummy'])
            ->getMock();
        $this->templateVariableContainer = new StandardVariableProvider();

        $this->viewHelperInvoker = $this->getMockBuilder(ViewHelperInvoker::class)
            ->addMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->renderingContext = $this->getMockBuilder(RenderingContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->errorHandler = new StandardErrorHandler();
        $this->templateParser = new TemplateParser();
        $this->templateParser->setRenderingContext($this->renderingContext);
        $this->renderingContext->method('getViewHelperResolver')->willReturn($this->viewHelperResolver);

        $this->renderingContext->method('getViewHelperVariableContainer')->willReturn(
            $this->viewHelperVariableContainer
        );
        $this->renderingContext->method('getVariableProvider')->willReturn($this->templateVariableContainer);
        $this->renderingContext->method('getViewHelperInvoker')->willReturn($this->viewHelperInvoker);
        $this->renderingContext->method('getErrorHandler')->willReturn($this->errorHandler);
        $this->renderingContext->method('getTemplateParser')->willReturn($this->templateParser);
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')) {
            $this->renderingContext->method('getArgumentProcessor')->willReturn(new StrictArgumentProcessor());
        }
        $this->renderingContext->method('getTemplateProcessors')->willReturn($this->templateProcessors);
        $this->renderingContext->method('getExpressionNodeTypes')->willReturn($this->expressionTypes);

        if (method_exists($this->renderingContext, 'getRequest')) {
            $this->renderingContext->method('getRequest')->willReturn($request);
        } elseif (method_exists($this->renderingContext, 'getControllerContext')) {
            $uriBuilder = $this->getMockBuilder(UriBuilder::class)
                ->onlyMethods(['uriFor', 'buildFrontendUri', 'buildBackendUri', 'build'])
                ->disableOriginalConstructor()
                ->getMock();
            $uriBuilder->method('build')->willReturn('build');
            $uriBuilder->method('uriFor')->willReturn('for');
            $uriBuilder->method('buildFrontendUri')->willReturn('frontend');
            $uriBuilder->method('buildBackendUri')->willReturn('backend');

            $this->controllerContext = $this->getMockBuilder(ControllerContext::class)
                ->onlyMethods(['getRequest', 'getUriBuilder'])
                ->getMock();
            $this->controllerContext->method('getRequest')->willReturn($request);
            $this->controllerContext->method('getUriBuilder')->willReturn($uriBuilder);
            $this->renderingContext->method('getControllerContext')->willReturn($this->controllerContext);
        }

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     */
    public function canCreateViewHelperInstance(): void
    {
        $instance = $this->createInstance();
        self::assertInstanceOf($this->getViewHelperClassName(), $instance);
    }

    /**
     * @test
     */
    public function canPrepareArguments(): void
    {
        $instance = $this->createInstance();
        $arguments = $instance->prepareArguments();
        $this->assertIsArray($arguments);
    }

    /**
     * @return class-string<ViewHelperInterface>
     */
    protected function getViewHelperClassName(): string
    {
        $class = get_class($this);
        $class = str_replace('Tests\\Unit\\', '', $class);
        $className = substr($class, 0, -4);
        if (!is_subclass_of($className, ViewHelperInterface::class)) {
            throw new \RuntimeException('Resolved class name is not a ViewHelper.', 1780000292);
        }
        return $className;
    }

    /**
     * @param mixed $value
     */
    protected function createNode(string $type, $value): NodeInterface
    {
        $className = 'TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\' . $type . 'Node';
        if (!is_subclass_of($className, NodeInterface::class)) {
            throw new \RuntimeException('Resolved class name is not a node.', 1780000293);
        }
        return new $className($value);
    }

    protected function createInstance(): ViewHelperInterface
    {
        $className = $this->getViewHelperClassName();
        if (!is_subclass_of($className, AbstractViewHelper::class)) {
            throw new \RuntimeException('Resolved class name is not an AbstractViewHelper.', 1780000294);
        }
        /** @var class-string<AbstractViewHelper> $className */
        /** @var AbstractViewHelper $instance */
        $instance = $this->getMockBuilder($className)
            ->addMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        if (method_exists($instance, 'injectConfigurationManager')) {
            $cObject = $this->getMockBuilder(ContentObjectRenderer::class)->disableOriginalConstructor()->getMock();
            $cObject->start(['uid' => 123], 'tt_content');

            if (method_exists(ConfigurationManagerInterface::class, 'getContentObject')) {
                /** @var ConfigurationManagerInterface&MockObject $configurationManager */
                $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMock();
                $configurationManager->method('getContentObject')->willReturn($cObject);
            } else {
                /** @var ServerRequestInterface&MockObject $request */
                $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
                $request->method('getAttribute')->willReturn($cObject);

                /** @var ConfigurationManagerInterface&MockObject $configurationManager */
                $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
                    ->onlyMethods(['getConfiguration', 'setConfiguration', 'setRequest'])
                    ->addMethods(['getRequest'])
                    ->getMock();
                $configurationManager->method('getRequest')->willReturn($request);
            }

            $instance->injectConfigurationManager($configurationManager);
        }
        self::assertInstanceOf(RenderingContextInterface::class, $this->renderingContext);
        $instance->setRenderingContext($this->renderingContext);
        return $instance;
    }

    protected function buildViewHelperInstance(
        array $arguments = [],
        array $variables = [],
        ?NodeInterface $childNode = null,
        ?string $extensionName = null,
        ?string $pluginName = null
    ): ViewHelperInterface {
        $instance = $this->createInstance();
        $arguments = $this->buildViewHelperArguments($instance, $arguments);
        $node = $this->createViewHelperNode(
            $instance,
            $arguments,
            $childNode instanceof NodeInterface ? [$childNode] : []
        );
        self::assertInstanceOf(StandardVariableProvider::class, $this->templateVariableContainer);
        $this->templateVariableContainer->setSource($variables);

        $instance->setViewHelperNode($node);
        $instance->setArguments($arguments);

        if (method_exists($instance, 'setChildNodes')) {
            $instance->setChildNodes($node->getChildNodes());
        }

        $legacyTagBasedClassName = 'TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\AbstractTagBasedViewHelper';
        $isLegacyTagBasedViewHelper = class_exists($legacyTagBasedClassName)
            && $instance instanceof $legacyTagBasedClassName;
        if ($instance instanceof FluidAbstractTagBasedViewHelper || $isLegacyTagBasedViewHelper) {
            $tagName = $this->getInaccessiblePropertyValue($instance, 'tagName');
            $tagBuilder = new TagBuilder(is_string($tagName) ? $tagName : '');
            $this->setInaccessiblePropertyValue($instance, 'tag', $tagBuilder);
        }

        return $instance;
    }

    protected function buildViewHelperArguments(ViewHelperInterface $viewHelper, array $arguments): array
    {
        foreach ($viewHelper->prepareArguments() as $argumentName => $argumentDefinition) {
            if (!array_key_exists($argumentName, $arguments)) {
                $arguments[$argumentName] = $argumentDefinition->getDefaultValue();
            }
        }
        return $arguments;
    }

    protected function createRenderingContextWithRequest(ServerRequestInterface $request): RenderingContextInterface
    {
        $methods = [
            'getViewHelperResolver',
            'getViewHelperVariableContainer',
            'getVariableProvider',
            'getViewHelperInvoker',
            'getErrorHandler',
            'getTemplateParser',
            'getTemplateProcessors',
            'getExpressionNodeTypes',
        ];
        if (method_exists(RenderingContext::class, 'getRequest')) {
            $methods[] = 'getRequest';
        }
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')) {
            $methods[] = 'getArgumentProcessor';
        }
        $mockBuilder = $this->getMockBuilder(RenderingContext::class)
            ->onlyMethods($methods)
            ->disableOriginalConstructor();
        if (!method_exists(RenderingContext::class, 'getRequest')) {
            $mockBuilder->addMethods(['getRequest']);
        }
        $renderingContext = $mockBuilder->getMock();
        $renderingContext->method('getRequest')->willReturn($request);
        $renderingContext->method('getViewHelperResolver')->willReturn($this->viewHelperResolver);
        $renderingContext->method('getViewHelperVariableContainer')->willReturn($this->viewHelperVariableContainer);
        $renderingContext->method('getVariableProvider')->willReturn($this->templateVariableContainer);
        $renderingContext->method('getViewHelperInvoker')->willReturn($this->viewHelperInvoker);
        $renderingContext->method('getErrorHandler')->willReturn($this->errorHandler);
        $renderingContext->method('getTemplateParser')->willReturn($this->templateParser);
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')) {
            $renderingContext->method('getArgumentProcessor')->willReturn(new StrictArgumentProcessor());
        }
        $renderingContext->method('getTemplateProcessors')->willReturn($this->templateProcessors);
        $renderingContext->method('getExpressionNodeTypes')->willReturn($this->expressionTypes);

        return $renderingContext;
    }

    protected function createRenderingContextWithoutRequest(): RenderingContextInterface
    {
        $methods = [
            'getViewHelperResolver',
            'getViewHelperVariableContainer',
            'getVariableProvider',
            'getViewHelperInvoker',
            'getErrorHandler',
            'getTemplateParser',
            'getTemplateProcessors',
            'getExpressionNodeTypes',
        ];
        if (method_exists(RenderingContext::class, 'getRequest')) {
            $methods[] = 'getRequest';
        }
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')) {
            $methods[] = 'getArgumentProcessor';
        }
        $mockBuilder = $this->getMockBuilder(RenderingContext::class)
            ->onlyMethods($methods)
            ->disableOriginalConstructor();
        if (!method_exists(RenderingContext::class, 'getRequest')) {
            $mockBuilder->addMethods(['getRequest']);
        }
        $renderingContext = $mockBuilder->getMock();
        $renderingContext->method('getRequest')->willReturn(null);
        $renderingContext->method('getViewHelperResolver')->willReturn($this->viewHelperResolver);
        $renderingContext->method('getViewHelperVariableContainer')->willReturn($this->viewHelperVariableContainer);
        $renderingContext->method('getVariableProvider')->willReturn($this->templateVariableContainer);
        $renderingContext->method('getViewHelperInvoker')->willReturn($this->viewHelperInvoker);
        $renderingContext->method('getErrorHandler')->willReturn($this->errorHandler);
        $renderingContext->method('getTemplateParser')->willReturn($this->templateParser);
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')) {
            $renderingContext->method('getArgumentProcessor')->willReturn(new StrictArgumentProcessor());
        }
        $renderingContext->method('getTemplateProcessors')->willReturn($this->templateProcessors);
        $renderingContext->method('getExpressionNodeTypes')->willReturn($this->expressionTypes);

        return $renderingContext;
    }

    /**
     * @return mixed
     */
    protected function executeViewHelper(
        array $arguments = [],
        array $variables = [],
        ?NodeInterface $childNode = null,
        ?string $extensionName = null,
        ?string $pluginName = null
    ): mixed {
        $instance = $this->buildViewHelperInstance($arguments, $variables, $childNode, $extensionName, $pluginName);
        self::assertInstanceOf(RenderingContextInterface::class, $this->renderingContext);
        $this->renderingContext->getVariableProvider()->setSource($variables);
        return $this->executeInstance($instance, $arguments);
    }

    /**
     * @return mixed
     */
    protected function executeInstance(ViewHelperInterface $instance, array $arguments = []): mixed
    {
        self::assertInstanceOf(RenderingContextInterface::class, $this->renderingContext);
        return $this->renderingContext->getViewHelperInvoker()->invoke($instance, $arguments, $this->renderingContext);
    }

    /**
     * @param mixed $nodeValue
     * @return mixed
     */
    protected function executeViewHelperUsingTagContent(
        $nodeValue,
        array $arguments = [],
        array $variables = [],
        ?string $extensionName = null,
        ?string $pluginName = null
    ): mixed {
        $node = $this->getMockBuilder(NodeInterface::class)->getMockForAbstractClass();
        $node->method('evaluate')->willReturn($nodeValue);
        $instance = $this->buildViewHelperInstance($arguments, $variables, $node, $extensionName, $pluginName);
        self::assertInstanceOf(RenderingContextInterface::class, $this->renderingContext);
        return $this->renderingContext->getViewHelperInvoker()->invoke($instance, $arguments, $this->renderingContext);
    }

    /**
     * @param NodeInterface[] $childNNodes
     */
    protected function createViewHelperNode(
        ViewHelperInterface $instance,
        array $arguments,
        array $childNNodes = []
    ): ViewHelperNode {
        $dummyNode = new DummyViewHelperNode($instance);
        $node = $dummyNode->getNode();
        $node->setArguments($arguments);

        foreach ($childNNodes as $childNNode) {
            $node->addChildNode($childNNode);
        }

        $instance->setViewHelperNode($node);

        return $node;
    }

    protected function createObjectAccessorNode(string $accessor): ObjectAccessorNode
    {
        return new ObjectAccessorNode($accessor);
    }

    protected function expectViewHelperException(): void
    {
        $this->expectException(Exception::class);
    }
}
