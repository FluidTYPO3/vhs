<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Dominic Garms <djgarms@gmail.com>, DMFmedia GmbH
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper for rendering TYPO3 menus in Fluid
 * Require the extension static_info_table
 *
 * @author Dominic Garms, DMFmedia GmbH
 * @package Vhs
 * @subpackage ViewHelpers/Page
 */
class LanguageMenuViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var array
	 */
	protected $languageMenu = array();

	/**
	 * @var integer
	 */
	protected $defaultLangUid = 0;

	/**
	 * @var string
	 */
	protected $tagName = 'ul';

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $cObj;

	/**
	 * Initialize
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('tagName', 'string', 'Tag name to use for enclosing container, list and flags (not finished) only', FALSE, 'ul');
		$this->registerArgument('tagNameChildren', 'string', 'Tag name to use for child nodes surrounding links, list and flags only', FALSE, 'li');
		$this->registerArgument('defaultIsoFlag', 'string', 'ISO code of the default flag', FALSE, 'gb');
		$this->registerArgument('defaultLanguageLabel', 'string', 'Label for the default language', FALSE, 'English');
		$this->registerArgument('order', 'mixed', 'Orders the languageIds after this list', FALSE, '');
		$this->registerArgument('labelOverwrite', 'mixed', 'Overrides language labels', FALSE, '');
		$this->registerArgument('hideNotTranslated', 'boolean', 'Hides languageIDs which are not translated', FALSE, FALSE);
		$this->registerArgument('layout', 'string', 'How to render links when using autorendering. Possible selections: name,flag - use fx "name" or "flag,name" or "name,flag"', FALSE, 'flag,name');
		$this->registerArgument('useCHash', 'boolean', 'Use cHash for typolink', FALSE, TRUE);
		$this->registerArgument('flagPath', 'string', 'Overwrites the path to the flag folder', FALSE, 'typo3/sysext/t3skin/images/flags/');
		$this->registerArgument('flagImageType', 'string', 'Sets type of flag image: png, gif, jpeg', FALSE, 'png');
		$this->registerArgument('linkCurrent', 'boolean', 'Sets flag to link current language or not', FALSE, TRUE);
		$this->registerArgument('classCurrent', 'string', 'Sets the class, by which the current language will be marked', FALSE, 'current');
		$this->registerArgument('as', 'string', 'If used, stores the menu pages as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used', FALSE, 'languageMenu');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		if (FALSE === is_object($GLOBALS['TSFE']->sys_page)) {
			return NULL;
		}
		$this->cObj = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		$this->tagName = $this->arguments['tagName'];

		// to set the tagName we should call initialize()
		$this->initialize();

		$this->languageMenu = $this->parseLanguageMenu($this->arguments['order'], $this->arguments['labelOverwrite']);
		$this->templateVariableContainer->add($this->arguments['as'], $this->languageMenu);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove($this->arguments['as']);
		if (0 === strlen(trim($content))) {
			$content = $this->autoRender($this->languageMenu);
		}
		return $content;
	}

	/**
	 * Automatically render a language menu
	 *
	 * @return string
	 */
	protected function autoRender() {
		$content = $this->getLanguageMenu();
		$content = trim($content);
		if (FALSE === empty($content)) {
			$this->tag->setContent($content);
			$content = $this->tag->render();
		}
		return $content;
	}

	/**
	 * Get layout 0 (default): list
	 *
	 * @return    string
	 */
	protected function getLanguageMenu() {
		$tagName = $this->arguments['tagNameChildren'];
		$html = array();
		$itemCount = count($this->languageMenu);
		foreach ($this->languageMenu as $index => $var) {
			$class = '';
			$classes = array();
			if (TRUE === (boolean) $var['inactive']) {
				$classes[] = 'inactive';
			} elseif (TRUE === (boolean) $var['current']) {
				$classes[] = $this->arguments['classCurrent'];
			}
			if (0 === $index) {
				$classes[] = 'first';
			} elseif (($itemCount - 1) === $index) {
				$classes[] = 'last';
			}
			if (0 < count($classes)) {
				$class = ' class="' . implode(' ', $classes) . '" ';
			}
			if (TRUE === (boolean) $var['current'] && FALSE === (boolean) $this->arguments['linkCurrent']) {
				$html[] = '<' . $tagName . $class . '>' . $this->getLayout($var) . '</' . $tagName . '>';
			} else {
				$html[] = '<' . $tagName . $class . '><a href="' . htmlspecialchars($var['url']) . '">' . $this->getLayout($var) . '</a></' . $tagName . '>';
			}
		}
		return implode(LF, $html);
	}

	/**
	 * Returns the flag source
	 *
	 * @param string $iso
	 * @return string
	 */
	protected function getLanguageFlagSrc($iso) {
		$path = trim($this->arguments['flagPath']);
		$imgType = trim($this->arguments['flagImageType']);
		$img = $path . $iso . '.' . $imgType;
		return $img;
	}

	/**
	 * Return the layout: flag & text, flags only or text only
	 *
	 * @param array $language
	 * @return string
	 */
	protected function getLayout(array $language) {
		$flagImage = FALSE !== stripos($this->arguments['layout'], 'flag') ? $this->getFlagImage($language) : '';
		$label = $language['label'];
		switch ($this->arguments['layout']) {
			case 'flag':
				$html = $flagImage;
				break;
			case 'name':
				$html = $label;
				break;
			case 'name,flag':
				$html = $label;
				if ('' !== $flagImage) {
					$html .= '&nbsp;' . $flagImage;
				}
				break;
			case 'flag,name':
			default:
				if ('' !== $flagImage) {
					$html = $flagImage . '&nbsp;' . $label;
				} else {
					$html = $label;
				}
		}
		return $html;
	}

	/**
	 * Render the flag image for autorenderer
	 *
	 * @param array $language
	 * @return string
	 */
	protected function getFlagImage(array $language) {
		$conf = array(
			'file' => $language['flagSrc'],
			'altText' => $language['label'],
			'titleText' => $language['label']
		);
		return $this->cObj->IMAGE($conf);
	}

	/**
	 * Sets all parameter for langMenu
	 *
	 * @return array
	 */
	protected function parseLanguageMenu() {
		$order = $this->arguments['order'] ? GeneralUtility::trimExplode(',', $this->arguments['order']) : '';
		$labelOverwrite = $this->arguments['labelOverwrite'] ? GeneralUtility::trimExplode(',', $this->arguments['labelOverwrite']) : '';

		$tempArray = $languageMenu = array();

		$tempArray[0] = array(
			'label' => $this->arguments['defaultLanguageLabel'],
			'flag' => $this->arguments['defaultIsoFlag']
		);

		$select = 'uid, title, flag';
		$from = 'sys_language';
		$where = '1=1' . $this->cObj->enableFields('sys_language');
		$sysLanguage = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select, $from, $where);

		foreach ($sysLanguage as $value) {
			$tempArray[$value['uid']] = array(
				'label' => $value['title'],
				'flag' => $value['flag'],
			);
		}

		// reorders languageMenu
		if (FALSE === empty($order)) {
			foreach ($order as $value) {
				$languageMenu[$value] = $tempArray[$value];
			}
		} else {
			$languageMenu = $tempArray;
		}

		// overwrite of label
		if (FALSE === empty($labelOverwrite)) {
			$i = 0;
			foreach ($languageMenu as $key => $value) {
				$languageMenu[$key]['label'] = $labelOverwrite[$i];
				$i++;
			}
		}

		// Select all pages_language_overlay records on the current page. Each represents a possibility for a language.
		$pageArray = array();
		$table = 'pages_language_overlay';
		$whereClause = 'pid=' . $GLOBALS['TSFE']->id . ' ';
		$whereClause .= $GLOBALS['TSFE']->sys_page->enableFields($table);
		$sysLang = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('DISTINCT sys_language_uid', $table, $whereClause);

		if (FALSE === empty($sysLang)) {
			foreach ($sysLang as $val) {
				$pageArray[$val['sys_language_uid']] = $val['sys_language_uid'];
			}
		}

		foreach ($languageMenu as $key => $value) {
			$current = $GLOBALS['TSFE']->sys_language_uid === (integer) $key ? 1 : 0;
			$inactive = $pageArray[$key] || (integer) $key === $this->defaultLangUid ? 0 : 1;
			$languageMenu[$key]['current'] = $current;
			$languageMenu[$key]['inactive'] = $inactive;
			$languageMenu[$key]['url'] = TRUE === (boolean) $current ? GeneralUtility::getIndpEnv('REQUEST_URI') : $this->getLanguageUrl($key, $inactive);
			$languageMenu[$key]['flagSrc'] = $this->getLanguageFlagSrc($value['flag']);
			if (TRUE === (boolean) $this->arguments['hideNotTranslated'] && TRUE === (boolean) $inactive) {
				unset($languageMenu[$key]);
			}
		}

		return $languageMenu;
	}

	/**
	 * Get link of language menu entry
	 *
	 * @param $uid
	 * @return string
	 */
	protected function getLanguageUrl($uid) {
		$getValues = GeneralUtility::_GET();
		$getValues['L'] = $uid;
		$currentPage = $GLOBALS['TSFE']->id;
		unset($getValues['id']);
		unset($getValues['cHash']);
		$addParams = http_build_query($getValues);
		$config = array(
			'parameter' => $currentPage,
			'returnLast' => 'url',
			'additionalParams' => '&' . $addParams,
			'useCacheHash' => $this->arguments['useCHash']
		);
		return $this->cObj->typoLink('', $config);
	}

}
