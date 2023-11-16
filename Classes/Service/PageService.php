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
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Type\Bitmask\PageTranslationVisibility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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

    protected static array $cachedPages = [];
    protected static array $cachedMenus = [];

    public function getMenu(
        int $pageUid,
        array $excludePages = [],
        bool $includeNotInMenu = false,
        bool $includeMenuSeparator = false,
        bool $disableGroupAccessCheck = false
    ): array {
        $pageRepository = $this->getPageRepository();
        $pageConstraints = $this->getPageConstraints($excludePages, $includeNotInMenu, $includeMenuSeparator);
        $cacheKey = md5($pageUid . $pageConstraints . (integer) $disableGroupAccessCheck);
        if (!isset(static::$cachedMenus[$cacheKey])) {
            if ($disableGroupAccessCheck
                && version_compare(VersionNumberUtility::getCurrentTypo3Version(), '12.1', '<=')
            ) {
                $pageRepository->where_groupAccess = '';
            }

            static::$cachedMenus[$cacheKey] = array_filter(
                $pageRepository->getMenu($pageUid, '*', 'sorting', $pageConstraints, true, $disableGroupAccessCheck),
                function ($page) use ($includeNotInMenu) {
                    return (!($page['nav_hide'] ?? false) || $includeNotInMenu)
                        && !$this->hidePageForLanguageUid($page);
                }
            );
        }

        return static::$cachedMenus[$cacheKey];
    }

    public function getPage(int $pageUid, bool $disableGroupAccessCheck = false): array
    {
        $cacheKey = md5($pageUid . (integer) $disableGroupAccessCheck);
        if (!isset(static::$cachedPages[$cacheKey])) {
            static::$cachedPages[$cacheKey] = $this->getPageRepository()->getPage($pageUid, $disableGroupAccessCheck);
        }

        return static::$cachedPages[$cacheKey];
    }

    public function getRootLine(
        ?int $pageUid = null,
        bool $reverse = false
    ): array {
        if (null === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        /** @var RootlineUtility $rootLineUtility */
        $rootLineUtility = GeneralUtility::makeInstance(RootlineUtility::class, $pageUid);
        $rootline = $rootLineUtility->get();
        if ($reverse) {
            $rootline = array_reverse($rootline);
        }
        return $rootline;
    }

    protected function getPageConstraints(
        array $excludePages = [],
        bool $includeNotInMenu = false,
        bool $includeMenuSeparator = false
    ): string {
        $constraints = [];

        $constraints[] = 'doktype NOT IN ('
            . PageRepository::DOKTYPE_BE_USER_SECTION
            . ','
            . PageRepository::DOKTYPE_RECYCLER
            . ','
            . PageRepository::DOKTYPE_SYSFOLDER
            . ')';

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
     * @param array|integer|null $page
     */
    public function hidePageForLanguageUid($page = null, int $languageUid = -1, bool $normalWhenNoLanguage = true): bool
    {
        if (is_array($page)) {
            $pageUid = $page['uid'];
            $pageRecord = $page;
        } else {
            $pageUid = (0 === (integer) $page) ? $GLOBALS['TSFE']->id : (integer) $page;
            $pageRecord = $this->getPage($pageUid);
        }
        if (-1 === $languageUid) {
            $languageUid = $GLOBALS['TSFE']->sys_language_uid;
            if (class_exists(LanguageAspect::class)) {
                /** @var Context $context */
                $context = GeneralUtility::makeInstance(Context::class);
                /** @var LanguageAspect $languageAspect */
                $languageAspect = $context->getAspect('language');
                $languageUid = $languageAspect->getId();
            }
        }

        $l18nCfg = $pageRecord['l18n_cfg'] ?? 0;
        if (class_exists(PageTranslationVisibility::class)) {
            /** @var PageTranslationVisibility $visibilityBitSet */
            $visibilityBitSet = GeneralUtility::makeInstance(
                PageTranslationVisibility::class,
                $l18nCfg
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
            ($hideIfNotTranslated && (0 !== $languageUid) && !$translationAvailable) ||
            ($hideIfDefaultLanguage && ((0 === $languageUid) || !$translationAvailable)) ||
            (!$normalWhenNoLanguage && (0 !== $languageUid) && !$translationAvailable);
    }

    public function getItemLink(array $page, bool $forceAbsoluteUrl = false): string
    {
        if ((integer) $page['doktype'] === PageRepository::DOKTYPE_LINK) {
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

        return $GLOBALS['TSFE']->cObj->typoLink('', $config);
    }

    public function isAccessProtected(array $page): bool
    {
        return (0 !== (integer) $page['fe_group']);
    }

    public function isAccessGranted(array $page): bool
    {
        if (!$this->isAccessProtected($page)) {
            return true;
        }

        $groups = GeneralUtility::intExplode(',', (string) $page['fe_group']);

        $hide = (in_array(-1, $groups));
        $show = (in_array(-2, $groups));

        $userIsLoggedIn = (is_array($GLOBALS['TSFE']->fe_user->user));
        $userGroups = $GLOBALS['TSFE']->fe_user->groupData['uid'];
        $userIsInGrantedGroups = (0 < count(array_intersect($userGroups, $groups)));

        return (!$userIsLoggedIn && $hide) || ($userIsLoggedIn && $show) || ($userIsLoggedIn && $userIsInGrantedGroups);
    }

    public function isCurrent(int $pageUid): bool
    {
        return ($pageUid === (integer) $GLOBALS['TSFE']->id);
    }

    public function isActive(int $pageUid): bool
    {
        $rootLineData = $this->getRootLine();
        foreach ($rootLineData as $page) {
            if ((integer) $page['uid'] === $pageUid) {
                return true;
            }
        }

        return false;
    }

    public function shouldUseShortcutTarget(array $arguments): bool
    {
        $useShortcutTarget = (boolean) $arguments['useShortcutData'];
        if (array_key_exists('useShortcutTarget', $arguments)) {
            $useShortcutTarget = (boolean) $arguments['useShortcutTarget'];
        }

        return $useShortcutTarget;
    }

    public function shouldUseShortcutUid(array $arguments): bool
    {
        $useShortcutUid = (boolean) $arguments['useShortcutData'];
        if (array_key_exists('useShortcutUid', $arguments)) {
            $useShortcutUid = (boolean) $arguments['useShortcutUid'];
        }

        return $useShortcutUid;
    }

    /**
     * Determines the target page record for the provided page record
     * if it is configured as a shortcut in any of the possible modes.
     * Returns NULL otherwise.
     */
    public function getShortcutTargetPage(array $page): ?array
    {
        if ((integer) $page['doktype'] !== PageRepository::DOKTYPE_SHORTCUT) {
            return null;
        }
        $originalPageUid = $page['uid'];
        switch ($page['shortcut_mode']) {
            case PageRepository::SHORTCUT_MODE_PARENT_PAGE:
                $targetPage = $this->getPage($page['pid']);
                break;
            case PageRepository::SHORTCUT_MODE_RANDOM_SUBPAGE:
                $menu = $this->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $originalPageUid);
                $targetPage = (0 < count($menu)) ? $menu[array_rand($menu)] : $page;
                break;
            case PageRepository::SHORTCUT_MODE_FIRST_SUBPAGE:
                $menu = $this->getMenu($page['shortcut'] > 0 ? $page['shortcut'] : $originalPageUid);
                $targetPage = (0 < count($menu)) ? reset($menu) : $page;
                break;
            case PageRepository::SHORTCUT_MODE_NONE:
            default:
                $targetPage = $this->getPage($page['shortcut']);
        }
        return $targetPage;
    }

    /**
     * @return PageRepository
     * @codeCoverageIgnore
     */
    public function getPageRepository()
    {
        return clone ($GLOBALS['TSFE']->sys_page ?? $this->getPageRepositoryForBackendContext());
    }

    /**
     * @return PageRepository
     * @codeCoverageIgnore
     */
    protected function getPageRepositoryForBackendContext()
    {
        static $instance = null;
        if ($instance === null) {
            /** @var PageRepository $instance */
            $instance = GeneralUtility::makeInstance(PageRepository::class);
        }
        return $instance;
    }
}
