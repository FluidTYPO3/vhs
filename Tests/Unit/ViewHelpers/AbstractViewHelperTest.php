<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
abstract class Tx_Vhs_ViewHelpers_AbstractViewHelperTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @param string $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->objectManager = clone $objectManager;
		parent::__construct($name, $data, $dataName);
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
		$this->assertThat($arguments, new PHPUnit_Framework_Constraint_IsType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY));
	}

	/**
	 * @return string
	 */
	protected function getViewHelperClassName() {
		$class = get_class($this);
		return substr($class, 0, -4);
	}

	/**
	 * @param string $type
	 * @param mixed $value
	 * @return Tx_Fluid_Core_Parser_SyntaxTree_NodeInterface
	 */
	protected function createNode($type, $value) {
		if ('Boolean' === $type) {
			$value = $this->createNode('Text', strval($value));
		}
		/** @var Tx_Fluid_Core_Parser_SyntaxTree_NodeInterface $node */
		$className = 'Tx_Fluid_Core_Parser_SyntaxTree_' . $type . 'Node';
		$node = new $className($value);
		return $node;
	}

	/**
	 * @return \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
	 */
	protected function createInstance() {
		$className = $this->getViewHelperClassName();
		/** @var \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper $instance */
		$instance = $this->objectManager->get($className);
		if (TRUE === method_exists($instance, 'injectConfigurationManager')) {
			$cObject = new tslib_cObj();
			$cObject->start(array(), 'tt_content');
			/** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
			$configurationManager = $this->objectManager->get('TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface');
			$configurationManager->setContentObject($cObject);
			$instance->injectConfigurationManager($configurationManager);
		}
		$instance->initialize();
		return $instance;
	}

	/**
	 * @param array $arguments
	 * @param array $variables
	 * @param Tx_Fluid_Core_Parser_SyntaxTree_NodeInterface $childNode
	 * @param string $extensionName
	 * @param string $pluginName
	 * @return \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
	 */
	protected function buildViewHelperInstance($arguments = array(), $variables = array(), $childNode = NULL, $extensionName = NULL, $pluginName = NULL) {
		$instance = $this->createInstance();
		/** @var Tx_Fluid_Core_ViewHelper_TemplateVariableContainer $container */
		$container = $this->objectManager->get('Tx_Fluid_Core_ViewHelper_TemplateVariableContainer');
		/** @var Tx_Fluid_Core_ViewHelper_ViewHelperVariableContainer $viewHelperContainer */
		$viewHelperContainer = $this->objectManager->get('Tx_Fluid_Core_ViewHelper_ViewHelperVariableContainer');
		if (0 < count($variables)) {
			\TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($container, 'variables', $variables, TRUE);
		}
		$node = new Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode($instance, $arguments);
		/** @var Tx_Extbase_MVC_Web_Routing_UriBuilder $uriBuilder */
		$uriBuilder = $this->objectManager->get('Tx_Extbase_MVC_Web_Routing_UriBuilder');
		/** @var Tx_Extbase_Mvc_Web_Request $request */
		$request = $this->objectManager->get('Tx_Extbase_Mvc_Web_Request');
		if (NULL !== $extensionName) {
			$request->setControllerExtensionName($extensionName);
		}
		if (NULL !== $pluginName) {
			$request->setPluginName($pluginName);
		}
		/** @var Tx_Extbase_Mvc_Web_Response $response */
		$response = $this->objectManager->get('Tx_Extbase_Mvc_Web_Response');
		/** @var Tx_Extbase_MVC_Controller_ControllerContext $controllerContext */
		$controllerContext = $this->objectManager->get('Tx_Extbase_MVC_Controller_ControllerContext');
		$controllerContext->setRequest($request);
		$controllerContext->setResponse($response);
		$controllerContext->setUriBuilder($uriBuilder);
		/** @var Tx_Fluid_Core_Rendering_RenderingContext $renderingContext */
		$renderingContext = $this->objectManager->get('Tx_Fluid_Core_Rendering_RenderingContext');
		$renderingContext->setControllerContext($controllerContext);
		\TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($renderingContext, 'viewHelperVariableContainer', $viewHelperContainer, TRUE);
		\TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($renderingContext, 'templateVariableContainer', $container, TRUE);
		$instance->setArguments($arguments);
		$instance->setRenderingContext($renderingContext);
		if (TRUE === $instance instanceof Tx_Fluidwidget_Core_Widget_AbstractWidgetViewHelper) {
			/** @var Tx_Fluid_Core_Widget_WidgetContext $widgetContext */
			$widgetContext = $this->objectManager->get('Tx_Fluid_Core_Widget_WidgetContext');
			\TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($instance, 'widgetContext', $widgetContext, TRUE);
		}
		if (NULL !== $childNode) {
			$node->addChildNode($childNode);
			if ($instance instanceof Tx_Fluid_Core_ViewHelper_Facets_ChildNodeAccessInterface) {
				$instance->setChildNodes(array($childNode));
			}
		}
		$instance->setViewHelperNode($node);
		return $instance;
	}

	/**
	 * @param array $arguments
	 * @param array $variables
	 * @param Tx_Fluid_Core_Parser_SyntaxTree_NodeInterface $childNode
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
