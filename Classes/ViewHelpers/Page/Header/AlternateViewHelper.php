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
 * Returns the all alternate urls.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class Tx_Vhs_ViewHelpers_Page_Header_AlternateViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Vhs_Service_PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var Tx_Fluid_Core_ViewHelper_TagBuilder
	 */
	protected $tagBuilder;

	/**
	 * @param Tx_Vhs_Service_PageSelectService $pageSelectService
	 * @return void
	 */
	public function injectPageSelectService(Tx_Vhs_Service_PageSelectService $pageSelectService) {
		$this->pageSelect = $pageSelectService;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
		$this->tagBuilder = $this->objectManager->get('Tx_Fluid_Core_ViewHelper_TagBuilder');
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('languages', 'mixed', 'The languages (either CSV, array or implementing Traversable)', TRUE);
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
			$languages = t3lib_div::trimExplode(',', $languages, TRUE);
		} else {
			$languages = (array) $languages;
		}

		$pageUid = intval($this->arguments['pageUid']);
		$normalWhenNoLanguage = $this->arguments['normalWhenNoLanguage'];

		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		$currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
		unset($languages[$currentLanguageUid]);

		$uriBuilder = $this->controllerContext->getUriBuilder();
		$uriBuilder = $uriBuilder->reset()
			->setTargetPageUid($pageUid)
			->setCreateAbsoluteUri(TRUE);

		$this->tagBuilder->reset();
		$this->tagBuilder->setTagName('link');
		$this->tagBuilder->addAttribute('rel', 'alternate');

		$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
		$usePageRenderer = (1 !== intval($GLOBALS['TSFE']->config['config']['disableAllHeaderCode']));
		$output = '';

		foreach ($languages as $languageUid => $languageName) {
			if (FALSE === $this->pageSelect->hidePageForLanguageUid($pageUid, $languageUid, $normalWhenNoLanguage)) {
				$uri = $uriBuilder->setArguments(array('L' => $languageUid))->build();
				$this->tagBuilder->addAttribute('href', $uri);
				$this->tagBuilder->addAttribute('hreflang', $languageName);

				$renderedTag = $this->tagBuilder->render();
				if (TRUE === $usePageRenderer) {
					$pageRenderer->addMetaTag($renderedTag);
				} else {
					$output .= $renderedTag . LF;
				}
			}
		}

		if (FALSE === $usePageRenderer) {
			return trim($output);
		}
	}

}
