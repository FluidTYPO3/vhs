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
 * Base class for menu rendering ViewHelpers
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Page\Menu
 */
abstract class Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'ul';

	/**
	 * @var array
	 */
	protected $backups = array('menu', 'rootLine');

	/**
	 * @var array
	 */
	private $backupValues = array();

	/**
	 * @var boolean
	 */
	private $original = TRUE;

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
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('tagName', 'string', 'Tag name to use for enclsing container', FALSE, 'ul');
		$this->registerArgument('tagNameChildren', 'string', 'Tag name to use for child nodes surrounding links. If set to "a" enables non-wrapping mode.', FALSE, 'li');
		$this->registerArgument('entryLevel', 'integer', 'Optional entryLevel TS equivalent of the menu', FALSE, 0);
		$this->registerArgument('levels', 'integer', 'Number of levels to render - setting this to a number higher than 1 (one) will expand menu items that are active, to a depth of $levels starting from $entryLevel', FALSE, 1);
		$this->registerArgument('divider', 'string', 'Optional divider to insert between each menu item. Note that this does not mix well with automatic rendering due to the use of an ul > li structure', FALSE, NULL);
		$this->registerArgument('expandAll', 'boolean', 'If TRUE and $levels > 1 then expands all (not just the active) menu items which have submenus', FALSE, FALSE);
		$this->registerArgument('classActive', 'string', 'Optional class name to add to active links', FALSE, 'active');
		$this->registerArgument('classCurrent', 'string', 'Optional class name to add to current link', FALSE, 'current');
		$this->registerArgument('classHasSubpages', 'string', 'Optional class name to add to links which have subpages', FALSE, 'sub');
		$this->registerArgument('useShortcutUid', 'boolean', 'If TRUE, substitutes the link UID of a shortcut with the target page UID (and thus avoiding redirects) but does not change other data - which is done by using useShortcutData.', FALSE, TRUE);
		$this->registerArgument('useShortcutTarget', 'boolean', 'Optional param for using shortcut target instead of shortcut itself for current link', FALSE, NULL);
		$this->registerArgument('useShortcutData', 'boolean', 'Shortcut to set useShortcutTarget and useShortcutData simultaneously', FALSE, NULL);
		$this->registerArgument('classFirst', 'string', 'Optional class name for the first menu elment', FALSE, '');
		$this->registerArgument('classLast', 'string', 'Optional class name for the last menu elment', FALSE, '');
		$this->registerArgument('substElementUid', 'boolean', 'Optional parameter for wrapping the link with the uid of the page', FALSE, '');
		$this->registerArgument('includeSpacers', 'boolean', 'Wether or not to include menu spacers in the page select query', FALSE, FALSE);
		$this->registerArgument('resolveExclude', 'boolean', 'Exclude link if realurl/cooluri flag tx_realurl_exclude is set', FALSE, FALSE);
		$this->registerArgument('showHidden', 'boolean', 'Include disabled pages into the menu', FALSE, FALSE);
		$this->registerArgument('showHiddenInMenu', 'boolean', 'Include pages that are set to be hidden in menus', FALSE, FALSE);
		$this->registerArgument('showCurrent', 'boolean', 'If FALSE, does not display the current page', FALSE, TRUE);
		$this->registerArgument('linkCurrent', 'boolean', 'If FALSE, does not wrap the current page in a link', FALSE, TRUE);
		$this->registerArgument('linkActive', 'boolean', 'If FALSE, does not wrap with links the titles of pages that are active in the rootline', FALSE, TRUE);
		$this->registerArgument('titleFields', 'string', 'CSV list of fields to use as link label - default is "nav_title,title", change to for example "tx_myext_somefield,subtitle,nav_title,title". The first field that contains text will be used. Field value resolved AFTER page field overlays.', FALSE, 'nav_title,title');
		$this->registerArgument('doktypes', 'mixed', 'CSV list or array of allowed doktypes from constant names or integer values, i.e. 1,254 or DEFAULT,SYSFOLDER,SHORTCUT or just default,sysfolder,shortcut');
		$this->registerArgument('excludeSubpageTypes', 'mixed', 'CSV list or array of doktypes to not consider as subpages. Can be constant names or integer values, i.e. 1,254 or DEFAULT,SYSFOLDER,SHORTCUT or just default,sysfolder,shortcut', FALSE, 'SYSFOLDER');
		$this->registerArgument('deferred', 'boolean', 'If TRUE, does not output the tag content UNLESS a v:page.menu.deferred child ViewHelper is both used and triggered. This allows you to create advanced conditions while still using automatic rendering', FALSE, FALSE);
		$this->registerArgument('as', 'string', 'If used, stores the menu pages as an array in a variable named after this value and renders the tag content. If the tag content is empty automatic rendering is triggered.', FALSE, 'menu');
		$this->registerArgument('rootLineAs', 'string', 'If used, stores the menu root line as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used', FALSE, 'rootLine');
		$this->registerArgument('excludePages', 'mixed', 'Page UIDs to exclude from the menu. Can be CSV, array or an object implementing Traversable.', FALSE, '');
		$this->registerArgument('includeAnchorTitle', 'boolean', 'If TRUE, includes the page title as title attribute on the anchor.', FALSE, TRUE);
	}

	/**
	 * Initialize variables used by the submenu instance recycler. Variables set here
	 * may be read by the Page / Menu / Sub ViewHelper which then automatically repeats
	 * rendering using the exact same arguments but with a new page UID as starting page.
	 * Note that the submenu VieWHelper is only capable of recycling one type of menu at
	 * a time - for example, a List menu nested inside a regular Menu ViewHelper will
	 * simply start another menu rendering completely separate from the parent menu.
	 * @return NULL
	 */
	protected function initalizeSubmenuVariables() {
		if (FALSE === $this->original) {
			return NULL;
		}
		$variables = $this->templateVariableContainer->getAll();
		$this->viewHelperVariableContainer->addOrUpdate('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'parentInstance', $this);
		$this->viewHelperVariableContainer->addOrUpdate('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'variables', $variables);
	}

	/**
	 * @param boolean $original
	 * @return void
	 */
	public function setOriginal($original) {
		$this->original = (boolean) $original;
	}

	/**
	 * @return NULL
	 */
	protected function cleanupSubmenuVariables() {
		if (FALSE === $this->original) {
			return NULL;
		}
		if (FALSE === $this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'parentInstance')) {
			return NULL;
		}
		$this->viewHelperVariableContainer->remove('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'parentInstance');
		$this->viewHelperVariableContainer->remove('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'variables');
	}

	/**
	 * Retrieves a stored, if any, parent instance of a menu. Although only implemented by
	 * the Page / Menu / Sub ViewHelper, placing this method in this abstract class instead
	 * will allow custom menu ViewHelpers to work as sub menu ViewHelpers without being
	 * forced to implement their own variable retrieval or subclass Page / Menu / Sub.
	 * Returns NULL if no parent exists.
	 * @param integer $pageUid UID of page that's the new parent page, overridden in arguments of cloned and recycled menu ViewHelper instance
	 * @return Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper|NULL
	 */
	protected function retrieveReconfiguredParentMenuInstance($pageUid) {
		if (FALSE === $this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'parentInstance')) {
			return NULL;
		}
		/** @var $parentInstance Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper */
		$parentInstance = $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'parentInstance');
		$arguments = $parentInstance->getArguments();
		$arguments['pageUid'] = $pageUid;
		$parentInstance->setArguments($arguments);
		return $parentInstance;
	}

	/**
	 * @return void
	 */
	protected function cleanTemplateVariableContainer() {
		if (FALSE === $this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'variables')) {
			return;
		}
		$storedVariables = $this->viewHelperVariableContainer->get('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'variables');
		foreach ($this->templateVariableContainer->getAll() as $variableName => $value) {
			$this->backupValues[$variableName] = $value;
			$this->templateVariableContainer->remove($variableName);
		}
		foreach ($storedVariables as $variableName => $value) {
			$this->templateVariableContainer->add($variableName, $value);
		}
	}

	/**
	 * @return array
	 */
	public function getArguments() {
		if (FALSE === is_array($this->arguments)) {
			return $this->arguments->toArray();
		}
		return $this->arguments;
	}

	/**
	 * @return boolean
	 */
	protected function shouldUseShortcutTarget() {
		$useShortcutTarget = (boolean) $this->arguments['useShortcutData'];
		if (TRUE === $this->hasArgument('useShortcutTarget')) {
			$useShortcutTarget = (boolean) $this->arguments['useShortcutTarget'];
		}
		return $useShortcutTarget;
	}

	/**
	 * @return boolean
	 */
	protected function shouldUseShortcutUid() {
		$useShortcutUid = (boolean) $this->arguments['useShortcutData'];
		if (TRUE === $this->hasArgument('useShortcutUid')) {
			$useShortcutUid = (boolean) $this->arguments['useShortcutUid'];
		}
		return $useShortcutUid;
	}

	/**
	 * @param integer $pageUid
	 * @return boolean
	 */
	protected function isCurrent($pageUid) {
		return (boolean) ($pageUid == $GLOBALS['TSFE']->id);
	}

	/**
	 * @param integer $pageUid
	 * @param array $rootLine
	 * @param integer $originalPageUid
	 * @return boolean
	 */
	protected function isActive($pageUid, $rootLine, $originalPageUid = NULL) {
		if (NULL !== $originalPageUid && $pageUid !== $originalPageUid) {
			$pageUid = $originalPageUid;
		}
		foreach ($rootLine as $page) {
			if ($page['uid'] == $pageUid) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Get a list from allowed doktypes for pages
	 *
	 * @return array
	 */
	protected function allowedDoktypeList() {
		if (TRUE === isset($this->arguments['doktypes']) && FALSE === empty($this->arguments['doktypes'])) {
			$types = $this->parseDoktypeList($this->arguments['doktypes']);
		} else {
			$types = array(
				constant('\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_DEFAULT'),
				constant('\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_LINK'),
				constant('\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SHORTCUT'),
				constant('\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_MOUNTPOINT')
			);
		}
		if ($this->arguments['includeSpacers'] && FALSE === in_array(constant('\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SPACER'), $types)) {
			array_push($types, constant('\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SPACER'));
		}
		return $types;
	}

	/**
	 * Parses the provided CSV list or array of doktypes to
	 * return an array of integers
	 *
	 * @param mixed $doktypes
	 * @return array
	 */
	protected function parseDoktypeList($doktypes) {
		if (TRUE === is_array($doktypes)) {
			$types = $doktypes;
		} else {
			$types = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $doktypes);
		}
		$parsed = array();
		foreach ($types as $index => $type) {
			if (FALSE === ctype_digit($type)) {
				$typeNumber = constant('\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_' . strtoupper($type));
				if (NULL !== $typeNumber) {
					$parsed[$index] = $typeNumber;
				}
			} else {
				$parsed[$index] = intval($type);
			}
		}
		return $parsed;
	}

	/**
	 * @param array $page
	 * @return string
	 */
	protected function getItemTitle($page) {
		$title = $page['title'];
		$titleFieldList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->arguments['titleFields']);
		foreach ($titleFieldList as $titleFieldName) {
			if (empty($page[$titleFieldName]) === FALSE) {
				$title = $page[$titleFieldName];
				break;
			}
		}
		return $title;
	}

	/**
	 * Get the combined item CSS class based on menu item state and VH arguments
	 *
	 * @param array $pageRow
	 * @return array
	 */
	protected function getItemClass($pageRow) {
		$class = array();
		if ($pageRow['active']) {
			$class[] = $this->arguments['classActive'];
		}
		if ($pageRow['current']) {
			$class[] = $this->arguments['classCurrent'];
		}
		if ($pageRow['hasSubPages']) {
			$class[] = $this->arguments['classHasSubpages'];
		}
		return $class;
	}

	/**
	 * Create the href of a link for page $pageUid respecting
	 * a possible shortcut UID
	 *
	 * @param integer $pageUid
	 * @param integer $doktype
	 * @param integer $shortcut
	 * @return string
	 */
	protected function getItemLink($pageUid, $doktype, $shortcut) {
		$isShortcutOrLink = ($doktype == \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SHORTCUT || $doktype == \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_LINK);
		$useShortcutTarget = $this->shouldUseShortcutTarget();
		if (TRUE === $isShortcutOrLink && TRUE === $useShortcutTarget && 0 < $shortcut) {
			$pageUid = $shortcut;
		}
		$config = array(
			'parameter' => $pageUid,
			'returnLast' => 'url',
			'additionalParams' => '',
			'useCacheHash' => FALSE
		);
		return $GLOBALS['TSFE']->cObj->typoLink('', $config);
	}

	/**
	 * Returns submenu for provided UID respecting
	 * possible subpage types to exclude
	 *
	 * @param integer $pageUid
	 * @return array
	 */
	protected function getSubmenu($pageUid) {
		$where = '';
		if (NULL !== $this->arguments['excludeSubpageTypes'] && FALSE === empty($this->arguments['excludeSubpageTypes'])) {
			$excludeSubpageTypes = $this->parseDoktypeList($this->arguments['excludeSubpageTypes']);
			if (0 < count($excludeSubpageTypes)) {
				$where .= ' AND doktype NOT IN (' . implode(',', $excludeSubpageTypes) . ')';
			}
		}
		return $this->getMenu($pageUid, $where);
	}

	/**
	 * @param array $page
	 * @param array $rootLine
	 * @throws Exception
	 * @return array
	 */
	protected function getMenuItemEntry($page, $rootLine) {
		$getLL = $GLOBALS['TSFE']->sys_language_uid;
		$pageUid = $page['uid'];
		// keep a backup of the original page UID to determine 'active' state
		$originalPageUid = $page['uid'];
		// first, ensure the complete data array is present
		$page = $this->pageSelect->getPage($pageUid);
		$targetPage = NULL;
		if ($page['doktype'] == \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SHORTCUT) {
			switch ($page['shortcut_mode']) {
				case 3:
					// mode: parent page of current page (using PID of current page)
					$targetPage = $this->pageSelect->getPage($page['pid']);
					if ($page['pid'] == $GLOBALS['TSFE']->id) {
						array_push($rootLine, $page);
					}
					break;
				case 2:
					// mode: random subpage of selected or current page
					$menu = $this->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $pageUid);
					$targetPage = count($menu) > 0 ? $menu[array_rand($menu)] : $page;
					break;
				case 1:
					// mode: first subpage of selected or current page
					$menu = $this->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $pageUid);
					$targetPage = count($menu) > 0 ? reset($menu) : $page;
					break;
				case 0:
				default:
					$targetPage = $this->pageSelect->getPage($page['shortcut']);
			}
			if (TRUE === (boolean) $this->shouldUseShortcutTarget()) {
				// overwrite current page data with shortcut page data
				$page = $targetPage;
			}
			if (TRUE === (boolean) $this->shouldUseShortcutUid()) {
				// overwrite current page UID
				$pageUid = $targetPage['uid'];
			}
		}

		if (0 < $getLL) {
			$pageOverlay = $this->pageSelect->getPageOverlay($page['uid'], $getLL);
			foreach ($pageOverlay as $name => $value) {
				if (empty($value) === FALSE) {
					$page[$name] = $value;
				}
			}
		}

		$doktype = (integer) $page['doktype'];
		$shortcut = ($doktype == \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SHORTCUT) ? $page['shortcut'] : $page['url'];
		$page['active'] = $this->isActive($pageUid, $rootLine, $originalPageUid);
		$page['current'] = $this->isCurrent($pageUid);
		$page['hasSubPages'] = 0 < count($this->getSubmenu($originalPageUid));
		$page['link'] = $this->getItemLink($pageUid, $doktype, $shortcut);
		$page['linktext'] = $this->getItemTitle($page);
		$page['class'] = implode(' ', $this->getItemClass($page));
		$page['doktype'] = $doktype;

		if ($doktype == \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_LINK) {
			$urlTypes = array(
				'1' => 'http://',
				'4' => 'https://',
				'2' => 'ftp://',
				'3' => 'mailto:'
			);
			$page['link'] = $urlTypes[$page['urltype']] . $page['url'];
		}

		return $page;
	}

	/**
	 * Filter the fetched menu according to visibility etc.
	 *
	 * @param array $menu
	 * @param array $rootLine
	 * @return array
	 */
	protected function parseMenu($menu, $rootLine) {
		$classFirst = $this->arguments['classFirst'];
		$classLast = $this->arguments['classLast'];
		$filtered = array();
		$allowedDocumentTypes = $this->allowedDoktypeList();
		foreach ($menu as $page) {
			if ($page['hidden'] == 1) {
				continue;
			} elseif (TRUE === (boolean) $page['nav_hide'] && FALSE === (boolean) $this->arguments['showHidden']) {
				continue;
			} elseif (TRUE === isset($page['tx_realurl_exclude']) && $page['tx_realurl_exclude'] == 1 && $this->arguments['resolveExclude'] == 1) {
				continue;
			} elseif (TRUE === isset($page['tx_cooluri_exclude']) && $page['tx_cooluri_exclude'] == 1 && $this->arguments['resolveExclude'] == 1) {
				continue;
			} elseif (TRUE === $this->pageSelect->hidePageForLanguageUid($page['uid'], $GLOBALS['TSFE']->sys_language_uid)) {
				continue;
			} elseif (in_array($page['doktype'], $allowedDocumentTypes)) {
				$page = $this->getMenuItemEntry($page, $rootLine);
				if (TRUE === (boolean) $page['nav_hide'] && FALSE === (boolean) $this->arguments['showHidden']) {
					continue;
				}
				$filtered[$page['uid']] = $page;
			}
		}
		$length = count($filtered);
		if ($length > 0) {
			$idx = 1;
			foreach ($filtered as $uid => $page) {
				switch ($idx) {
					case 1:
						$filtered[$uid]['class'] = trim($filtered[$uid]['class'] . ' ' . $classFirst);
						break;
					case $length:
						$filtered[$uid]['class'] = trim($filtered[$uid]['class'] . ' ' . $classLast);
						break;
				}
				$idx++;
			}
		}
		return $filtered;
	}

	/**
	 * Automatically render a menu
	 *
	 * @param array $menu
	 * @param integer $level
	 * @return string
	 */
	protected function autoRender($menu, $level = 1) {
		$tagName = $this->arguments['tagNameChildren'];
		$this->tag->setTagName($this->getWrappingTagName());
		$substElementUid = $this->arguments['substElementUid'];
		$linkCurrent = (boolean) $this->arguments['linkCurrent'];
		$linkActive = (boolean) $this->arguments['linkActive'];
		$showCurrent = (boolean) $this->arguments['showCurrent'];
		$expandAll = (boolean) $this->arguments['expandAll'];
		$maxLevels = (integer) $this->arguments['levels'];
		$includeAnchorTitle = (boolean) $this->arguments['includeAnchorTitle'];
		$html = array();
		$itemsRendered = 0;
		$numberOfItems = count($menu);
		$includedPages = array();
		foreach ($menu as $page) {
			if ($page['current'] && !$showCurrent) {
				continue;
			}
			$class = trim($page['class']) != '' ? ' class="' . $page['class'] . '"' : '';
			$elementId = $substElementUid ? ' id="elem_' . $page['uid'] . '"' : '';
			$target = $page['target'] != '' ? ' target="' . $page['target'] . '"' : '';
			if (FALSE === $this->isNonWrappingMode()) {
				$html[] = '<' . $tagName . $elementId . $class . '>';
			}
			if ($page['current'] && $linkCurrent === FALSE) {
				$html[] = htmlspecialchars($page['linktext']);
			} elseif ($page['active'] && $linkActive === FALSE) {
				$html[] = htmlspecialchars($page['linktext']);
			} elseif (TRUE === $includeAnchorTitle) {
				$html[] = sprintf('<a href="%s" title="%s"%s%s>%s</a>', $page['link'], htmlspecialchars($page['title']), $class, $target, htmlspecialchars($page['linktext']));
			} else {
				$html[] = sprintf('<a href="%s"%s%s>%s</a>', $page['link'], $class, $target, htmlspecialchars($page['linktext']));
			}
			if (($page['active'] || $expandAll) && $page['hasSubPages'] && $level < $maxLevels) {
				$pageUid = $page['uid'];
				$rootLineData = $this->pageSelect->getRootLine();
				$subMenuData = $this->getMenu($pageUid);
				$subMenu = $this->parseMenu($subMenuData, $rootLineData);
				$renderedSubMenu = $this->autoRender($subMenu, $level + 1);
				$this->tag->setTagName($this->getWrappingTagName());
				$this->tag->setContent($renderedSubMenu);
				$this->tag->addAttribute('class', ($this->arguments['class'] ? $this->arguments['class'] . ' lvl-' : 'lvl-') . strval($level));
				$html[] = $this->tag->render();
				$this->tag->addAttribute('class', $this->arguments['class']);
				array_push($includedPages, $page);
			}
			if (FALSE === $this->isNonWrappingMode()) {
				$html[] = '</' . $tagName . '>';
			}
			$itemsRendered++;
			if (TRUE === isset($this->arguments['divider']) && $itemsRendered < $numberOfItems) {
				$html[] = $this->arguments['divider'];
			}
		}
		$content = implode(LF, $html);
		return $content;
	}

	/**
	 * Returns the wrapping tag to use
	 *
	 * @return string
	 */
	public function getWrappingTagName() {
		return $this->isNonWrappingMode() ? 'nav' : $this->arguments['tagName'];
	}

	/**
	 * Returns TRUE for non-wrapping mode which is triggered
	 * by setting tagNameChildren to 'a'
	 *
	 * @return boolean
	 */
	public function isNonWrappingMode() {
		return 'a' == strtolower($this->arguments['tagNameChildren']);
	}

	/**
	 * Saves copies of all template variables while rendering
	 * the menu
	 *
	 * @return void
	 */
	public function backupVariables() {
		foreach ($this->backups as $var) {
			if ($this->templateVariableContainer->exists($var)) {
				$this->backupValues[$var] = $this->templateVariableContainer->get($var);
				$this->templateVariableContainer->remove($var);
			}
		}
	}

	/**
	 * Restores all saved template variables
	 *
	 * @return void
	 */
	public function restoreVariables() {
		if (count($this->backupValues) > 0) {
			foreach ($this->backupValues as $var => $value) {
				if (FALSE === $this->templateVariableContainer->exists($var)) {
					$this->templateVariableContainer->add($var, $value);
				}
			}
		}
	}

	/**
	 * Renders the tag's content or if omitted auto
	 * renders the menu for the provided arguments
	 *
	 * @param array $menu
	 * @return string
	 */
	public function renderContent($menu) {
		$deferredRendering = (boolean) $this->arguments['deferred'];
		if (0 === count($menu) && FALSE === $deferredRendering) {
			return NULL;
		}
		$this->tag->setTagName($this->getWrappingTagName());
		$this->tag->forceClosingTag(TRUE);
		if (TRUE === $deferredRendering) {
			$tagContent = $this->autoRender($menu);
			$this->tag->setContent($tagContent);
			$deferredContent = $this->tag->render();
			$this->viewHelperVariableContainer->addOrUpdate('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredString', $deferredContent);
			$this->viewHelperVariableContainer->addOrUpdate('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredArray', $menu);
			$output = $this->renderChildren();
			$this->unsetDeferredVariableStorage();
		} else {
			$content = $this->renderChildren();
			if (0 < strlen(trim($content))) {
				$output = $content;
			} else {
				$tagContent = $this->autoRender($menu);
				$this->tag->setContent($tagContent);
				$output = $this->tag->render();
			}
		}
		return $output;
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$pageUid = $this->arguments['pageUid'];
		$rootLineData = $this->pageSelect->getRootLine();
		$entryLevel = (integer) $this->arguments['entryLevel'];
		if (0 > $entryLevel) {
			$entryLevel = count($rootLineData) + $entryLevel - 1;
		}
		if (TRUE === empty($pageUid)) {
			if (NULL !== $rootLineData[$entryLevel]['uid']) {
				$pageUid = $rootLineData[$entryLevel]['uid'];
			} else {
				return '';
			}
		}
		$menuData = $this->getMenu($pageUid);
		$menu = $this->parseMenu($menuData, $rootLineData);
		$rootLine = $this->parseMenu($rootLineData, $rootLineData);
		$this->cleanupSubmenuVariables();
		$this->cleanTemplateVariableContainer();
		$this->backupVariables();
		$this->templateVariableContainer->add($this->arguments['as'], $menu);
		$this->templateVariableContainer->add($this->arguments['rootLineAs'], $rootLine);
		$this->initalizeSubmenuVariables();
		$output = $this->renderContent($menu);
		$this->cleanupSubmenuVariables();
		$this->templateVariableContainer->remove($this->arguments['as']);
		$this->templateVariableContainer->remove($this->arguments['rootLineAs']);
		$this->restoreVariables();
		return $output;
	}

	/**
	 * @param $pageUid
	 * @param string $where
	 * @return array
	 */
	public function getMenu($pageUid, $where = '') {
		$excludePages = $this->processPagesArgument($this->arguments['excludePages']);
		$showHidden = (boolean) $this->arguments['showHidden'];
		$showHiddenInMenu = (boolean) $this->arguments['showHiddenInMenu'];
		$menuData = $this->pageSelect->getMenu($pageUid, $showHidden, $excludePages, $where, $showHiddenInMenu, FALSE);
		return $menuData;
	}

	/**
	 * Returns array of page UIDs from provided pages
	 *
	 * @param mixed $pages
	 * @return array
	 */
	public function processPagesArgument($pages = NULL) {
		if (NULL === $pages) {
			$pages = $this->arguments['pages'];
		}
		if ($pages instanceof Traversable) {
			$pages = iterator_to_array($pages);
		} elseif (is_string($pages)) {
			$pages = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $pages, TRUE);
		}
		if (FALSE === is_array($pages)) {
			return array();
		}
		return $pages;
	}

	/**
	 * @return void
	 */
	protected function unsetDeferredVariableStorage() {
		if (TRUE === $this->viewHelperVariableContainer->exists('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredString')) {
			$this->viewHelperVariableContainer->remove('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredString');
			$this->viewHelperVariableContainer->remove('Tx_Vhs_ViewHelpers_Page_Menu_AbstractMenuViewHelper', 'deferredArray');
		}
	}

}
