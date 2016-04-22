<?php

namespace FluidTYPO3\Vhs\Service;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Page Service
 *
 * Wrapper service for \TYPO3\CMS\Frontend\Page\PageRepository including static caches for
 * menus, rootlines, pages and page overlays to be implemented in
 * viewhelpers by replacing calls to \TYPO3\CMS\Frontend\Page\PageRepository::getMenu()
 * and the like.
 *
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 */
class PageService implements SingletonInterface
{

    const DOKTYPE_MOVE_TO_PLACEHOLDER = 0;

    /**
     * @var array
     */
    protected static $cachedPages = array();

    /**
     * @var array
     */
    protected static $cachedMenus = array();

    /**
     * @var array
     */
    protected static $cachedRootlines = array();

    /**
     * @param int $pageUid
     * @param array $excludePages
     * @param bool $includeNotInMenu
     * @param bool $includeMenuSeparator
     * @param bool $disableGroupAccessCheck
     *
     * @return array
     */
    public function getMenu($pageUid, array $excludePages = array(), $includeNotInMenu = false, $includeMenuSeparator = false, $disableGroupAccessCheck = false)
    {
        $pageRepository = $this->getPageRepository();
        $pageConstraints = $this->getPageConstraints($excludePages, $includeNotInMenu, $includeMenuSeparator);
        $cacheKey = md5($pageUid . $pageConstraints . (integer) $disableGroupAccessCheck);
        if (false === isset(self::$cachedMenus[$cacheKey])) {
            if (true === (boolean) $disableGroupAccessCheck) {
                $pageRepository->where_groupAccess = '';
            }
            self::$cachedMenus[$cacheKey] = $pageRepository->getMenu(
                $pageUid,
                '*',
                'sorting',
                $pageConstraints
            );
        }

        return self::$cachedMenus[$cacheKey];
    }

    /**
     * @param int $pageUid
     * @param bool $disableGroupAccessCheck
     * @return array
     */
    public function getPage($pageUid, $disableGroupAccessCheck = false)
    {
        $cacheKey = md5($pageUid . (integer) $disableGroupAccessCheck);
        if (false === isset(self::$cachedPages[$cacheKey])) {
            self::$cachedPages[$cacheKey] = $this->getPageRepository()->getPage($pageUid, $disableGroupAccessCheck);
        }

        return self::$cachedPages[$cacheKey];
    }

    /**
     * @param int $pageUid
     * @param bool $reverse
     * @param bool $disableGroupAccessCheck
     * @return array
     */
    public function getRootLine($pageUid = null, $reverse = false, $disableGroupAccessCheck = false)
    {
        if (null === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        $cacheKey = md5($pageUid . (integer) $reverse . (integer) $disableGroupAccessCheck);
        if (false === isset(self::$cachedRootlines[$cacheKey])) {
            $pageRepository = $this->getPageRepository();
            if (true === (boolean) $disableGroupAccessCheck) {
                $pageRepository->where_groupAccess = '';
            }
            $rootline = $pageRepository->getRootLine($pageUid);
            if (true === $reverse) {
                $rootline = array_reverse($rootline);
            }
            self::$cachedRootlines[$cacheKey] = $rootline;
        }

        return self::$cachedRootlines[$cacheKey];
    }

    /**
     * @param array $excludePages
     * @param bool $includeNotInMenu
     * @param bool $includeMenuSeparator
     *
     * @return string
     */
    protected function getPageConstraints(array $excludePages = array(), $includeNotInMenu = false, $includeMenuSeparator = false)
    {
        $constraints = array();

        $constraints[] = 'doktype NOT IN (' . PageRepository::DOKTYPE_BE_USER_SECTION . ',' . PageRepository::DOKTYPE_RECYCLER . ',' . PageRepository::DOKTYPE_SYSFOLDER . ')';

        if ($includeNotInMenu === false) {
            $constraints[] = 'nav_hide = 0';
        }

        if ($includeMenuSeparator === false) {
            $constraints[] = 'doktype != ' . PageRepository::DOKTYPE_SPACER;
        }

        if (0 < count($excludePages)) {
            $constraints[] = 'uid NOT IN (' . implode(',', $excludePages) . ')';
        }

        return 'AND ' . implode(' AND ', $constraints);
    }

    /**
     * @param int $pageUid
     * @param int $languageUid
     * @param bool $normalWhenNoLanguage
     * @return bool
     */
    public function hidePageForLanguageUid($pageUid = 0, $languageUid = -1, $normalWhenNoLanguage = true)
    {
        if (0 === (integer) $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        if (-1 === (integer) $languageUid) {
            $languageUid = $GLOBALS['TSFE']->sys_language_uid;
        }
        $page = $this->getPage($pageUid);
        $l18nCfg = true === isset($page['l18n_cfg']) ? $page['l18n_cfg'] : 0;
        $hideIfNotTranslated = (boolean) GeneralUtility::hideIfNotTranslated($l18nCfg);
        $hideIfDefaultLanguage = (boolean) GeneralUtility::hideIfDefaultLanguage($l18nCfg);
        $pageOverlay = (0 !== $languageUid) ? $GLOBALS['TSFE']->sys_page->getPageOverlay($pageUid, $languageUid) : array();
        $translationAvailable = (0 !== count($pageOverlay));

        return
            (true === $hideIfNotTranslated && (0 !== $languageUid) && false === $translationAvailable) ||
            (true === $hideIfDefaultLanguage && ((0 === $languageUid) || false === $translationAvailable)) ||
            (false === $normalWhenNoLanguage && (0 !== $languageUid) && false === $translationAvailable);
    }

    /**
     * @return PageRepository
     */
    protected function getPageRepository()
    {
        return clone $GLOBALS['TSFE']->sys_page;
    }
}
