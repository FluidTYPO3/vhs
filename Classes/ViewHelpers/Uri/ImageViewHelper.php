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
 * ************************************************************* */

/**
 * Returns the relative or absolute URI for the image resource
 * or it's derivate if differing dimesions are provided.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Uri
 */
class Tx_Vhs_ViewHelpers_Uri_ImageViewHelper extends Tx_Vhs_ViewHelpers_Media_AbstractMediaViewHelper {

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('width', 'int', 'Optional width.', FALSE);
		$this->registerArgument('height', 'int', 'Optional height.', FALSE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {

		$this->preprocessImage();

		$src = $this->mediaSource;

		if (TYPO3_MODE === 'BE' || FALSE === $this->arguments['relative']) {
			$src = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $src;
		}

		return $src;
	}

}
