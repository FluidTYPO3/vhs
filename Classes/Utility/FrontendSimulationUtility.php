<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\SiteFinderProxy;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Frontend Simulation Utility
 *
 * Utility to simulate frontend enviroment in backend enviroment.
 */
class FrontendSimulationUtility
{
    /**
     * Sets the global variable $GLOBALS['TYPO3_REQUEST'] with frontend.controller attribute in Backend mode.
     */
    public static function simulateFrontendEnvironment(): ?ServerRequestInterface
    {
        if (!ContextUtility::isBackend() || VersionUtility::isCoreAtLeast14()) {
            return null;
        }
        $requestBackup = $GLOBALS['TYPO3_REQUEST'] ?? null;

        $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieName'] = $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieName'] ?? 'fe_user';

        /** @var SiteFinderProxy $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinderProxy::class);
        $sites = $siteFinder->getAllSites();
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        /** @var Site $site */
        $site = reset($sites);
        $siteLanguage = $site->getDefaultLanguage();
        /** @var PageArguments $pageArguments */
        $pageArguments = GeneralUtility::makeInstance(
            PageArguments::class,
            0,
            (string) PageRepository::DOKTYPE_DEFAULT,
            []
        );
        /** @var FrontendUserAuthentication $frontendUser */
        $frontendUser = GeneralUtility::makeInstance(FrontendUserAuthentication::class);

        /** @var class-string $controllerClassName */
        $controllerClassName = TypoScriptFrontendController::class;
        if (!class_exists($controllerClassName)) {
            return null;
        }

        $controller = GeneralUtility::makeInstance(
            $controllerClassName, // @phpstan-ignore-line The legacy controller class does not exist on TYPO3 v14.
            $context,
            $site,
            $siteLanguage,
            $pageArguments,
            $frontendUser
        );

        $GLOBALS['TSFE'] = $controller;

        $GLOBALS['TYPO3_REQUEST'] = ($GLOBALS['TYPO3_REQUEST'] ?? new ServerRequest())->withAttribute(
            'frontend.controller',
            $controller
        );

        return $requestBackup;
    }

    /**
     * Resets the frontend request if it was previously changed by simulateFrontendEnvironment()
     *
     * @see simulateFrontendEnvironment()
     */
    public static function resetFrontendEnvironment(?ServerRequestInterface $request): void
    {
        if (!ContextUtility::isBackend() || VersionUtility::isCoreAtLeast14()) {
            return;
        }

        unset($GLOBALS['TSFE']);

        if (!$request) {
            unset($GLOBALS['TYPO3_REQUEST']);
            return;
        }

        $GLOBALS['TYPO3_REQUEST'] = $request;
    }
}
