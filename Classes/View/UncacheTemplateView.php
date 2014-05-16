<?php
namespace FluidTYPO3\Vhs\View;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * Uncache Template View
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage View
 */
use \TYPO3\CMS\Fluid\View\TemplateView;
use \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use  \TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;

class UncacheTemplateView extends TemplateView {

	/**
	 * @param string $postUserFunc
	 * @param array $conf
	 * @param string $content
	 * @return string
	 */
	public function callUserFunction($postUserFunc, $conf, $content) {
		$partial = $conf['partial'];
		$section = $conf['section'];
		$arguments = TRUE === is_array($conf['arguments']) ? $conf['arguments'] : array();
		/** @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext */
		$controllerContext = $conf['controllerContext'];
		if (TRUE === empty($partial)) {
			return '';
		}
		$this->prepareContextsForUncachedRendering($controllerContext);
		return $this->renderPartialUncached($partial, $section, $arguments);
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
	 * @return void
	 */
	protected function prepareContextsForUncachedRendering(ControllerContext $controllerContext) {
		$renderingContext = $this->objectManager->get('TYPO3\CMS\Fluid\Core\Rendering\RenderingContext');
		$renderingContext->setControllerContext($controllerContext);
		$this->setRenderingContext($renderingContext);
		$this->templateParser = TemplateParserBuilder::build();
		$this->templateCompiler = $this->objectManager->get('TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler');
		$this->templateCompiler->setTemplateCache($GLOBALS['typo3CacheManager']->getCache('fluid_template'));
	}

	/**
	 * @param string $partial
	 * @param string $section
	 * @param array $arguments
	 * @return string
	 */
	protected function renderPartialUncached($partial, $section = NULL, $arguments = array()) {
		array_push($this->renderingStack, array('type' => self::RENDERING_TEMPLATE, 'parsedTemplate' => NULL, 'renderingContext' => $this->getCurrentRenderingContext()));
		$rendered = $this->renderPartial($partial, $section, $arguments);
		array_pop($this->renderingStack);
		return $rendered;
	}

}
