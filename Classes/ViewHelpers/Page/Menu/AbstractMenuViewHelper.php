<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageSelectService;
use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Base class for menu rendering ViewHelpers
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @author Björn Fromme <fromeme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 * @subpackage ViewHelpers\Page\Menu
 */
abstract class AbstractMenuViewHelper extends AbstractTagBasedViewHelper {

	use TagViewHelperTrait;

	/**
	 * @var string
	 */
	protected $tagName = 'ul';

	/**
	 * @var array
	 */
	private $backupValues = array();

	/**
	 * @var boolean
	 */
	private $original = TRUE;

	/**
	 * @var PageSelectService
	 */
	protected $pageSelect;

	/**
	 * @param PageSelectService $pageSelectService
	 * @return void
	 */
	public function injectPageSelectService(PageSelectService $pageSelectService) {
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
		$this->registerArgument('useShortcutUid', 'boolean', 'If TRUE, substitutes the link UID of a shortcut with the target page UID (and thus avoiding redirects) but does not change other data - which is done by using useShortcutData.', FALSE, FALSE);
		$this->registerArgument('useShortcutTarget', 'boolean', 'Optional param for using shortcut target instead of shortcut itself for current link', FALSE, NULL);
		$this->registerArgument('useShortcutData', 'boolean', 'Shortcut to set useShortcutTarget and useShortcutData simultaneously', FALSE, NULL);
		$this->registerArgument('classFirst', 'string', 'Optional class name for the first menu elment', FALSE, '');
		$this->registerArgument('classLast', 'string', 'Optional class name for the last menu elment', FALSE, '');
		$this->registerArgument('substElementUid', 'boolean', 'Optional parameter for wrapping the link with the uid of the page', FALSE, '');
		$this->registerArgument('includeSpacers', 'boolean', 'Wether or not to include menu spacers in the page select query', FALSE, FALSE);
		$this->registerArgument('resolveExclude', 'boolean', 'Exclude link if realurl/cooluri flag tx_realurl_exclude is set', FALSE, FALSE);
		$this->registerArgument('showHidden', 'boolean', 'DEPRECATED - IGNORED. FIELD IS AN ENABLE-FIELD WHICH MUST BE RESPECTED. Include disabled pages into the menu', FALSE, FALSE);
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
		$this->registerArgument('forceAbsoluteUrl', 'boolean', 'If TRUE, the menu will be rendered with absolute URLs', FALSE, FALSE);
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
		$this->viewHelperVariableContainer->addOrUpdate('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'parentInstance', $this);
		$this->viewHelperVariableContainer->addOrUpdate('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'variables', $variables);
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
		if (FALSE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'parentInstance')) {
			return NULL;
		}
		$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'parentInstance');
		$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'variables');
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
		$parentInstance = $this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\Page\\Menu\\AbstractMenuViewHelper', 'parentInstance');
		$arguments = $parentInstance->getArguments();
		$arguments['pageUid'] = $pageUid;
		$parentInstance->setArguments($arguments);
		return $parentInstance;
	}

	/**
	 * @return void
	 */
	protected function cleanTemplateVariableContainer() {
		if (FALSE === $this->viewHelperVariableContainer->exists('FluidTYPO3\\Vhs\\ViewHelpers\\Page\\Menu\\AbstractMenuViewHelper', 'variables')) {
			return;
		}
		$storedVariables = $this->viewHelperVariableContainer->get('FluidTYPO3\\Vhs\\ViewHelpers\\Page\\Menu\\AbstractMenuViewHelper', 'variables');
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
		return (boolean) ((integer) $pageUid === (integer) $GLOBALS['TSFE']->id);
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
			if ((integer) $page['uid'] === (integer) $pageUid) {
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
				PageSelectService::DOKTYPE_MOVE_TO_PLACEHOLDER,
				PageRepository::DOKTYPE_DEFAULT,
				PageRepository::DOKTYPE_LINK,
				PageRepository::DOKTYPE_SHORTCUT,
				PageRepository::DOKTYPE_MOUNTPOINT,
			);
		}
		if (TRUE === (boolean) $this->arguments['includeSpacers'] && FALSE === in_array(PageRepository::DOKTYPE_SPACER, $types)) {
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
		if (TRUE === is_array($doktypes)) {
			$types = $doktypes;
		} else {
			$types = GeneralUtility::trimExplode(',', $doktypes);
		}
		$parsed = array();
		foreach ($types as $index => $type) {
			if (FALSE === ctype_digit($type)) {
				$typeNumber = constant('TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_' . strtoupper($type));
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
		$titleFieldList = GeneralUtility::trimExplode(',', $this->arguments['titleFields']);
		foreach ($titleFieldList as $titleFieldName) {
			if (FALSE === empty($page[$titleFieldName])) {
				return $page[$titleFieldName];
			}
		}
		return $page['title'];
	}

	/**
	 * Get the combined item CSS class based on menu item state and VH arguments
	 *
	 * @param array $pageRow
	 * @return array
	 */
	protected function getItemClass($pageRow) {
		$class = array();
		if (TRUE === (boolean) $pageRow['active']) {
			$class[] = $this->arguments['classActive'];
		}
		if (TRUE === (boolean) $pageRow['current']) {
			$class[] = $this->arguments['classCurrent'];
		}
		if (TRUE === (boolean) $pageRow['hasSubPages']) {
			$class[] = $this->arguments['classHasSubpages'];
		}
		return $class;
	}

	/**
	 * Create the href of a link for a page record respecting
	 * a possible shortcut UID or mountpoint
	 *
	 * @param array $page
	 * @return string
	 */
	protected function getItemLink($page) {
		$doktype = (integer) $page['doktype'];
		if (PageRepository::DOKTYPE_SPACER === $doktype) {
			return '';
		}
		$shortcut = (PageRepository::DOKTYPE_SHORTCUT === $doktype ? $page['shortcut'] : $page['url']);
		$isShortcutOrLink = PageRepository::DOKTYPE_SHORTCUT === $doktype || PageRepository::DOKTYPE_LINK === $doktype;
		$useShortcutTarget = $this->shouldUseShortcutTarget();
		$pageUid = $page['uid'];
		if (TRUE === $isShortcutOrLink && TRUE === $useShortcutTarget && 0 < $shortcut) {
			$pageUid = $shortcut;
		}
		if (TRUE === (PageRepository::DOKTYPE_MOUNTPOINT === $doktype)) {
			$pageUid = $page['mountedPageUid'];
		}
		$forceAbsoluteUrl = (boolean) $this->arguments['forceAbsoluteUrl'];
		$config = array(
			'parameter' => $pageUid,
			'returnLast' => 'url',
			'additionalParams' => '',
			'useCacheHash' => FALSE,
			'forceAbsoluteUrl' => $forceAbsoluteUrl,
		);
		// Append mountpoint parameter to urls of pages of a mounted subtree
		$mountPointParameter = NULL;
		if (FALSE === empty($page['mountPointParameter'])) {
			$mountPointParameter = $page['mountPointParameter'];
		}
		if (FALSE === empty($page['_MP_PARAM'])) {
			$mountPointParameter = $page['_MP_PARAM'];
		}
		if (NULL !== $mountPointParameter) {
			$config['additionalParams'] = '&MP=' . $mountPointParameter;
		}
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
	 * @param array $parentPage
	 * @return array
	 */
	protected function getMenuItemEntry($page, $rootLine, array $parentPage = NULL) {
		$getLL = $GLOBALS['TSFE']->sys_language_uid;
		$page['originalPageUid'] = $page['uid'];
		$overlayPageUid = $page['uid'];
		$pageUid = $page['uid'];
		$targetPage = NULL;
		$doktype = (integer) $page['doktype'];
		if (NULL !== $parentPage && TRUE === isset($parentPage['_MP_PARAM'])) {
			$page['mountPointParameter'] = $parentPage['_MP_PARAM'];
		}
		if (PageRepository::DOKTYPE_MOUNTPOINT === $doktype) {
			$mountInfo = $GLOBALS['TSFE']->sys_page->getMountPointInfo($page['uid'], $page);
			$page['mountedPageUid'] = $mountInfo['mount_pid'];
			$page['mountPointParameter'] = $mountInfo['MPvar'];
		} elseif (PageRepository::DOKTYPE_SHORTCUT === $doktype) {
			switch ($page['shortcut_mode']) {
				case 3:
					// mode: parent page of current page (using PID of current page)
					$targetPage = $this->pageSelect->getPage($page['pid']);
					if ((integer) $page['pid'] === (integer) $GLOBALS['TSFE']->id) {
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
				$overlayPageUid = $targetPage['uid'];
			}
			if (TRUE === (boolean) $this->shouldUseShortcutUid()) {
				// overwrite current page UID
				$page['uid'] = $targetPage['uid'];
			}
		}

		if (0 < $getLL) {
			$pageOverlay = $this->pageSelect->getPageOverlay($overlayPageUid, $getLL);
			foreach ($pageOverlay as $name => $value) {
				$page[$name] = $value;
			}
		}

		$page['hasSubPages'] = (0 < count($this->getSubmenu($page['originalPageUid'])));
		$page['active'] = $this->isActive($page['uid'], $rootLine, $page['originalPageUid']);
		$page['current'] = $this->isCurrent($page['uid']);
		$page['link'] = $this->getItemLink($page);
		$page['linktext'] = $this->getItemTitle($page);
		$page['class'] = implode(' ', $this->getItemClass($page));
		$page['doktype'] = $doktype;

		if (PageRepository::DOKTYPE_LINK === $doktype) {
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
	 * @param array $parentPage
	 * @return array
	 */
	protected function parseMenu($menu, $rootLine, array $parentPage = NULL) {
		$classFirst = $this->arguments['classFirst'];
		$classLast = $this->arguments['classLast'];
		$filtered = array();
		$allowedDocumentTypes = $this->allowedDoktypeList();
		foreach ($menu as $uid => $page) {
			if (TRUE === isset($page['tx_realurl_exclude']) && TRUE === (boolean) $page['tx_realurl_exclude'] && TRUE === (boolean) $this->arguments['resolveExclude']) {
				continue;
			} elseif (TRUE === isset($page['tx_cooluri_exclude']) && TRUE === (boolean) $page['tx_cooluri_exclude'] && TRUE === (boolean) $this->arguments['resolveExclude']) {
				continue;
			} elseif (TRUE === in_array($page['doktype'], $allowedDocumentTypes)) {
				$filtered[$uid] = $this->getMenuItemEntry($page, $rootLine, $parentPage);
			}
		}
		$length = count($filtered);
		if (0 < $length) {
			$idx = 1;
			foreach ($filtered as $uid => $page) {
				if (1 === $idx) {
					$filtered[$uid]['class'] = trim($filtered[$uid]['class'] . ' ' . $classFirst);
				}
				if ($length === $idx) {
					$filtered[$uid]['class'] = trim($filtered[$uid]['class'] . ' ' . $classLast);
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
			if (TRUE === (boolean) $page['current'] && FALSE === $showCurrent) {
				continue;
			}
			$class = trim($page['class']) != '' ? ' class="' . $page['class'] . '"' : '';
			$elementId = $substElementUid ? ' id="elem_' . $page['uid'] . '"' : '';
			$target = $page['target'] != '' ? ' target="' . $page['target'] . '"' : '';
			if (FALSE === $this->isNonWrappingMode()) {
				$html[] = '<' . $tagName . $elementId . $class . '>';
			}
			$isSpacer = ($page['doktype'] === PageRepository::DOKTYPE_SPACER);
			$isCurrent = (boolean) $page['current'];
			$isActive = (boolean) $page['active'];
			if (TRUE === $isSpacer || (TRUE === $isCurrent && FALSE === $linkCurrent) || (TRUE === $isActive && FALSE === $linkActive)) {
				$html[] = htmlspecialchars($page['linktext']);
			} elseif (TRUE === $includeAnchorTitle) {
				$html[] = sprintf('<a href="%s" title="%s"%s%s>%s</a>', $page['link'], htmlspecialchars($page['title']), $class, $target, htmlspecialchars($page['linktext']));
			} else {
				$html[] = sprintf('<a href="%s"%s%s>%s</a>', $page['link'], $class, $target, htmlspecialchars($page['linktext']));
			}
			if ((TRUE === (boolean) $page['active'] || TRUE === $expandAll) && TRUE === (boolean) $page['hasSubPages'] && $level < $maxLevels) {
				$pageUid = (TRUE === isset($page['mountedPageUid'])) ? $page['mountedPageUid'] : $page['originalPageUid'];
				$rootLineData = $this->pageSelect->getRootLine();
				$subMenuData = $this->getMenu($pageUid);
				$subMenu = $this->parseMenu($subMenuData, $rootLineData, $page);
				$renderedSubMenu = $this->autoRender($subMenu, $level + 1);
				$parentTagId = $this->tag->getAttribute('id');
				if (FALSE === empty($parentTagId)) {
					$this->tag->addAttribute('id', $parentTagId . '-lvl-' . strval($level));
				}
				$this->tag->setTagName($this->getWrappingTagName());
				$this->tag->setContent($renderedSubMenu);
				$this->tag->addAttribute('class', ($this->arguments['class'] ? $this->arguments['class'] . ' lvl-' : 'lvl-') . strval($level));
				$html[] = $this->tag->render();
				$this->tag->addAttribute('class', $this->arguments['class']);
				if (FALSE === empty($parentTagId)) {
					$this->tag->addAttribute('id', $parentTagId);
				}
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
		return (boolean) ('a' === strtolower($this->arguments['tagNameChildren']));
	}

	/**
	 * Saves copies of all template variables while rendering
	 * the menu
	 *
	 * @return void
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
	 *
	 * @return void
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
		if (TRUE === $deferredRendering) {
			$tagContent = $this->autoRender($menu);
			$this->tag->setContent($tagContent);
			$deferredContent = $this->tag->render();
			$this->viewHelperVariableContainer->addOrUpdate(
				'FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'deferredString', $deferredContent
			);
			$this->viewHelperVariableContainer->addOrUpdate(
				'FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'deferredArray', $menu
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
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$pageUid = $this->arguments['pageUid'];
		$rootLineData = $this->pageSelect->getRootLine();
		$entryLevel = (integer) $this->arguments['entryLevel'];
		if (0 > $entryLevel) {
			$entryLevel = count($rootLineData) + $entryLevel;
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
		$showHiddenInMenu = (boolean) $this->arguments['showHiddenInMenu'];
		$allowedDoktypeList = $this->allowedDoktypeList();
		$menuData = $this->pageSelect->getMenu($pageUid, $excludePages, $where, $showHiddenInMenu, FALSE, $allowedDoktypeList);
		foreach ($menuData as $key => $page) {
			if (TRUE === $this->pageSelect->hidePageForLanguageUid($page['uid'], $GLOBALS['TSFE']->sys_language_uid)) {
				unset($menuData[$key]);
			}
		}
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
		if (TRUE === $pages instanceof \Traversable) {
			$pages = iterator_to_array($pages);
		} elseif (TRUE === is_string($pages)) {
			$pages = GeneralUtility::trimExplode(',', $pages, TRUE);
		} elseif (TRUE === is_integer($pages)) {
			$pages = (array) $pages;
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
		if (TRUE === $this->viewHelperVariableContainer->exists('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'deferredString')) {
			$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'deferredString');
			$this->viewHelperVariableContainer->remove('FluidTYPO3\Vhs\ViewHelpers\Page\Menu\AbstractMenuViewHelper', 'deferredArray');
		}
	}

}
