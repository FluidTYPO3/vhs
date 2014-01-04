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
 * Uncaches partials. Use like ``f:render``.
 * The partial will then be rendered each time.
 * Please be aware that this will impact render time.
 * Arguments must be serializable and will be cached.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class Tx_Vhs_ViewHelpers_Render_UncacheViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('partial', 'string', 'Reference to a partial.', TRUE);
		$this->registerArgument('section', 'string', 'Name of section inside the partial to render.', FALSE, NULL);
		$this->registerArgument('arguments', 'array', 'Arguments to pass to the partial.', FALSE, NULL);
	}

	/**
	 * @return string
	 */
	public function render() {
		$partialArguments = $this->arguments['arguments'];
		if (FALSE === is_array($partialArguments)) {
			$partialArguments = array();
		}
		if (FALSE === isset($partialArguments['settings']) && TRUE === $this->templateVariableContainer->exists('settings')) {
			$partialArguments['settings'] = $this->templateVariableContainer->get('settings');
		}

		$substKey = 'INT_SCRIPT.' . $GLOBALS['TSFE']->uniqueHash();
		$content = '<!--' . $substKey . '-->';
		$templateView = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Vhs_View_UncacheTemplateView');

		$GLOBALS['TSFE']->config['INTincScript'][$substKey] = array(
			'type' => 'POSTUSERFUNC',
			'cObj' => serialize($templateView),
			'postUserFunc' => 'render',
			'conf' => array(
				'partial' => $this->arguments['partial'],
				'section' => $this->arguments['section'],
				'arguments' => $partialArguments,
				'controllerContext' => $this->renderingContext->getControllerContext()
			),
			'content' => $content
		);

		return $content;
	}

}
