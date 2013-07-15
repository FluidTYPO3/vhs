<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>
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
 * @protection off
 * @author Claus Due <claus@wildside.dk>
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_Math_SubtractViewHelperTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var $objectManager Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @param $objectManager Tx_Extbase_Object_ObjectManagerInterface
	 * @return void
	 */
	protected function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @return Tx_Vhs_ViewHelpers_Math_SubtractViewHelper
	 * @support
	 */
	protected function getPreparedInstance() {
		$viewHelperClassName = 'Tx_Vhs_ViewHelpers_Math_SubtractViewHelper';
		$arguments = array();
		$nodeClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode' : '\\TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ViewHelperNode');
		$renderingContextClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Fluid_Core_Rendering_RenderingContext' : '\\TYPO3\\CMS\\Fluid\\Core\\Rendering\\RenderingContext');
		$controllerContextClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Extbase_MVC_Controller_ControllerContext' : '\\TYPO3\\CMS\\Extbase\\MVC\\Controller\\ControllerContext');
		$requestClassName = (FALSE !== strpos($viewHelperClassName, '_') ? 'Tx_Extbase_MVC_Web_Request' : '\\TYPO3\\CMS\\Extbase\\MVC\\Web\\Request');

		/** @var Tx_Extbase_MVC_Web_Request $request */
		$request = $this->objectManager->get($requestClassName);
		/** @var $viewHelperInstance Tx_Fluid_Core_ViewHelper_AbstractViewHelper */
		$viewHelperInstance = $this->objectManager->get($viewHelperClassName);
		/** @var Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode $node */
		$node = $this->objectManager->get($nodeClassName, $viewHelperInstance, $arguments);
		/** @var Tx_Extbase_MVC_Controller_ControllerContext $controllerContext */
		$controllerContext = $this->objectManager->get($controllerContextClassName);
		$controllerContext->setRequest($request);
		/** @var Tx_Fluid_Core_Rendering_RenderingContext $renderingContext */
		$renderingContext = $this->objectManager->get($renderingContextClassName);
		$renderingContext->setControllerContext($controllerContext);

		$viewHelperInstance->setRenderingContext($renderingContext);
		$viewHelperInstance->setViewHelperNode($node);
		return $viewHelperInstance;
	}

	/**
	 * @test
	 */
	public function canCreateViewHelperClassInstance() {
		$instance = $this->getPreparedInstance();
		$this->assertInstanceOf('Tx_Vhs_ViewHelpers_Math_SubtractViewHelper', $instance);
	}

	/**
	 * @test
	 */
	public function canInitializeViewHelper() {
		$instance = $this->getPreparedInstance();
		$instance->initialize();
	}

	/**
	 * @test
	 */
	public function canPrepareViewHelperArguments() {
		$instance = $this->getPreparedInstance();
		$this->assertInstanceOf('Tx_Vhs_ViewHelpers_Math_SubtractViewHelper', $instance);
		$arguments = $instance->prepareArguments();
		$constraint = new PHPUnit_Framework_Constraint_IsType('array');
		$this->assertThat($arguments, $constraint);
	}

	/**
	 * @test
	 */
	public function canSetViewHelperNode() {
		$instance = $this->getPreparedInstance();
		$arguments = $instance->prepareArguments();
		$node = new \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode($instance, $arguments);
		$instance->setViewHelperNode($node);
	}

}
