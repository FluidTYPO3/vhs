<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

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
use FluidTYPO3\Vhs\Service\PageSelectService;
use FluidTYPO3\Vhs\Utility\ViewHelperUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ### Page: Link ViewHelper
 *
 * Viewhelper for rendering page links
 *
 * This viewhelper behaves identically to Fluid's link viewhelper
 * except for it fetches the title of the provided page UID and inserts
 * it as linktext if that is omitted. The link will not render at all
 * if the requested page is not translated in the current language.
 *
 * Automatic linktext: <v:page.link pageUid="UID" />
 * Manual linktext:    <v:page.link pageUid="UID">linktext</v:page.link>
 *
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Page
 */
class LinkViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param PageSelectService $pageSelect
	 * @return void
	 */
	public function injectPageSelectService(PageSelectService $pageSelect) {
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
		$this->registerArgument('pageUid', 'integer', 'UID of the page to create the link and fetch the title for.', FALSE, 0);
		$this->registerArgument('additionalParams', 'array', 'Query parameters to be attached to the resulting URI', FALSE, array());
		$this->registerArgument('pageType', 'integer', 'Type of the target page. See typolink.parameter', FALSE, 0);
		$this->registerArgument('noCache', 'boolean', 'When TRUE disables caching for the target page. You should not need this.', FALSE, FALSE);
		$this->registerArgument('noCacheHash', 'boolean', 'When TRUE supresses the cHash query parameter created by TypoLink. You should not need this.', FALSE, FALSE);
		$this->registerArgument('section', 'string', 'The anchor to be added to the URI', FALSE, '');
		$this->registerArgument('linkAccessRestrictedPages', 'boolean', 'When TRUE, links pointing to access restricted pages will still link' .
			'to the page even though the page cannot be accessed.', FALSE, FALSE);
		$this->registerArgument('absolute', 'boolean', 'When TRUE, the URI of the rendered link is absolute', FALSE, FALSE);
		$this->registerArgument('addQueryString', 'boolean', 'When TRUE, the current query parameters will be kept in the URI', FALSE, FALSE);
		$this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'Arguments to be removed from the URI. Only active if $addQueryString = TRUE', FALSE, array());
		$this->registerArgument('titleFields', 'string', 'CSV list of fields to use as link label - default is "nav_title,title", change to' .
			'for example "tx_myext_somefield,subtitle,nav_title,title". The first field that contains text will be used. Field value resolved' .
			'AFTER page field overlays.', FALSE, 'nav_title,title');
		$this->registerArgument('pageTitleAs', 'string', 'When rendering child content, supplies page title as variable.', FALSE, NULL);
	}

	/**
	 * Render method
	 * @return NULL|string
	 */
	public function render() {
		// Check if link wizard link
		$pageUid = $this->arguments['pageUid'];
		$additionalParameters = $this->arguments['additionalParams'];
		if (FALSE === is_numeric($pageUid)) {
			$linkConfig = GeneralUtility::unQuoteFilenames($pageUid, TRUE);
			if (TRUE === isset($linkConfig[0])) {
				$pageUid = $linkConfig[0];
			}
			if (TRUE === isset($linkConfig[1]) && '-' !== $linkConfig[1]) {
				$this->tag->addAttribute('target', $linkConfig[1]);
			}
			if (TRUE === isset($linkConfig[2]) && '-' !== $linkConfig[2]) {
				$this->tag->addAttribute('class', $linkConfig[2]);
			}
			if (TRUE === isset($linkConfig[3]) && '-' !== $linkConfig[3]) {
				$this->tag->addAttribute('title', $linkConfig[3]);
			}
			if (TRUE === isset($linkConfig[4]) && '-' !== $linkConfig[4]) {
				$additionalParametersString = trim($linkConfig[4], '&');
				$additionalParametersArray = GeneralUtility::trimExplode('&', $additionalParametersString);
				foreach ($additionalParametersArray as $parameter) {
					list($key, $value) = GeneralUtility::trimExplode('=', $parameter);
					$additionalParameters[$key] = $value;
				}
			}
		}

		// Get page via pageUid argument or current id
		$pageUid = intval($pageUid);
		if (0 === $pageUid) {
			$pageUid = $GLOBALS['TSFE']->id;
		}

		$page = $this->pageSelect->getPage($pageUid);
		if (TRUE === empty($page)) {
			return NULL;
		}

		// Do not render the link, if the page should be hidden
		$currentLanguageUid = $GLOBALS['TSFE']->sys_language_uid;
		$hidePage = $this->pageSelect->hidePageForLanguageUid($pageUid, $currentLanguageUid);
		if (TRUE === $hidePage) {
			return NULL;
		}

		// Get the title from the page or page overlay
		if (0 < $currentLanguageUid) {
			$pageOverlay = $this->pageSelect->getPageOverlay($pageUid, $currentLanguageUid);
			$title = $this->getTitleValue($pageOverlay);
		} else {
			$title = $this->getTitleValue($page);
		}

		// Check if we should assign page title to the template variable container
		$pageTitleAs = $this->arguments['pageTitleAs'];
		if (FALSE === empty($pageTitleAs)) {
			$variables = array($pageTitleAs => $title);
		} else {
			$variables = array();
		}

		// Render childs to see if an alternative title content should be used
		$renderedTitle = ViewHelperUtility::renderChildrenWithVariables($this, $this->templateVariableContainer, $variables);
		if (FALSE === empty($renderedTitle)) {
			$title = $renderedTitle;
		}

		$uriBuilder = $this->controllerContext->getUriBuilder();
		$uri = $uriBuilder->reset()
			->setTargetPageUid($pageUid)
			->setTargetPageType($this->arguments['pageType'])
			->setNoCache($this->arguments['noCache'])
			->setUseCacheHash(!$this->arguments['noCacheHash'])
			->setSection($this->arguments['section'])
			->setLinkAccessRestrictedPages($this->arguments['linkAccessRestrictedPages'])
			->setArguments($additionalParameters)
			->setCreateAbsoluteUri($this->arguments['absolute'])
			->setAddQueryString($this->arguments['addQueryString'])
			->setArgumentsToBeExcludedFromQueryString($this->arguments['argumentsToBeExcludedFromQueryString'])
			->build();
		$this->tag->addAttribute('href', $uri);
		$this->tag->setContent($title);
		return $this->tag->render();
	}

	/**
	 * @param array $record
	 * @return string
	 */
	private function getTitleValue($record) {
		$titleFieldList = GeneralUtility::trimExplode(',', $this->arguments['titleFields']);
		foreach ($titleFieldList as $titleFieldName) {
			if (FALSE === empty($record[$titleFieldName])) {
				return $record[$titleFieldName];
			}
		}
		return '';
	}

}
