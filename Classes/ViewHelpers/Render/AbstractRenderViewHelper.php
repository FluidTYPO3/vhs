<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

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
 ***************************************************************/

/**
 * ### Base class for all rendering ViewHelpers.
 *
 * If errors occur they can be graciously ignored and
 * replaced by a small error message or the error itself.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

abstract class AbstractRenderViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
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
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	protected function getPreparedClonedView() {
		$view = $this->getPreparedView();
		$view->setControllerContext(clone $this->controllerContext);
		$view->setFormat($this->controllerContext->getRequest()->getFormat());
		$view->assignMultiple($this->templateVariableContainer->getAll());
		return $view;
	}

	/**
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	protected function getPreparedView() {
		/** @var $view \TYPO3\CMS\Fluid\View\StandaloneView */
		$view = $this->objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');
		return $view;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
	 * @throws \Exception
	 * @return string
	 */
	protected function renderView(ViewInterface $view) {
		try {
			$content = $view->render();
		} catch (\Exception $error) {
			if (!$this->arguments['graceful']) {
				throw $error;
			}
			$content = $error->getMessage() . ' (' . $error->getCode() . ')';
		}
		return $content;
	}

}
