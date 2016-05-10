<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Base class for menu rendering ViewHelpers
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Menu
 */
abstract class AbstractMenuViewHelper extends AbstractTagBasedViewHelper
{

	use TagViewHelperTrait;

	/**
	 * @var string
	 */
	protected $tagName = 'ul';

	/**
	 * @var PageService
	 */
	protected $pageService;

	/**
	 * @var boolean
	 */
	private $original = TRUE;

	/**
	 * @var array
	 */
	private $backupValues = array();

	/**
	 * @param PageService $pageService
	 */
	public function injectPageService(PageService $pageService) {
		$this->pageService = $pageService;
	}

	/**
	 * Initialize
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('tagName', 'string', 'Tag name to use for enclosing container', FALSE, 'ul');
		$this->registerArgument('tagNameChildren', 'string', 'Tag name to use for child nodes surrounding links. If set to "a" enables non-wrapping mode.', FALSE, 'li');
		$this->registerArgument('entryLevel', 'integer', 'Optional entryLevel TS equivalent of the menu', FALSE, 0);
		$this->registerArgument('levels', 'integer', 'Number of levels to render - setting this to a number higher than 1 (one) will expand menu items that are active, to a depth of $levels starting from $entryLevel', FALSE, 1);
		$this->registerArgument('expandAll', 'boolean', 'If TRUE and $levels > 1 then expands all (not just the active) menu items which have submenus', FALSE, FALSE);
		$this->registerArgument('showAccessProtected', 'boolean', 'If TRUE links to access protected pages are always rendered regardless of user login status', FALSE, FALSE);
		$this->registerArgument('classFirst', 'string', 'Optional class name for the first menu elment', FALSE, '');
		$this->registerArgument('classLast', 'string', 'Optional class name for the last menu elment', FALSE, '');
		$this->registerArgument('classActive', 'string', 'Optional class name to add to active links', FALSE, 'active');
		$this->registerArgument('classCurrent', 'string', 'Optional class name to add to current link', FALSE, 'current');
		$this->registerArgument('classHasSubpages', 'string', 'DEPRECATED: Use argument classHasSubPages instead', FALSE);
		$this->registerArgument('classHasSubPages', 'string', 'Optional class name to add to links which have subpages', FALSE, 'sub');
		$this->registerArgument('classAccessProtected', 'string', 'Optional class name to add to links which are access protected', FALSE, 'protected');
		$this->registerArgument('classAccessGranted', 'string', 'Optional class name to add to links which are access protected but access is actually granted', FALSE, 'access-granted');
		$this->registerArgument('substElementUid', 'boolean', 'Optional parameter for wrapping the link with the uid of the page', FALSE, FALSE);
		$this->registerArgument('useShortcutUid', 'boolean', 'If TRUE, substitutes the link UID of a shortcut with the target page UID (and thus avoiding redirects) but does not change other data - which is done by using useShortcutData.', FALSE, FALSE);
		$this->registerArgument('useShortcutTarget', 'boolean', 'Optional param for using shortcut target instead of shortcut itself for current link', FALSE, NULL);
		$this->registerArgument('useShortcutData', 'boolean', 'Shortcut to set useShortcutTarget and useShortcutData simultaneously', FALSE, NULL);
		$this->registerArgument('showHiddenInMenu', 'boolean', 'Include pages that are set to be hidden in menus', FALSE, FALSE);
		$this->registerArgument('showCurrent', 'boolean', 'If FALSE, does not display the current page', FALSE, TRUE);
		$this->registerArgument('linkCurrent', 'boolean', 'If FALSE, does not wrap the current page in a link', FALSE, TRUE);
		$this->registerArgument('linkActive', 'boolean', 'If FALSE, does not wrap with links the titles of pages that are active in the rootline', FALSE, TRUE);
		$this->registerArgument('titleFields', 'string', 'CSV list of fields to use as link label - default is "nav_title,title", change to for example "tx_myext_somefield,subtitle,nav_title,title". The first field that contains text will be used. Field value resolved AFTER page field overlays.', FALSE, 'nav_title,title');
		$this->registerArgument('includeAnchorTitle', 'boolean', 'If TRUE, includes the page title as title attribute on the anchor.', FALSE, TRUE);
		$this->registerArgument('includeSpacers', 'boolean', 'Wether or not to include menu spacers in the page select query', FALSE, FALSE);
		$this->registerArgument('deferred', 'boolean', 'If TRUE, does not output the tag content UNLESS a v:page.menu.deferred child ViewHelper is both used and triggered. This allows you to create advanced conditions while still using automatic rendering', FALSE, FALSE);
		$this->registerArgument('as', 'string', 'If used, stores the menu pages as an array in a variable named after this value and renders the tag content. If the tag content is empty automatic rendering is triggered.', FALSE, 'menu');
		$this->registerArgument('rootLineAs', 'string', 'If used, stores the menu root line as an array in a variable named according to this value and renders the tag content - which means automatic rendering is disabled if this attribute is used', FALSE, 'rootLine');
		$this->registerArgument('excludePages', 'mixed', 'Page UIDs to exclude from the menu. Can be CSV, array or an object implementing Traversable.', FALSE, '');
		$this->registerArgument('forceAbsoluteUrl', 'boolean', 'If TRUE, the menu will be rendered with absolute URLs', FALSE, FALSE);
		$this->registerArgument('doktypes', 'mixed', 'CSV list or array of allowed doktypes from constant names or integer values, i.e. 1,254 or DEFAULT,SYSFOLDER,SHORTCUT or just default,sysfolder,shortcut', FALSE, '');
	}

	/**
	 * @return string
	 */
	public function render() {
		$pages = $this->getMenu($this->arguments['pageUid'], $this->arguments['entryLevel']);
		$menu = $this->parseMenu($pages);
		$rootLine = $this->pageService->getRootLine(
			$this->arguments['pageUid'],
			$this->arguments['reverse'],
			$this->arguments['showAccessProtected']
		);
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
	 * Renders the tag's content or if omitted auto
	 * renders the menu for the provided arguments
	 *
	 * @param array $menu
	 * @return string
	 */
	public function renderContent(array $menu) {
		$deferredRendering = (boolean) $this->arguments['deferred'];
		if (0 === count($menu) && FALSE === $deferredRendering) {
			return NULL;
		}
		if (TRUE === $deferredRendering) {
			$tagContent = $this->autoRender($menu);
			$this->tag->setContent($tagContent);
			$deferredContent = $this->tag->render();
			$this->viewHelperVariableContainer->addOrUpdate(
				'FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'deferredString', $deferredContent
			);
			$this->viewHelperVariableContainer->addOrUpdate(
				'FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'deferredArray', $menu
			);
			$output = $this->renderChildren();
			$this->unsetDeferredVariableStorage();
		} else {
			$content = $this->renderChildren();
			if (0 < strlen(trim($content))) {
				$output = $content;
			} else {
				$output = $this->renderTag($this->getWrappingTagName(), $this->autoRender($menu));
			}
		}

		return $output;
	}

	/**
	 * @param array $menu
	 * @param integer $level
	 * @return string
	 */
	protected function autoRender(array $menu, $level = 1) {
		$tagName = $this->arguments['tagNameChildren'];
		$this->tag->setTagName($this->getWrappingTagName());
		$html = array();
		$levels = (integer) $this->arguments['levels'];
		$showCurrent = (boolean) $this->arguments['showCurrent'];
		$expandAll = (boolean) $this->arguments['expandAll'];
		foreach ($menu as $page) {
			if (TRUE === (boolean) $page['current'] && FALSE === $showCurrent) {
				continue;
			}
			$class = (trim($page['class']) !== '') ? ' class="' . trim($page['class']) . '"' : '';
			$elementId = (TRUE === (boolean) $this->arguments['substElementUid']) ? ' id="elem_' . $page['uid'] . '"' : '';
			if (FALSE === $this->isNonWrappingMode()) {
				$html[] = '<' . $tagName . $elementId . $class . '>';
			}
			$html[] = $this->renderItemLink($page);
			if ((TRUE === (boolean) $page['active'] || TRUE === $expandAll) && TRUE === (boolean) $page['hasSubPages'] && $level < $levels) {
				$subPages = $this->getMenu($page['uid']);
				$subMenu = $this->parseMenu($subPages);
				if (0 < count($subMenu)) {
					$renderedSubMenu = $this->autoRender($subMenu, $level + 1);
					$parentTagId = $this->tag->getAttribute('id');
					if (FALSE === empty($parentTagId)) {
						$this->tag->addAttribute('id', $parentTagId . '-lvl-' . $level);
					}
					$this->tag->setTagName($this->getWrappingTagName());
					$this->tag->setContent($renderedSubMenu);
					$this->tag->addAttribute('class', (FALSE === empty($this->arguments['class']) ? $this->arguments['class'] . ' lvl-' : 'lvl-') . $level);
					$html[] = $this->tag->render();
					$this->tag->addAttribute('class', $this->arguments['class']);
					if (FALSE === empty($parentTagId)) {
						$this->tag->addAttribute('id', $parentTagId);
					}
				}
			}
			if (FALSE === $this->isNonWrappingMode()) {
				$html[] = '</' . $tagName . '>';
			}
		}

		return implode(LF, $html);
	}

	/**
	 * @param array $page
	 * @return string
	 */
	protected function renderItemLink(array $page) {
		$isSpacer = ($page['doktype'] === PageRepository::DOKTYPE_SPACER);
		$isCurrent = (boolean) $page['current'];
		$isActive = (boolean) $page['active'];
		$linkCurrent = (boolean) $this->arguments['linkCurrent'];
		$linkActive = (boolean) $this->arguments['linkActive'];
		$includeAnchorTitle = (boolean) $this->arguments['includeAnchorTitle'];
		$target = (FALSE === empty($page['target'])) ? ' target="' . $page['target'] . '"' : '';
		$class = (trim($page['class']) !== '') ? ' class="' . trim($page['class']) . '"' : '';
		if (TRUE === $isSpacer || (TRUE === $isCurrent && FALSE === $linkCurrent) || (TRUE === $isActive && FALSE === $linkActive)) {
			$html = htmlspecialchars($page['linktext']);
		} elseif (TRUE === $includeAnchorTitle) {
			$html = sprintf('<a href="%s" title="%s"%s%s>%s</a>', $page['link'], htmlspecialchars($page['title']), $class, $target, htmlspecialchars($page['linktext']));
		} else {
			$html = sprintf('<a href="%s"%s%s>%s</a>', $page['link'], $class, $target, htmlspecialchars($page['linktext']));
		}

		return $html;
	}

	/**
	 * @param null|integer $pageUid
	 * @param integer $entryLevel
	 * @return integer
	 */
	protected function determinePageUid($pageUid = NULL, $entryLevel = 0) {
		$rootLineData = $this->pageService->getRootLine();
		if (NULL === $pageUid) {
			if (NULL !== $entryLevel) {
				if (0 > $entryLevel) {
					$entryLevel = count($rootLineData) - 1 + $entryLevel;
				}
				$pageUid = $rootLineData[$entryLevel]['uid'];
			} else {
				$pageUid = $GLOBALS['TSFE']->id;
			}
		}

		return (integer) $pageUid;
	}

	/**
	 * @param null|integer $pageUid
	 * @param integer $entryLevel
	 *
	 * @return array
	 */
	public function getMenu($pageUid = NULL, $entryLevel = 0) {
		$pageUid = $this->determinePageUid($pageUid, $entryLevel);
		$showHiddenInMenu = (boolean) $this->arguments['showHiddenInMenu'];
		$showAccessProtected = (boolean) $this->arguments['showAccessProtected'];
		$includeSpacers = (boolean) $this->arguments['includeSpacers'];
		$excludePages = $this->processPagesArgument($this->arguments['excludePages']);

		return $this->pageService->getMenu($pageUid, $excludePages, $showHiddenInMenu, $includeSpacers, $showAccessProtected);
	}

	/**
	 * @param array $pages
	 * @return array
	 */
	public function parseMenu(array $pages) {
		$count = 0;
		$total = count($pages);
		$allowedDocumentTypes = $this->allowedDoktypeList();
		foreach ($pages as $index => $page) {
			if (!in_array($page['doktype'], $allowedDocumentTypes)) {
				continue;
			}
			if (TRUE === $this->pageService->hidePageForLanguageUid($page)) {
				continue;
			}
			$count++;
			$class = array();
			$originalPageUid = $page['uid'];
			if (TRUE === (boolean) $this->arguments['showAccessProtected']) {
				$pages[$index]['accessProtected'] =  $this->isAccessProtected($page);
				if (TRUE === $pages[$index]['accessProtected']) {
					$class[] = $this->arguments['classAccessProtected'];
				}
				$pages[$index]['accessGranted'] = $this->isAccessGranted($page);
				if (TRUE === $pages[$index]['accessGranted'] && TRUE === $this->isAccessProtected($page)) {
					$class[] = $this->arguments['classAccessGranted'];
				}
			}
			if (PageRepository::DOKTYPE_SHORTCUT === (integer) $page['doktype']) {
				switch ($page['shortcut_mode']) {
					case 3:
						// mode: parent page of current page (using PID of current page)
						$targetPage = $this->pageService->getPage($page['pid']);
						break;
					case 2:
						// mode: random subpage of selected or current page
						$menu = $this->pageService->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $originalPageUid);
						$targetPage = (0 < count($menu)) ? $menu[array_rand($menu)] : $page;
						break;
					case 1:
						// mode: first subpage of selected or current page
						$menu = $this->pageService->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $originalPageUid);
						$targetPage = (0 < count($menu)) ? reset($menu) : $page;
						break;
					case 0:
					default:
						// mode: selected page
						$targetPage = $this->pageService->getPage($page['shortcut']);
				}
				if (TRUE === $this->shouldUseShortcutTarget()) {
					$pages[$index] = $targetPage;
				}
				if (TRUE === $this->shouldUseShortcutUid()) {
					$page['uid'] = $targetPage['uid'];
				}
			}
			if (TRUE === $this->isActive($originalPageUid)) {
				$pages[$index]['active'] = TRUE;
				$class[] = $this->arguments['classActive'];
			}
			if (TRUE === $this->isCurrent($originalPageUid)) {
				$pages[$index]['current'] = TRUE;
				$class[] = $this->arguments['classCurrent'];
			}
			if (0 < count($this->pageService->getMenu($originalPageUid))) {
				$pages[$index]['hasSubPages'] = TRUE;
				//TODO: Remove deprecated argument in next major version
				$class[] = (TRUE === $this->hasArgument('classHasSubpages')) ? $this->arguments['classHasSubpages'] : $this->arguments['classHasSubPages'];
			}
			if (1 === $count) {
				$class[] = $this->arguments['classFirst'];
			}
			if ($count === $total) {
				$class[] = $this->arguments['classLast'];
			}
			$pages[$index]['class'] = implode(' ', $class);
			$pages[$index]['linktext'] = $this->getItemTitle($pages[$index]);
			$pages[$index]['link'] = $this->getItemLink($page);
		}

		return $pages;
	}

	/**
	 * @param array $page
	 * @return string
	 */
	protected function getItemLink(array $page) {
		$forceAbsoluteUrl = (boolean) $this->arguments['forceAbsoluteUrl'];
		$config = array(
			'parameter' => $page['uid'],
			'returnLast' => 'url',
			'additionalParams' => '',
			'useCacheHash' => FALSE,
			'forceAbsoluteUrl' => $forceAbsoluteUrl,
		);

		return $GLOBALS['TSFE']->cObj->typoLink('', $config);
	}

	/**
	 * @param array $page
	 * @return string
	 */
	protected function getItemTitle(array $page) {
		$titleFieldList = GeneralUtility::trimExplode(',', $this->arguments['titleFields']);
		foreach ($titleFieldList as $titleFieldName) {
			if (FALSE === empty($page[$titleFieldName])) {
				return $page[$titleFieldName];
			}
		}

		return $page['title'];
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
		return ((integer) $pageUid === (integer) $GLOBALS['TSFE']->id);
	}

	/**
	 * @param $pageUid
	 * @return boolean
	 */
	protected function isActive($pageUid) {
		$rootLineData = $this->pageService->getRootLine(NULL, FALSE, $this->arguments['showAccessProtected']);
		foreach ($rootLineData as $page) {
			if ((integer) $page['uid'] === (integer) $pageUid) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @param array $page
	 * @return boolean
	 */
	protected function isAccessProtected(array $page) {
		return (0 !== (integer) $page['fe_group']);
	}

	/**
	 * @param array $page
	 * @return boolean
	 */
	protected function isAccessGranted(array $page) {
		if (FALSE === $this->isAccessProtected($page)) {
			return TRUE;
		}

		$groups = explode(',', $page['fe_group']);

		$showPageAtAnyLogin = (TRUE === in_array(-2, $groups));
		$hidePageAtAnyLogin = (TRUE === in_array(-1, $groups));
		$userIsLoggedIn = (TRUE === is_array($GLOBALS['TSFE']->fe_user->user));
		$userGroups = $GLOBALS['TSFE']->fe_user->groupData['uid'];
		$userIsInGrantedGroups = (0 < count(array_intersect($userGroups, $groups)));

		if (
			(FALSE === $userIsLoggedIn && TRUE === $hidePageAtAnyLogin) ||
			(TRUE === $userIsLoggedIn && TRUE === $showPageAtAnyLogin) ||
			(TRUE === $userIsLoggedIn && TRUE === $userIsInGrantedGroups)
		) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Initialize variables used by the submenu instance recycler. Variables set here
	 * may be read by the Page / Menu / Sub ViewHelper which then automatically repeats
	 * rendering using the exact same arguments but with a new page UID as starting page.
	 * Note that the submenu VieWHelper is only capable of recycling one type of menu at
	 * a time - for example, a List menu nested inside a regular Menu ViewHelper will
	 * simply start another menu rendering completely separate from the parent menu.
	 */
	protected function initalizeSubmenuVariables() {
		if (FALSE === $this->original) {
			return NULL;
		}
		$variables = $this->templateVariableContainer->getAll();
		$this->viewHelperVariableContainer->addOrUpdate(
			'FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'parentInstance', $this
		);
		$this->viewHelperVariableContainer->addOrUpdate(
			'FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'variables', $variables
		);
	}

	/**
	 * @param boolean $original
	 */
	public function setOriginal($original) {
		$this->original = (boolean) $original;
	}

	protected function cleanupSubmenuVariables() {
		if (FALSE === $this->original) {
			return NULL;
		}
		if (FALSE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'parentInstance')) {
			return NULL;
		}
		$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'parentInstance');
		$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'variables');
	}

	/**
	 * Saves copies of all template variables while rendering
	 * the menu
	 */
	public function backupVariables() {
		$backups = array($this->arguments['as'], $this->arguments['rootLineAs']);
		foreach ($backups as $var) {
			if (TRUE === $this->templateVariableContainer->exists($var)) {
				$this->backupValues[$var] = $this->templateVariableContainer->get($var);
				$this->templateVariableContainer->remove($var);
			}
		}
	}

	/**
	 * Restores all saved template variables
	 */
	public function restoreVariables() {
		if (0 < count($this->backupValues)) {
			foreach ($this->backupValues as $var => $value) {
				if (FALSE === $this->templateVariableContainer->exists($var)) {
					$this->templateVariableContainer->add($var, $value);
				}
			}
		}
	}

	/**
	 * Retrieves a stored, if any, parent instance of a menu. Although only implemented by
	 * the Page / Menu / Sub ViewHelper, placing this method in this abstract class instead
	 * will allow custom menu ViewHelpers to work as sub menu ViewHelpers without being
	 * forced to implement their own variable retrieval or subclass Page / Menu / Sub.
	 * Returns NULL if no parent exists.
	 * @param integer $pageUid UID of page that's the new parent page, overridden in arguments of cloned and recycled menu ViewHelper instance
	 * @return AbstractMenuViewHelper|NULL
	 */
	protected function retrieveReconfiguredParentMenuInstance($pageUid) {
		if (FALSE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'parentInstance')) {
			return NULL;
		}
		$parentInstance = $this->viewHelperVariableContainer->get('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'parentInstance');
		$arguments = $parentInstance->getArguments();
		$arguments['pageUid'] = $pageUid;
		$parentInstance->setArguments($arguments);

		return $parentInstance;
	}

	protected function cleanTemplateVariableContainer() {
		if (FALSE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'variables')) {
			return;
		}
		$storedVariables = $this->viewHelperVariableContainer->get('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'variables');
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

	protected function unsetDeferredVariableStorage() {
		if (TRUE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'deferredString')) {
			$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'deferredString');
			$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper', 'deferredArray');
		}
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
		return (boolean) ('a' === strtolower($this->arguments['tagNameChildren']));
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
		if (TRUE === $pages instanceof \Traversable) {
			$pages = iterator_to_array($pages);
		} elseif (TRUE === is_string($pages)) {
			$pages = GeneralUtility::trimExplode(',', $pages, TRUE);
		} elseif (TRUE === is_int($pages)) {
			$pages = (array) $pages;
		}
		if (FALSE === is_array($pages)) {
			return array();
		}

		return $pages;
	}

	/**
	 * Get a list from allowed doktypes for pages
	 *
	 * @return array
	 */
	protected function allowedDoktypeList() {
		if (isset($this->arguments['doktypes']) && !empty($this->arguments['doktypes'])) {
			$types = $this->parseDoktypeList($this->arguments['doktypes']);
		} else {
			$types = array(
				PageService::DOKTYPE_MOVE_TO_PLACEHOLDER,
				PageRepository::DOKTYPE_DEFAULT,
				PageRepository::DOKTYPE_LINK,
				PageRepository::DOKTYPE_SHORTCUT,
				PageRepository::DOKTYPE_MOUNTPOINT,
			);
		}
		if (TRUE === (boolean) $this->arguments['includeSpacers'] && !in_array(PageRepository::DOKTYPE_SPACER, $types)) {
			array_push($types, PageRepository::DOKTYPE_SPACER);
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
		if (is_array($doktypes)) {
			$types = $doktypes;
		} else {
			$types = GeneralUtility::trimExplode(',', $doktypes);
		}
		$parsed = array();
		foreach ($types as $index => $type) {
			if (!ctype_digit($type)) {
				$typeNumber = constant('TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_' . strtoupper($type));
				if (NULL !== $typeNumber) {
					$parsed[$index] = $typeNumber;
				}
			} else {
				$parsed[$index] = (integer) $type;
			}
		}

		return $parsed;
	}

}
