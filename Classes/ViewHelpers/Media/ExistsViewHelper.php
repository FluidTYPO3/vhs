<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;
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
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * File/Directory Exists Condition ViewHelper
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class ExistsViewHelper extends AbstractConditionViewHelper {

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

		$file = GeneralUtility::getFileAbsFileName($this->arguments['file']);
		$directory = $this->arguments['directory'];

		$evaluation = FALSE;
		if (TRUE === isset($this->arguments['file'])) {
			$evaluation = (boolean) ((TRUE === file_exists($file) || TRUE === file_exists(constant('PATH_site') . $file)) && TRUE === is_file($file));
		} elseif (TRUE === isset($this->arguments['directory'])) {
			$evaluation = (boolean) (TRUE === is_dir($directory) || TRUE === is_dir(constant('PATH_site') . $directory));
		}

		if (FALSE !== $evaluation) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
