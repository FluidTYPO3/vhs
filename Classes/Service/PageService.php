<?php

namespace FluidTYPO3\Vhs\Service;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Type\Bitmask\PageTranslationVisibility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageInformation;

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
    public const SHORTCUT_MODE_RANDOM_SUBPAGE = 2;
    protected ?ServerRequestInterface $request = null;

    public function setRequest(?ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getMenu(
        int $pageUid,
        array $excludePages = [],
        bool $includeNotInMenu = false,
        bool $includeMenuSeparator = false,
        bool $disableGroupAccessCheck = false
    ): array {
        $pageRepository = $this->getPageRepository();
        $pageConstraints = $this->getPageConstraints($excludePages, $includeNotInMenu, $includeMenuSeparator);
        $cacheKey = $this->buildContextualCacheKey([$pageUid, $pageConstraints, $disableGroupAccessCheck]);
        if (!isset(static::$cachedMenus[$cacheKey])) {
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
        $cacheKey = $this->buildContextualCacheKey([$pageUid, $disableGroupAccessCheck]);
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
            $pageUid = $this->getCurrentPageUid();
        }

        if (!$pageUid) {
            throw new \UnexpectedValueException('PageService::getRootLine requires a page UID', 1774448248);
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

        $types = [
            PageRepository::DOKTYPE_BE_USER_SECTION,
            PageRepository::DOKTYPE_SYSFOLDER
        ];

        $constraints[] = 'doktype NOT IN (' . implode(',', $types) . ')';

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
            $pageUid = (0 === (int) $page) ? (int) ($this->getCurrentPageUid() ?? 0) : (int) $page;
            $pageRecord = $this->getPage($pageUid);
        }
        if (-1 === $languageUid) {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var LanguageAspect $languageAspect */
            $languageAspect = $context->getAspect('language');
            $languageUid = $languageAspect->getId();
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
            $hideIfNotTranslated = (bool) GeneralUtility::hideIfNotTranslated($l18nCfg);
            $hideIfDefaultLanguage = (bool) GeneralUtility::hideIfDefaultLanguage($l18nCfg);
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
        $parameter = $page['uid'];
        if ((int) $page['doktype'] === PageRepository::DOKTYPE_LINK) {
            $redirectTo = $page['url'] ?? '';
            if (!empty($redirectTo)) {
                $uI = parse_url($redirectTo);
                // If relative path, prefix Site URL
                // If it's a valid email without protocol, add "mailto:"
                if (!($uI['scheme'] ?? false)) {
                    if (GeneralUtility::validEmail($redirectTo)) {
                        $redirectTo = 'mailto:' . $redirectTo;
                    } elseif ($redirectTo[0] !== '/') {
                        $redirectTo = $this->readSiteUrlFromRequest() . $redirectTo;
                    }
                }
                $parameter = $redirectTo;
            }
        }
        $config = [
            'parameter' => $parameter,
            'returnLast' => 'url',
            'additionalParams' => '',
            'forceAbsoluteUrl' => $forceAbsoluteUrl,
        ];

        return $this->getContentObjectRenderer()->typoLink('', $config);
    }

    public function isAccessProtected(array $page): bool
    {
        return (0 !== (int) $page['fe_group']);
    }

    public function isAccessGranted(array $page): bool
    {
        if (!$this->isAccessProtected($page)) {
            return true;
        }

        $groups = GeneralUtility::intExplode(',', (string) $page['fe_group']);

        $hide = (in_array(-1, $groups));
        $show = (in_array(-2, $groups));

        $frontendUser = $this->getFrontendUserAuthentication();
        $user = $frontendUser?->user;
        $userGroups = (array) ($frontendUser?->groupData['uid'] ?? []);
        $userIsLoggedIn = (is_array($user));
        $userIsInGrantedGroups = (0 < count(array_intersect($userGroups, $groups)));

        return (!$userIsLoggedIn && $hide) || ($userIsLoggedIn && $show) || ($userIsLoggedIn && $userIsInGrantedGroups);
    }

    public function isCurrent(int $pageUid): bool
    {
        return $pageUid === $this->getCurrentPageUid();
    }

    public function isActive(int $pageUid): bool
    {
        $rootLineData = $this->getRootLine();
        foreach ($rootLineData as $page) {
            if ((int) $page['uid'] === $pageUid) {
                return true;
            }
        }

        return false;
    }

    public function shouldUseShortcutTarget(array $arguments): bool
    {
        $useShortcutTarget = (bool) $arguments['useShortcutData'];
        if (array_key_exists('useShortcutTarget', $arguments)) {
            $useShortcutTarget = (bool) $arguments['useShortcutTarget'];
        }

        return $useShortcutTarget;
    }

    public function shouldUseShortcutUid(array $arguments): bool
    {
        $useShortcutUid = (bool) $arguments['useShortcutData'];
        if (array_key_exists('useShortcutUid', $arguments)) {
            $useShortcutUid = (bool) $arguments['useShortcutUid'];
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
        $dokType = (int) ($page['doktype'] ?? PageRepository::DOKTYPE_DEFAULT);
        if ($dokType !== PageRepository::DOKTYPE_SHORTCUT) {
            return null;
        }
        $originalPageUid = $page['uid'];
        switch ($page['shortcut_mode']) {
            case PageRepository::SHORTCUT_MODE_PARENT_PAGE:
                $targetPage = $this->getPage($page['pid']);
                break;
            case self::SHORTCUT_MODE_RANDOM_SUBPAGE:
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
        return clone $this->getPageRepositoryForBackendContext();
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

    protected function getRequest(): ?ServerRequestInterface
    {
        if ($this->request instanceof ServerRequestInterface) {
            return $this->request;
        }
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        return $request instanceof ServerRequestInterface ? $request : null;
    }

    protected function buildContextualCacheKey(array $parts): string
    {
        $request = $this->getRequest();
        $contextParts = [
            'request' => null,
            'site' => null,
            'language' => null,
            'workspace' => null,
            'frontendUser' => null,
        ];
        if ($request instanceof ServerRequestInterface) {
            $site = $request->getAttribute('site');
            $frontendUser = $request->getAttribute('frontend.user');
            $contextParts['request'] = spl_object_id($request);
            $contextParts['site'] = is_object($site) && method_exists($site, 'getIdentifier')
                ? $site->getIdentifier()
                : null;
            if ($frontendUser instanceof FrontendUserAuthentication) {
                $contextParts['frontendUser'] = [
                    'user' => $frontendUser->user['uid'] ?? null,
                    'groups' => $frontendUser->groupData['uid'] ?? [],
                ];
            }
        }

        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        try {
            /** @var LanguageAspect $languageAspect */
            $languageAspect = $context->getAspect('language');
            $contextParts['language'] = $languageAspect->getId();
        } catch (\Throwable) {
        }
        try {
            $workspaceAspect = $context->getAspect('workspace');
            $contextParts['workspace'] = method_exists($workspaceAspect, 'getId') ? $workspaceAspect->getId() : null;
        } catch (\Throwable) {
        }

        return sha1(json_encode([$parts, $contextParts], JSON_THROW_ON_ERROR));
    }

    protected function getPageInformation(): ?PageInformation
    {
        $pageInformation = $this->getRequest()?->getAttribute('frontend.page.information');
        return $pageInformation instanceof PageInformation ? $pageInformation : null;
    }

    protected function getCurrentPageUid(): ?int
    {
        $pageInformation = $this->getPageInformation();
        if ($pageInformation instanceof PageInformation) {
            return $pageInformation->getId();
        }
        $routing = $this->getRequest()?->getAttribute('routing');
        if ($routing instanceof PageArguments) {
            return $routing->getPageId();
        }
        return null;
    }

    protected function getFrontendUserAuthentication(): ?FrontendUserAuthentication
    {
        $frontendUser = $this->getRequest()?->getAttribute('frontend.user');
        return $frontendUser instanceof FrontendUserAuthentication ? $frontendUser : null;
    }

    protected function getContentObjectRenderer(): ContentObjectRenderer
    {
        $request = $this->getRequest();
        if (!$request instanceof ServerRequestInterface) {
            throw new \UnexpectedValueException('PageService::getItemLink requires a frontend request', 1774448249);
        }
        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $contentObjectRenderer->setRequest($request);
        return $contentObjectRenderer;
    }

    protected function readSiteUrlFromRequest(): string
    {
        $request = $this->getRequest();
        if (!$request instanceof ServerRequestInterface) {
            throw new \UnexpectedValueException(
                'PageService::readSiteUrlFromRequest requires a frontend request',
                1774448250
            );
        }
        $normalizedParams = $request->getAttribute('normalizedParams');
        if ($normalizedParams instanceof NormalizedParams) {
            return $normalizedParams->getSiteUrl();
        }

        $uri = $request->getUri();
        $path = (string) $uri->getPath();
        if ('' === $path || '/' === $path) {
            $path = '/';
        }
        $path = rtrim(dirname($path), '/');
        return $uri->withPath($path . '/')->withQuery('')->withFragment('')->__toString();
    }

    public static function resetCaches(): void
    {
        static::$cachedPages = [];
        static::$cachedMenus = [];
    }
}
