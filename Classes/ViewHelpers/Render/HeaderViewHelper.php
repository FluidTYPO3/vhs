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
 * ViewHelper used to render content header from raw records typically fetched
 * with <v:content.get(column: '0', render: FALSE) />
 *
 * @author Denys Koch, <koch@louis.info>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 * @see http://typo3.org/documentation/snippets/sd/12/
 */
class Tx_Vhs_ViewHelpers_Render_HeaderViewHelper extends Tx_Vhs_ViewHelpers_Content_AbstractContentViewHelper {

	/**
	 * Render method
	 *
	 * @param array $record
	 * @return string
	 */
	public function render(array $record = array()) {
		if (FALSE === isset($record['uid'])) {
			return NULL;
		}
		// clone the cObj so the data you writh into it does not influence the cObj which is used by the whole pi, otherwise you'll get strange phenomens
		$localCObj = clone $this->contentObject;

		// add record data
		$localCObj->data = $record;
		$conf = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

		// execute your typoscript
		$content = $localCObj->cObjGetSingle($conf['lib.']['stdheader'], $conf['lib.']['stdheader.']);

		return $content;
	}
}
