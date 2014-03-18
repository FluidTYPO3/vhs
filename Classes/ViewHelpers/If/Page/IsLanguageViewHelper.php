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
 ***************************************************************/

/**
 * ### Condition: Is current language
 *
 * A condition ViewHelper which renders the `then` child if
 * current language matches the provided language uid or language
 * title. When using language titles like 'de' it is required to
 * provide a default title to distinguish between the standard
 * and a non existing language.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\If\Page
 */
class Tx_Vhs_ViewHelpers_If_Page_IsLanguageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {

	/**
	 * Render method
	 *
	 * @param mixed $language
	 * @param string $defaultTitle
	 * @return string
	 */
	public function render($language, $defaultTitle = 'en') {
		$currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
		if (TRUE === is_numeric($language)) {
			$languageUid = intval($language);
		} else {
			$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid', 'sys_language', "title='" . $language . "'");
			if (FALSE !== $row) {
				$languageUid = intval($row['uid']);
			} else {
				if ($language == $defaultTitle) {
					$languageUid = $currentLanguageUid;
				} else {
					$languageUid = -1;
				}
			}
		}
		if ($languageUid === $currentLanguageUid) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
