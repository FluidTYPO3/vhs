<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
class UncacheViewHelper extends AbstractViewHelper {

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
		$templateView = GeneralUtility::makeInstance('FluidTYPO3\\Vhs\\View\\UncacheTemplateView');

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
