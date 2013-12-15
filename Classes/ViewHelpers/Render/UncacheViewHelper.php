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
 * Uncaches partials or sections. Use like ``f:render``.
 * The partial or section will then be rendered each time.
 * Please be aware that this will impact render time.
 * Arguments must be serializable and will be cached.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class Tx_Vhs_ViewHelpers_Render_UncacheViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('section', 'string', 'Name of section to render. If used in a layout, renders a section of the main content file. If used inside a standard template, renders a section of the same file.', FALSE, NULL);
		$this->registerArgument('partial', 'string', 'Reference to a partial.', FALSE, NULL);
		$this->registerArgument('arguments', 'array', 'Arguments to pass to the partial.', FALSE, NULL);
		$this->registerArgument('optional', 'boolean', 'Set to TRUE, to ignore unknown sections, so the definition of a section inside a template can be optional for a layout', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		$arguments = $this->arguments['arguments'];
		if (FALSE === is_array($arguments)) {
			$arguments = array();
		}

		if (FALSE === isset($arguments['settings']) && TRUE === $this->templateVariableContainer->exists('settings')) {
			$arguments['settings'] = $this->templateVariableContainer->get('settings');
		}

		$substKey = 'INT_SCRIPT.' . $GLOBALS['TSFE']->uniqueHash();
		$content = '<!--' . $substKey . '-->';

		$GLOBALS['TSFE']->config['INTincScript'][$substKey] = array(
			'type' => 'POSTUSERFUNC',
			'cObj' => serialize($this),
			'postUserFunc' => 'render',
			'conf' => array(
				'arguments' => $arguments,
				'view' => serialize($this->viewHelperVariableContainer->getView())
			)
		);

		return $content;
	}

	/**
	 * @param string $postUserFunc
	 * @param array $conf
	 * @return string
	 */
	public function callUserFunction($postUserFunc, $conf) {
		$section = $this->arguments['section'];
		$partial = $this->arguments['partial'];
		$optional = (boolean) $this->arguments['optional'];

		$arguments = $conf['arguments'];
		$view = unserialize($conf['view']);

		if (NULL !== $partial) {
			return $view->renderPartial($partial, $section, $arguments);
		} elseif (NULL !== $section) {
			return $view->renderSection($section, $arguments, $optional);
		}

		return '';
	}

}
