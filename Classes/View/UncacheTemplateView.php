<?php
namespace FluidTYPO3\Vhs\View;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\View\TemplateView;

/**
 * Uncache Template View
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage View
 */
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
		/** @var RenderingContext $renderingContext */
		$renderingContext = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\Rendering\\RenderingContext');
		$this->prepareContextsForUncachedRendering($renderingContext, $controllerContext);
		return $this->renderPartialUncached($renderingContext, $partial, $section, $arguments);
	}

	/**
	 * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
	 * @return void
	 */
	protected function prepareContextsForUncachedRendering(RenderingContextInterface $renderingContext, ControllerContext $controllerContext) {
		$renderingContext->setControllerContext($controllerContext);
		$this->setRenderingContext($renderingContext);
		$this->templateParser = TemplateParserBuilder::build();
		$this->templateCompiler = $this->objectManager->get('TYPO3\\CMS\\Fluid\\Core\\Compiler\\TemplateCompiler');
		$cacheManager = isset($GLOBALS['typo3CacheManager']) ? $GLOBALS['typo3CacheManager'] : GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
		$this->templateCompiler->setTemplateCache($cacheManager->getCache('fluid_template'));
	}

	/**
	 * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @param string $partial
	 * @param string $section
	 * @param array $arguments
	 * @return string
	 */
	protected function renderPartialUncached(RenderingContextInterface $renderingContext, $partial, $section = NULL, $arguments = array()) {
		array_push($this->renderingStack, array('type' => self::RENDERING_TEMPLATE, 'parsedTemplate' => NULL, 'renderingContext' => $renderingContext));
		$rendered = $this->renderPartial($partial, $section, $arguments);
		array_pop($this->renderingStack);
		return $rendered;
	}

}
