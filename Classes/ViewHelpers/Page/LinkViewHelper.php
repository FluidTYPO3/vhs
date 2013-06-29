<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due, Wildside A/S <claus@wildside.dk>
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
 * ### Page: Link ViewHelper
 *
 * Viewhelper for rendering page links
 *
 * This viewhelper acts identically to Fluid's link viewhelper except
 * it fetches the link text for the provided page UID which is to be
 * omitted obviously: <v:page.link pageUid="UID" />. The viewhelper
 * is l18n aware.
 *
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class Tx_Vhs_ViewHelpers_Page_LinkViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * @var Tx_Vhs_Service_PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param Tx_Vhs_Service_PageSelectService $pageSelect
	 * @return void
	 */
	public function injectPageSelectService(Tx_Vhs_Service_PageSelectService $pageSelect) {
		$this->pageSelect = $pageSelect;
	}

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('target', 'string', 'Target of link', FALSE);
		$this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document', FALSE);
		$this->registerArgument('titleFields', 'string', 'CSV list of fields to use as link label - default is "nav_title,title", change to for example "tx_myext_somefield,subtitle,nav_title,title". The first field that contains text will be used. Field value resolved AFTER page field overlays.', FALSE, 'nav_title,title');
	}

	/**
	 * @param integer $pageUid target page. See TypoLink destination
	 * @param array $additionalParams query parameters to be attached to the resulting URI
	 * @param integer $pageType type of the target page. See typolink.parameter
	 * @param boolean $noCache set this to disable caching for the target page. You should not need this.
	 * @param boolean $noCacheHash set this to supress the cHash query parameter created by TypoLink. You should not need this.
	 * @param string $section the anchor to be added to the URI
	 * @param boolean $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
	 * @param boolean $absolute If set, the URI of the rendered link is absolute
	 * @param boolean $addQueryString If set, the current query parameters will be kept in the URI
	 * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
	 * @return NULL|string Rendered page URI
	 */
	public function render($pageUid = NULL, array $additionalParams = array(), $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $linkAccessRestrictedPages = FALSE, $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array()) {
		if (NULL === $pageUid) {
			return NULL;
		}
		$page = $this->pageSelect->getPage($pageUid);
		if (TRUE === empty($page)) {
			return NULL;
		}
		$title = $this->getTitleValue($page);
		$getLL = $GLOBALS['TSFE']->sys_language_uid;
		$l18nConfig = $page['l18n_cfg'];
		if (0 < $getLL) {
			$pageOverlay = $this->pageSelect->getPageOverlay($pageUid, $getLL);
			if (TRUE === (boolean) t3lib_div::hideIfNotTranslated($l18nConfig) && TRUE === empty($pageOverlay)) {
				return NULL;
			}
			if (NULL !== ($overlayTitle = $this->getTitleValue($pageOverlay))) {
				$title = $overlayTitle;
			}
		}
		$uriBuilder = $this->controllerContext->getUriBuilder();
		$uri = $uriBuilder->reset()->setTargetPageUid($pageUid)->setTargetPageType($pageType)->setNoCache($noCache)->setUseCacheHash(!$noCacheHash)->setSection($section)->setLinkAccessRestrictedPages($linkAccessRestrictedPages)->setArguments($additionalParams)->setCreateAbsoluteUri($absolute)->setAddQueryString($addQueryString)->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)->build();
		$this->tag->addAttribute('href', $uri);
		$this->tag->setContent($title);
		return $this->tag->render();
	}

	/**
	 * @param array $record
	 * @return string
	 */
	private function getTitleValue($record) {
		$title = NULL;
		$titleFieldList = t3lib_div::trimExplode(',', $this->arguments['titleFields']);
		foreach ($titleFieldList as $titleFieldName) {
			if (FALSE === empty($record[$titleFieldName])) {
				$title = $record[$titleFieldName];
				break;
			}
		}
		return $title;
	}
}
