<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3\CMS\Fluid\Core\Widget\WidgetContext;

/**
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers
 */
abstract class AbstractViewHelperTest extends UnitTestCase {

	/**
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * Setup global
	 */
	public function setUp() {
		parent::setUp();
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
	}

	/**
	 * @test
	 */
	public function canCreateViewHelperInstance() {
		$instance = $this->createInstance();
		$this->assertInstanceOf($this->getViewHelperClassName(), $instance);
	}

	/**
	 * @test
	 */
	public function canPrepareArguments() {
		$instance = $this->createInstance();
		$arguments = $instance->prepareArguments();
		$this->assertThat($arguments, new \PHPUnit_Framework_Constraint_IsType(\PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY));
	}

	/**
	 * @return string
	 */
	protected function getViewHelperClassName() {
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
	protected function createNode($type, $value) {
		if ('Boolean' === $type) {
			$value = $this->createNode('Text', strval($value));
		}
		/** @var NodeInterface $node */
		$className = 'TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\' . $type . 'Node';
		$node = new $className($value);
		return $node;
	}

	/**
	 * @return AbstractViewHelper
	 */
	protected function createInstance() {
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
	protected function buildViewHelperInstance($arguments = array(), $variables = array(), $childNode = NULL, $extensionName = NULL, $pluginName = NULL) {
		$instance = $this->createInstance();
		$node = new ViewHelperNode($instance, $arguments);
		/** @var RenderingContext $renderingContext */
		$renderingContext = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\Rendering\\RenderingContext');
		/** @var TemplateVariableContainer $container */
		$container = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TemplateVariableContainer');
		if (0 < count($variables)) {
			ObjectAccess::setProperty($container, 'variables', $variables, TRUE);
		}
		ObjectAccess::setProperty($renderingContext, 'templateVariableContainer', $container, TRUE);
		if (NULL !== $extensionName || NULL !== $pluginName) {
			/** @var ViewHelperVariableContainer $viewHelperContainer */
			$viewHelperContainer = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\ViewHelperVariableContainer');
			/** @var UriBuilder $uriBuilder */
			$uriBuilder = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Routing\\UriBuilder');
			/** @var Request $request */
			$request = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Request');
			if (NULL !== $extensionName) {
				$request->setControllerExtensionName($extensionName);
			}
			if (NULL !== $pluginName) {
				$request->setPluginName($pluginName);
			}
			/** @var Response $response */
			$response = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Response');
			/** @var ControllerContext $controllerContext */
			$controllerContext = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ControllerContext');
			$controllerContext->setRequest($request);
			$controllerContext->setResponse($response);
			$controllerContext->setUriBuilder($uriBuilder);
			ObjectAccess::setProperty($renderingContext, 'viewHelperVariableContainer', $viewHelperContainer, TRUE);
			$renderingContext->setControllerContext($controllerContext);
		}
		if (TRUE === $instance instanceof \Tx_Fluidwidget_Core_Widget_AbstractWidgetViewHelper) {
			/** @var WidgetContext $widgetContext */
			$widgetContext = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\Widget\\WidgetContext');
			ObjectAccess::setProperty($instance, 'widgetContext', $widgetContext, TRUE);
		}
		if (NULL !== $childNode) {
			$node->addChildNode($childNode);
			if ($instance instanceof ChildNodeAccessInterface) {
				$instance->setChildNodes(array($childNode));
			}
		}
		$instance->setArguments($arguments);
		$instance->setRenderingContext($renderingContext);
		$instance->setViewHelperNode($node);
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
	protected function executeViewHelper($arguments = array(), $variables = array(), $childNode = NULL, $extensionName = NULL, $pluginName = NULL) {
		$instance = $this->buildViewHelperInstance($arguments, $variables, $childNode, $extensionName, $pluginName);
		$output = $instance->initializeArgumentsAndRender();
		return $output;
	}

	/**
	 * @param string $nodeType
	 * @param mixed $nodeValue
	 * @param array $arguments
	 * @param array $variables
	 * @param string $extensionName
	 * @param string $pluginName
	 * @return mixed
	 */
	protected function executeViewHelperUsingTagContent($nodeType, $nodeValue, $arguments = array(), $variables = array(), $extensionName = NULL, $pluginName = NULL) {
		$childNode = $this->createNode($nodeType, $nodeValue);
		$instance = $this->buildViewHelperInstance($arguments, $variables, $childNode, $extensionName, $pluginName);
		$output = $instance->initializeArgumentsAndRender();
		return $output;
	}

}
