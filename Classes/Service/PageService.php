<?php

namespace FluidTYPO3\Vhs\Service;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Type\Bitmask\PageTranslationVisibility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Page Service
 *
 * Wrapper service for \TYPO3\CMS\Frontend\Page\PageRepository including static caches for
 * menus, rootlines, pages and page overlays to be implemented in
 * viewhelpers by replacing calls to \TYPO3\CMS\Frontend\Page\PageRepository::getMenu()
 * and the like.
 */
class PageService implements SingletonInterface
{
    const DOKTYPE_MOVE_TO_PLACEHOLDER = 0;

    /**
     * @var array
     */
    protected static $cachedPages = [];

    /**
     * @var array
     */
    protected static $cachedMenus = [];

    /**
     * @var array
     */
    protected static $cachedRootlines = [];

    /**
     * @param string $constantName
     * @return mixed
     */
    public function readPageRepositoryConstant(string $constantName)
    {
        if (class_exists(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class)) {
            $class = \TYPO3\CMS\Core\Domain\Repository\PageRepository::class;
        } else {
            $class = \TYPO3\CMS\Frontend\Page\PageRepository::class;
        }

        return constant($class . '::' . $constantName);
    }

    /**
     * @param integer $pageUid
     * @param array $excludePages
     * @param boolean $includeNotInMenu
     * @param boolean $includeMenuSeparator
     * @param boolean $disableGroupAccessCheck
     *
     * @return array
     */
    public function getMenu(
        $pageUid,
        array $excludePages = [],
        $includeNotInMenu = false,
        $includeMenuSeparator = false,
        $disableGroupAccessCheck = false
    ) {
        $pageRepository = $this->getPageRepository();
        $pageConstraints = $this->getPageConstraints($excludePages, $includeNotInMenu, $includeMenuSeparator);
        $cacheKey = md5($pageUid . $pageConstraints . (integer) $disableGroupAccessCheck);
        if (false === isset(static::$cachedMenus[$cacheKey])) {
            if (true === (boolean) $disableGroupAccessCheck) {
                $pageRepository->where_groupAccess = '';
            }

            static::$cachedMenus[$cacheKey] = array_filter(
                $pageRepository->getMenu($pageUid, '*', 'sorting', $pageConstraints),
                function ($page) {
                    return $this->hidePageForLanguageUid($page) === false;
                }
            );
        }

        return static::$cachedMenus[$cacheKey];
    }

    /**
     * @param integer $pageUid
     * @param boolean $disableGroupAccessCheck
     * @return array
     */
    public function getPage($pageUid, $disableGroupAccessCheck = false)
    {
        $cacheKey = md5($pageUid . (integer) $disableGroupAccessCheck);
        if (false === isset(static::$cachedPages[$cacheKey])) {
            static::$cachedPages[$cacheKey] = $this->getPageRepository()->getPage($pageUid, $disableGroupAccessCheck);
        }

        return static::$cachedPages[$cacheKey];
    }

    /**
     * @param integer $pageUid
     * @param boolean $reverse
     * @param boolean $disableGroupAccessCheck
     * @return array
     */
    public function getRootLine($pageUid = null, $reverse = false, $disableGroupAccessCheck = false)
    {
        if (null === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        $cacheKey = md5($pageUid . (integer) $reverse . (integer) $disableGroupAccessCheck);
        if (false === isset(static::$cachedRootlines[$cacheKey])) {
            $pageRepository = $this->getPageRepository();
            if (class_exists(RootlineUtility::class)) {
                $rootline = (new RootlineUtility($pageUid))->get();
            } elseif (method_exists($pageRepository, 'getRootLine')) {
                if (true === (boolean) $disableGroupAccessCheck) {
                    $pageRepository->where_groupAccess = '';
                }
                $rootline = $pageRepository->getRootLine($pageUid);
            } else {
                $rootline = [];
            }
            if (true === $reverse) {
                $rootline = array_reverse($rootline);
            }
            static::$cachedRootlines[$cacheKey] = $rootline;
        }

        return static::$cachedRootlines[$cacheKey];
    }

    /**
     * @param array $excludePages
     * @param boolean $includeNotInMenu
     * @param boolean $includeMenuSeparator
     *
     * @return string
     */
    protected function getPageConstraints(
        array $excludePages = [],
        $includeNotInMenu = false,
        $includeMenuSeparator = false
    ) {
        $constraints = [];

        $constraints[] = 'doktype NOT IN ('
            . $this->readPageRepositoryConstant('DOKTYPE_BE_USER_SECTION')
            . ','
            . $this->readPageRepositoryConstant('DOKTYPE_RECYCLER')
            . ','
            . $this->readPageRepositoryConstant('DOKTYPE_SYSFOLDER')
            . ')';

        if ($includeNotInMenu === false) {
            $constraints[] = 'nav_hide = 0';
        }

        if ($includeMenuSeparator === false) {
            $constraints[] = 'doktype != ' . $this->readPageRepositoryConstant('DOKTYPE_SPACER');
        }

        if (0 < count($excludePages)) {
            $constraints[] = 'uid NOT IN (' . implode(',', $excludePages) . ')';
        }

        return 'AND ' . implode(' AND ', $constraints);
    }

    /**
     * @param array|integer $page
     * @param integer $languageUid
     * @param boolean $normalWhenNoLanguage
     * @return boolean
     */
    public function hidePageForLanguageUid($page = null, $languageUid = -1, $normalWhenNoLanguage = true)
    {
        if (is_array($page)) {
            $pageUid = $page['uid'];
            $pageRecord = $page;
        } else {
            $pageUid = (0 === (integer) $page) ? $GLOBALS['TSFE']->id : (integer) $page;
            $pageRecord = $this->getPage($pageUid);
        }
        if (-1 === (integer) $languageUid) {
            if (class_exists(LanguageAspect::class)) {
                /** @var Context $context */
                $context = GeneralUtility::makeInstance(Context::class);
                /** @var LanguageAspect $languageAspect */
                $languageAspect = $context->getAspect('language');
                $languageUid = $languageAspect->getId();
            } else {
                $languageUid = $GLOBALS['TSFE']->sys_language_uid;
            }
        }

        $l18nCfg = true === isset($pageRecord['l18n_cfg']) ? $pageRecord['l18n_cfg'] : 0;
        if (class_exists(PageTranslationVisibility::class)) {
            /** @var PageTranslationVisibility $visibilityBitSet */
            $visibilityBitSet = GeneralUtility::makeInstance(
                PageTranslationVisibility::class,
                $pageRecord['l18n_cfg'] ?? 0
            );
            $hideIfNotTranslated = $visibilityBitSet->shouldHideTranslationIfNoTranslatedRecordExists();
            $hideIfDefaultLanguage = $visibilityBitSet->shouldBeHiddenInDefaultLanguage();
        } else {
            $hideIfNotTranslated = (boolean) GeneralUtility::hideIfNotTranslated($l18nCfg);
            $hideIfDefaultLanguage = (boolean) GeneralUtility::hideIfDefaultLanguage($l18nCfg);
        }

        $pageOverlay = [];
        if (0 !== $languageUid) {
            $pageOverlay = $this->getPageRepository()->getPageOverlay($pageUid, $languageUid);
        }
        $translationAvailable = (0 !== count($pageOverlay));

        return
            (true === $hideIfNotTranslated && (0 !== $languageUid) && false === $translationAvailable) ||
            (true === $hideIfDefaultLanguage && ((0 === $languageUid) || false === $translationAvailable)) ||
            (false === $normalWhenNoLanguage && (0 !== $languageUid) && false === $translationAvailable);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Page\PageRepository|\TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    public function getPageRepository()
    {
        return clone ($GLOBALS['TSFE']->sys_page ?? $this->getPageRepositoryForBackendContext());
    }

    /**
     * @return \TYPO3\CMS\Frontend\Page\PageRepository|\TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    protected function getPageRepositoryForBackendContext()
    {
        static $instance = null;
        if ($instance === null) {
            /** @var PageRepository|\TYPO3\CMS\Core\Domain\Repository\PageRepository $instance */
            $instance = GeneralUtility::makeInstance(
                class_exists(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class)
                    ? \TYPO3\CMS\Core\Domain\Repository\PageRepository::class
                    : \TYPO3\CMS\Frontend\Page\PageRepository::class
            );
        }
        return $instance;
    }

    /**
     * @param array $page
     * @param boolean $forceAbsoluteUrl
     *
     * @return string
     */
    public function getItemLink(array $page, $forceAbsoluteUrl = false)
    {
        if ((integer) $page['doktype'] === $this->readPageRepositoryConstant('DOKTYPE_LINK')) {
            $parameter = $this->getPageRepository()->getExtURL($page);
        } else {
            $parameter = $page['uid'];
        }
        $config = [
            'parameter' => $parameter,
            'returnLast' => 'url',
            'additionalParams' => '',
            'forceAbsoluteUrl' => $forceAbsoluteUrl,
        ];
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '9.5', '<')) {
            $config['useCacheHash'] = false;
        }

        return $GLOBALS['TSFE']->cObj->typoLink('', $config);
    }

    /**
     * @param array $page
     * @return boolean
     */
    public function isAccessProtected(array $page)
    {
        return (0 !== (integer) $page['fe_group']);
    }

    /**
     * @param array $page
     * @return boolean
     */
    public function isAccessGranted(array $page)
    {
        if (!$this->isAccessProtected($page)) {
            return true;
        }

        $groups = explode(',', $page['fe_group']);

        $showPageAtAnyLogin = (in_array(-2, $groups));
        $hidePageAtAnyLogin = (in_array(-1, $groups));
        $userIsLoggedIn = (is_array($GLOBALS['TSFE']->fe_user->user));
        $userGroups = $GLOBALS['TSFE']->fe_user->groupData['uid'];
        $userIsInGrantedGroups = (0 < count(array_intersect($userGroups, $groups)));

        if ((false === $userIsLoggedIn && true === $hidePageAtAnyLogin) ||
            (true === $userIsLoggedIn && true === $showPageAtAnyLogin) ||
            (true === $userIsLoggedIn && true === $userIsInGrantedGroups)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param integer $pageUid
     * @return boolean
     */
    public function isCurrent($pageUid)
    {
        return ((integer) $pageUid === (integer) $GLOBALS['TSFE']->id);
    }

    /**
     * @param integer $pageUid
     * @param boolean $showAccessProtected
     * @return boolean
     */
    public function isActive($pageUid, $showAccessProtected = false)
    {
        $rootLineData = $this->getRootLine(null, false, $showAccessProtected);
        foreach ($rootLineData as $page) {
            if ((integer) $page['uid'] === (integer) $pageUid) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    public function shouldUseShortcutTarget(array $arguments)
    {
        $useShortcutTarget = (boolean) $arguments['useShortcutData'];
        if ($arguments['useShortcutTarget'] !== null) {
            $useShortcutTarget = (boolean) $arguments['useShortcutTarget'];
        }

        return $useShortcutTarget;
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    public function shouldUseShortcutUid(array $arguments)
    {
        $useShortcutUid = (boolean) $arguments['useShortcutData'];
        if ($arguments['useShortcutUid'] !== null) {
            $useShortcutUid = (boolean) $arguments['useShortcutUid'];
        }

        return $useShortcutUid;
    }

    /**
     * Determines the target page record for the provided page record
     * if it is configured as a shortcut in any of the possible modes.
     * Returns NULL otherwise.
     *
     * @param array $page
     * @return NULL|array
     */
    public function getShortcutTargetPage(array $page)
    {
        if ((integer) $page['doktype'] !== $this->readPageRepositoryConstant('DOKTYPE_SHORTCUT')) {
            return null;
        }
        $originalPageUid = $page['uid'];
        switch ($page['shortcut_mode']) {
            case 3:
                // mode: parent page of current page (using PID of current page)
                $targetPage = $this->getPage($page['pid']);
                break;
            case 2:
                // mode: random subpage of selected or current page
                $menu = $this->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $originalPageUid);
                $targetPage = (0 < count($menu)) ? $menu[array_rand($menu)] : $page;
                break;
            case 1:
                // mode: first subpage of selected or current page
                $menu = $this->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $originalPageUid);
                $targetPage = (0 < count($menu)) ? reset($menu) : $page;
                break;
            case 0:
            default:
                // mode: selected page
                $targetPage = $this->getPage($page['shortcut']);
        }
        return $targetPage;
    }
}
