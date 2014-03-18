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
 * ### Page: Deferred menu rendering ViewHelper
 *
 * Place this ViewHelper inside any other ViewHelper which
 * has been configured with the `deferred` attribute set to
 * TRUE - this will cause the output of the parent to only
 * contain the content of this ViewHelper.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_Menu_DeferredViewHelper extends Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper {

	/**
	 * Initialize
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->overrideArgument('as', 'string', 'If used, stores the menu pages as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used', FALSE, NULL);
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function render() {
		$as = $this->arguments['as'];
		if (FALSE === $this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredArray')) {
			return NULL;
		}
		if (FALSE === $this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredString')) {
			return NULL;
		}
		if (NULL === $as) {
			$content = $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredString');
			$this->unsetDeferredVariableStorage();
			return $content;
		} elseif (TRUE === empty($as)) {
			throw new Exception('An "as" attribute was used but was empty - use a proper string value', 1370096373);
		}
		if ($this->templateVariableContainer->exists($as)) {
			$backupVariable = $this->templateVariableContainer->get($as);
			$this->templateVariableContainer->remove($as);
		}
		$this->templateVariableContainer->add($as, $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredArray'));
		$this->unsetDeferredVariableStorage();
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		if (TRUE === isset($backupVariable)) {
			$this->templateVariableContainer->add($as, $backupVariable);
		}
		return $content;
	}

}
