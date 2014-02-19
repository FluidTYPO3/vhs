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
 * Returns the all alternate urls.
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page\Header
 */
class Tx_Vhs_ViewHelpers_Page_Header_AlternateViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var Tx_Vhs_Service_PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder
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
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
		$this->tagBuilder = $this->objectManager->get('TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder');
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
