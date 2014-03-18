<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
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
 * Returns the current language from languages depending on l18n settings.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_LanguageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var Tx_Vhs_Service_PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param Tx_Vhs_Service_PageSelectService $pageSelectService
	 * @return void
	 */
	public function injectPageSelectService(Tx_Vhs_Service_PageSelectService $pageSelectService) {
		$this->pageSelect = $pageSelectService;
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('languages', 'mixed', 'The languages (either CSV, array or implementing Traversable)', FALSE);
		$this->registerArgument('pageUid', 'integer', 'The page uid to check', FALSE, 0);
		$this->registerArgument('normalWhenNoLanguage', 'boolean', 'If TRUE, a missing page overlay should be ignored', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		if ('BE' === TYPO3_MODE) {
			return;
		}

		$languages = $this->arguments['languages'];
		if (TRUE === $languages instanceof Traversable) {
			$languages = iterator_to_array($languages);
		} elseif (TRUE === is_string($languages)) {
			$languages = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $languages, TRUE);
		} else {
			$languages = (array) $languages;
		}

		$pageUid = intval($this->arguments['pageUid']);
		$normalWhenNoLanguage = $this->arguments['normalWhenNoLanguage'];

		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		$currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
		$languageUid = 0;
		if (FALSE === $this->pageSelect->hidePageForLanguageUid($pageUid, $currentLanguageUid, $normalWhenNoLanguage)) {
			$languageUid = $currentLanguageUid;
		} elseif (0 !== $currentLanguageUid) {
			if (TRUE === $this->pageSelect->hidePageForLanguageUid($pageUid, 0, $normalWhenNoLanguage)) {
				return;
			}
		}

		if (FALSE === empty($languages[$languageUid])) {
			return $languages[$languageUid];
		}

		return $languageUid;
	}

}
