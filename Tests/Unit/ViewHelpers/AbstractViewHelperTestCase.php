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
use TYPO3\CMS\Backend\Controller\DummyController;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Web\Request;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ErrorHandler\ErrorHandlerInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInvoker;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * AbstractViewHelperTestCase
 */
abstract class AbstractViewHelperTestCase extends AbstractTestCase
{
    protected ?RenderingContext $renderingContext;
    protected ?ViewHelperResolver $viewHelperResolver;
    protected ?ViewHelperInvoker $viewHelperInvoker;
    protected ?ViewHelperVariableContainer $viewHelperVariableContainer;
    protected ?StandardVariableProvider $templateVariableContainer;
    protected ?ControllerContext $controllerContext;
    protected ?ErrorHandlerInterface $errorHandler;
    protected ?TemplateParser $templateParser;
    protected array $templateProcessors = [];
    protected array $expressionTypes = [];

    /**
     * @var array
     */
    protected $defaultArguments = array(
        'name' => 'test'
    );

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $extbaseParameters = null;
        if (class_exists(ExtbaseRequestParameters::class)) {
            $extbaseParameters = new ExtbaseRequestParameters(DummyController::class);
        }
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturnMap(
            [
                ['applicationType', null, SystemEnvironmentBuilder::REQUESTTYPE_FE],
                ['extbase', null, $extbaseParameters],
            ]
        );

        if (class_exists(Request::class)) {
            $requestClassName = Request::class;
        } else {
            $requestClassName = \TYPO3\CMS\Extbase\Mvc\Request::class;
        }

        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '11.0', '<')) {
            $request = $this->getMockBuilder($requestClassName)
                ->setMethods(['getControllerExtensionName', 'getControllerName', 'getControllerActionName'])
                ->setConstructorArgs([DummyController::class])
                ->getMock();
        } else {
            $request = $this->getMockBuilder($requestClassName)
                ->setMethods(['getControllerExtensionName', 'getControllerName', 'getControllerActionName'])
                ->setConstructorArgs([$GLOBALS['TYPO3_REQUEST']])
                ->getMock();
        }

        $request->method('getControllerExtensionName')->willReturn('Vhs');
        $request->method('getControllerName')->willReturn('Controller');
        $request->method('getControllerActionName')->willReturn('action');

        $this->viewHelperResolver = $this->getMockBuilder(ViewHelperResolver::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewHelperVariableContainer = $this->getMockBuilder(ViewHelperVariableContainer::class)
            ->setMethods(['dummy'])
            ->getMock();
        $this->templateVariableContainer = new StandardVariableProvider();

        $this->viewHelperInvoker = $this->getMockBuilder(ViewHelperInvoker::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->renderingContext = $this->getMockBuilder(RenderingContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->errorHandler = $this->getMockBuilder(ErrorHandlerInterface::class)->getMockForAbstractClass();
        $this->templateParser = new TemplateParser();
        $this->templateParser->setRenderingContext($this->renderingContext);
        $this->errorHandler->method('handleViewHelperError')->willThrowException(new Exception('dummy'));
        $this->renderingContext->method('getViewHelperResolver')->willReturn($this->viewHelperResolver);

        $this->renderingContext->method('getViewHelperVariableContainer')->willReturn(
            $this->viewHelperVariableContainer
        );
        $this->renderingContext->method('getVariableProvider')->willReturn($this->templateVariableContainer);
        $this->renderingContext->method('getViewHelperInvoker')->willReturn($this->viewHelperInvoker);
        $this->renderingContext->method('getErrorHandler')->willReturn($this->errorHandler);
        $this->renderingContext->method('getTemplateParser')->willReturn($this->templateParser);
        $this->renderingContext->method('getTemplateProcessors')->willReturn($this->templateProcessors);
        $this->renderingContext->method('getExpressionNodeTypes')->willReturn($this->expressionTypes);

        if (method_exists($this->renderingContext, 'getRequest')) {
            $this->renderingContext->method('getRequest')->willReturn($request);
        } else {
            $uriBuilder = $this->getMockBuilder(UriBuilder::class)
                ->setMethods(['uriFor', 'buildFrontendUri', 'buildBackendUri', 'build'])
                ->disableOriginalConstructor()
                ->getMock();
            $uriBuilder->method('build')->willReturn('build');
            $uriBuilder->method('uriFor')->willReturn('for');
            $uriBuilder->method('buildFrontendUri')->willReturn('frontend');
            $uriBuilder->method('buildBackendUri')->willReturn('backend');

            $this->controllerContext = $this->getMockBuilder(ControllerContext::class)
                ->setMethods(['getRequest', 'getUriBuilder'])
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
    public function canCreateViewHelperInstance()
    {
        $instance = $this->createInstance();
        $this->assertInstanceOf($this->getViewHelperClassName(), $instance);
    }

    /**
     * @test
     */
    public function canPrepareArguments()
    {
        $instance = $this->createInstance();
        $arguments = $instance->prepareArguments();
        $this->assertIsArray($arguments);
    }

    /**
     * @return string
     */
    protected function getViewHelperClassName()
    {
        $class = get_class($this);
        $class = str_replace('Tests\\Unit\\', '', $class);
        return substr($class, 0, -4);
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return NodeInterface
     */
    protected function createNode($type, $value)
    {
        /** @var NodeInterface $node */
        $className = 'TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\' . $type . 'Node';
        $node = new $className($value);
        return $node;
    }

    /**
     * @return AbstractViewHelper
     */
    protected function createInstance()
    {
        $className = $this->getViewHelperClassName();
        /** @var AbstractViewHelper $instance */
        $instance = $this->getMockBuilder($className)->setMethods(['dummy'])->disableOriginalConstructor()->getMock();
        if (method_exists($instance, 'injectConfigurationManager')) {
            $cObject = $this->getMockBuilder(ContentObjectRenderer::class)->disableOriginalConstructor()->getMock();
            $cObject->start(['uid' => 123], 'tt_content');
            /** @var ConfigurationManagerInterface $configurationManager */
            $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass();
            $configurationManager->method('getContentObject')->willReturn($cObject);
            $instance->injectConfigurationManager($configurationManager);
        }
        $instance->setRenderingContext($this->renderingContext);
        return $instance;
    }

    /**
     * @param array $arguments
     * @param array $variables
     * @param NodeInterface|null $childNode
     * @param string|null $extensionName
     * @param string|null $pluginName
     * @return AbstractViewHelper
     */
    protected function buildViewHelperInstance($arguments = [], $variables = [], $childNode = null, $extensionName = null, $pluginName = null)
    {
        $instance = $this->createInstance();
        $arguments = $this->buildViewHelperArguments($instance, $arguments);
        $node = $this->createViewHelperNode($instance, $arguments, $childNode instanceof NodeInterface ? [$childNode] : []);
        $this->templateVariableContainer->setSource($variables);

        $instance->setViewHelperNode($node);
        $instance->setArguments($arguments);

        if ($instance instanceof AbstractTagBasedViewHelper || $instance instanceof \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper) {
            $tagBuilder = new TagBuilder(
                (string) $this->getInaccessiblePropertyValue($instance, 'tagName')
            );
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

    /**
     * @param array $arguments
     * @param array $variables
     * @param NodeInterface $childNode
     * @param string $extensionName
     * @param string $pluginName
     * @return mixed
     */
    protected function executeViewHelper($arguments = [], $variables = [], $childNode = null, $extensionName = null, $pluginName = null)
    {
        $instance = $this->buildViewHelperInstance($arguments, $variables, $childNode, $extensionName, $pluginName);
        $this->renderingContext->getVariableProvider()->setSource($variables);
        return $this->renderingContext->getViewHelperInvoker()->invoke($instance, $arguments, $this->renderingContext);
    }

    /**
     * @param mixed $nodeValue
     * @param array $arguments
     * @param array $variables
     * @param string $extensionName
     * @param string $pluginName
     * @return mixed
     */
    protected function executeViewHelperUsingTagContent($nodeValue, $arguments = [], $variables = [], $extensionName = null, $pluginName = null)
    {
        $node = $this->getMockBuilder(NodeInterface::class)->getMockForAbstractClass();
        $node->method('evaluate')->willReturn($nodeValue);
        $instance = $this->buildViewHelperInstance($arguments, $variables, $node, $extensionName, $pluginName);
        return $this->renderingContext->getViewHelperInvoker()->invoke($instance, $arguments, $this->renderingContext);
    }

    /**
     * @param ViewHelperInterface $instance
     * @param array $arguments
     * @param NodeInterface[] $childNNodes
     * @return MockObject|ViewHelperNode
     */
    protected function createViewHelperNode($instance, array $arguments, array $childNNodes = [])
    {
        $node = new DummyViewHelperNode($instance);

        foreach ($childNNodes as $childNNode) {
            $node->addChildNode($childNNode);
        }

        $instance->setViewHelperNode($node);

        return $node;
    }

    /**
     * @param string $accessor
     * @return ObjectAccessorNode
     */
    protected function createObjectAccessorNode($accessor)
    {
        return new ObjectAccessorNode($accessor);
    }

    /**
     * @return void
     */
    protected function expectViewHelperException()
    {
        $this->expectException(Exception::class);
    }
}
