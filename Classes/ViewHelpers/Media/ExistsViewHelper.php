<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * File/Directory Exists Condition ViewHelper
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class Tx_Vhs_ViewHelpers_ExistsViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('file', 'string', 'Filename which must exist to trigger f:then rendering', FALSE);
		$this->registerArgument('directory', 'string', 'Directory which must exist to trigger f:then rendering', FALSE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {

		$file = t3lib_div::getFileAbsFileName($this->arguments['file']);
		$directory = $this->arguments['directory'];

		$evaluation = FALSE;
		if (isset($this->arguments['file'])) {
			$evaluation = (file_exists($file) || file_exists(PATH_site . $file)) && is_file($file);
		} elseif (isset($this->arguments['directory'])) {
			$evaluation = (is_dir($directory) || is_dir(PATH_site . $directory));
		}

		if ($evaluation !== FALSE) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
