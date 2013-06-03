<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
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
 * ### Condition: String matches regular expression
 *
 * Condition ViewHelper which renders the `then` child if provided
 * string matches provided regular expression. $matches array containing
 * the results can be made available by providing a template variable
 * name with argument $as.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\If\String
 */
class Tx_Vhs_ViewHelpers_If_String_PregViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param string $pattern
	 * @param string $string
	 * @param boolean $global
	 * @param string $as
	 * @return string
	 */
	public function render($pattern, $string, $global = FALSE, $as = NULL) {
		$matches = array();
		if (TRUE === (boolean) $global) {
			preg_match_all($pattern, $string, $matches);
		} else {
			preg_match($pattern, $string, $matches);
		}
		if (FALSE === empty($as)) {
			if ($this->templateVariableContainer->exists($as)) {
				$backupVariable = $this->templateVariableContainer->get($as);
				$this->templateVariableContainer->remove($as);
			}
			$this->templateVariableContainer->add($as, $matches);
		}
		if (0 < count($matches)) {
			$content = $this->renderThenChild();
		} else {
			$content =  $this->renderElseChild();
		}
		if (TRUE === isset($backupVariable)) {
			$this->templateVariableContainer->add($as, $backupVariable);
		}
		return $content;
	}
}
