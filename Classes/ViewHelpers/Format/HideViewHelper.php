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
 * Hides output from browser, but still renders tag content
 * which means any ViewHelper inside the tag content still
 * gets processed.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Format
 */
class Tx_Vhs_ViewHelpers_Format_HideViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('disabled', 'boolean', 'If TRUE, renders content - use to quickly enable/disable Fluid code', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		if ($this->arguments['disabled'] === TRUE) {
			return $this->renderChildren();
		} else {
			$this->renderChildren();
		}
		return NULL;
	}

}
