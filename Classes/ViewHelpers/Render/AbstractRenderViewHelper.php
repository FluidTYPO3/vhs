<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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
 ***************************************************************/

/**
 * ### Base class for all rendering ViewHelpers.
 *
 * If errors occur they can be graciously ignored and
 * replaced by a small error message or the error itself.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
abstract class Tx_Vhs_ViewHelpers_Render_AbstractRenderViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('onError', 'string', 'Optional error message to display if error occur while rendering. If NULL, lets the error Exception pass trough (and break rendering)', FALSE, NULL);
		$this->registerArgument('graceful', 'boolean', 'If forced to FALSE, errors are not caught but rather "transmitted" as every other error would be', FALSE, FALSE);
	}

	/**
	 * @return array
	 */
	protected function getPreparedNamespaces() {
		$namespaces = array();
		foreach ((array) $this->arguments['namespaces'] as $namespaceIdentifier => $namespace) {
			$addedOverriddenNamespace = '{namespace ' . $namespaceIdentifier . '=' . $namespace . '}';
			array_push($namespaces, $addedOverriddenNamespace);
		}
		return $namespaces;
	}

	/**
	 * @return Tx_Fluid_View_StandaloneView
	 */
	protected function getPreparedClonedView() {
		$view = $this->getPreparedView();
		$view->setControllerContext(clone $this->controllerContext);
		$view->setFormat($this->controllerContext->getRequest()->getFormat());
		$view->assignMultiple($this->templateVariableContainer->getAll());
		return $view;
	}

	/**
	 * @return Tx_Fluid_View_StandaloneView
	 */
	protected function getPreparedView() {
		/** @var $view Tx_Fluid_View_StandaloneView */
		$view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		return $view;
	}

	/**
	 * @param Tx_Extbase_MVC_View_ViewInterface $view
	 * @throws Exception
	 * @return string
	 */
	protected function renderView(Tx_Extbase_MVC_View_ViewInterface $view) {
		try {
			$content = $view->render();
		} catch (Exception $error) {
			if (!$this->arguments['graceful']) {
				throw $error;
			}
			$content = $error->getMessage() . ' (' . $error->getCode() . ')';
		}
		return $content;
	}

}
