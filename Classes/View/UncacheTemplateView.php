<?php
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
class Tx_Vhs_View_UncacheTemplateView extends Tx_Fluid_View_TemplateView {

	/**
	 * @param string $postUserFunc
	 * @param array $conf
	 * @param string $content
	 * @return string
	 */
	public function callUserFunction($postUserFunc, $conf, $content) {
		$partial = $conf['partial'];
		$section = $conf['section'];
		$arguments = $conf['arguments'];
		$controllerContext = $conf['controllerContext'];

		$renderingContext = $this->objectManager->get('Tx_Fluid_Core_Rendering_RenderingContext');
		$renderingContext->setControllerContext($controllerContext);
		$this->setRenderingContext($renderingContext);

		$this->templateParser = \TYPO3\CMS\Fluid\Compatibility\TemplateParserBuilder::build();
		$this->templateCompiler = $this->objectManager->get('Tx_Fluid_Core_Compiler_TemplateCompiler');
		$this->templateCompiler->setTemplateCache($GLOBALS['typo3CacheManager']->getCache('fluid_template'));

		if (NULL !== $partial) {
			array_push($this->renderingStack, array('type' => self::RENDERING_TEMPLATE, 'parsedTemplate' => NULL, 'renderingContext' => $renderingContext));
			return $this->renderPartial($partial, $section, $arguments);
			array_pop($this->renderingStack);
		}

		return '';
	}

}
