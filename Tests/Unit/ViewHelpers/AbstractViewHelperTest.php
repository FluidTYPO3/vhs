<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Development\AbstractTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\Variables\CmsVariableProvider;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * Class AbstractViewHelperTest
 */
abstract class AbstractViewHelperTest extends AbstractTestCase
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var RenderingContextInterface
     */
    protected $renderingContext;

    /**
     * Setup global
     */
    public function setUp()
    {
        parent::setUp();
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->renderingContext = $this->objectManager->get(RenderingContext::class);
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
        $this->assertThat($arguments, new \PHPUnit_Framework_Constraint_IsType(\PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY));
    }

    /**
     * @return string
     */
    protected function getViewHelperClassName()
    {
        $class = get_class($this);
        $class = substr($class, 0, -4);
        $class = str_replace('Tests\\Unit\\', '', $class);
        return $class;
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return NodeInterface
     */
    protected function createNode($type, $value)
    {
        /** @var NodeInterface $node */
        $className = 'TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\' . $type . 'Node';
        if (class_exists($className)) {
            $node = new $className($value);
        } else {
            $className = 'TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\' . $type . 'Node';
            $node = new $className($value);
        }
        return $node;
    }

    /**
     * @return AbstractViewHelper
     */
    protected function createInstance()
    {
        $className = $this->getViewHelperClassName();
        /** @var AbstractViewHelper $instance */
        $instance = $this->objectManager->get($className);
        $instance->initialize();
        return $instance;
    }

    /**
     * @param array $arguments
     * @param array $variables
     * @param NodeInterface $childNode
     * @param string $extensionName
     * @param string $pluginName
     * @return AbstractViewHelper
     */
    protected function buildViewHelperInstance($arguments = [], $variables = [], $childNode = null, $extensionName = null, $pluginName = null)
    {
        $instance = $this->createInstance();
        $node = $this->createViewHelperNode($instance, $arguments);
        if (method_exists($this->renderingContext, 'getVariableProvider')) {
            $this->renderingContext->getVariableProvider()->setSource($variables);
        } else {
            /** @var TemplateVariableContainer $container */
            $container = $this->objectManager->get(TemplateVariableContainer::class);
            if (0 < count($variables)) {
                ObjectAccess::setProperty($container, 'variables', $variables, true);
            }
            ObjectAccess::setProperty($this->renderingContext, 'templateVariableContainer', $container, true);
        }

        /** @var ViewHelperVariableContainer $viewHelperContainer */
        $viewHelperContainer = $this->objectManager->get(ViewHelperVariableContainer::class);
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        /** @var Request $request */
        $request = $this->objectManager->get(Request::class);
        if (null !== $extensionName) {
            $request->setControllerExtensionName($extensionName);
        }
        if (null !== $pluginName) {
            $request->setPluginName($pluginName);
        }
        /** @var Response $response */
        $response = $this->objectManager->get(Response::class);
        /** @var ControllerContext $controllerContext */
        $controllerContext = $this->objectManager->get(ControllerContext::class);
        $controllerContext->setRequest($request);
        $controllerContext->setResponse($response);
        $controllerContext->setUriBuilder($uriBuilder);
        ObjectAccess::setProperty($this->renderingContext, 'viewHelperVariableContainer', $viewHelperContainer, true);
        $this->renderingContext->setControllerContext($controllerContext);

        $instance->setRenderingContext($this->renderingContext);
        $instance->setViewHelperNode($node);
        $instance->setArguments($arguments);
        if ($childNode) {
            $node->expects($this->any())->method('getChildNodes')->willReturn([$childNode]);
            if (method_exists($instance, 'setChildNodes')) {
                $instance->setChildNodes([$childNode]);
            }
        } else {
            $node->expects($this->any())->method('getChildNodes')->willReturn([]);
        }

        if (method_exists($instance, 'injectReflectionService')) {
            $instance->injectReflectionService($this->objectManager->get(ReflectionService::class));
        }
        return $instance;
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
        return $instance->initializeArgumentsAndRender();
    }

    /**
     * @param array $arguments
     * @param array $variables
     * @param NodeInterface $childNode
     * @param string $extensionName
     * @param string $pluginName
     * @return mixed
     */
    protected function executeViewHelperStatic($arguments = [], $variables = [], $childNode = null, $extensionName = null, $pluginName = null)
    {
        if ($childNode !== null) {
            $childClosure = function () use ($childNode) {
                return $childNode->evaluate($this->renderingContext);
            };
        } else {
            $childClosure = function () {
            };
        }
        $viewHelperClassName = $this->getViewHelperClassName();
        return $viewHelperClassName::renderStatic($arguments, $childClosure, $this->renderingContext);
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
        $context = $this->renderingContext;
        $instance = $this->buildViewHelperInstance($arguments, $variables, null, $extensionName, $pluginName);
        $instance->setRenderChildrenClosure(function() use ($instance, $nodeValue, $context) {
            if (method_exists($instance, 'setChildNodes')
                && (
                    $nodeValue instanceof \TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode
                    || $nodeValue instanceof \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode
                    || $nodeValue instanceof \TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface
                    || $nodeValue instanceof \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\NodeInterface
                )
            ) {
                $instance->setChildNodes([$nodeValue]);
                return $nodeValue->evaluate($context);
            }
            return $nodeValue;
        });
        return $instance->initializeArgumentsAndRender();
    }

    /**
     * @param ViewHelperInterface $instance
     * @param array $arguments
     * @return \PHPUnit_Framework_MockObject_MockObject|ViewHelperNode
     */
    protected function createViewHelperNode($instance, array $arguments)
    {
        if (!$this->usesLegacyFluidVersion()) {
            $node = $this->getMockBuilder(\TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode::class)
                ->setMethods(['getChildNodes', 'getUninitializedViewHelper'])
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $node = $this->getMockBuilder(ViewHelperNode::class)
                ->setMethods(['getChildNodes', 'getUninitializedViewHelper'])
                ->setConstructorArgs([$instance, $arguments])
                ->getMock();
        }
        $node->expects($this->any())->method('getUninitializedViewHelper')->willReturn($instance);
        return $node;
    }

    /**
     * @param string $accessor
     * @return \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode|\TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode
     */
    protected function createObjectAccessorNode($accessor) {
        if (!$this->usesLegacyFluidVersion()) {
            return new \TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode($accessor);
        }
        return new \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode($accessor);
    }

    /**
     * @param null|string $message
     * @param null|integer $code
     */
    protected function expectViewHelperException($message = null, $code = null)
    {
        if ($this->usesLegacyFluidVersion()) {
            $this->setExpectedException(\TYPO3\CMS\Fluid\Core\ViewHelper\Exception::class, $message, $code);
        } else {
            $this->setExpectedException(\TYPO3Fluid\Fluid\Core\ViewHelper\Exception::class, $message, $code);
        }
    }

    /**
     * @return boolean
     */
    protected function usesLegacyFluidVersion()
    {
        return version_compare(TYPO3_version, '8.0', '<');
    }
}
