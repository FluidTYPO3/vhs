<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
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
     * Sets the global variable $GLOBALS['TSFE'] in Backend mode.
     */
    public static function simulateFrontendEnvironment(): ?TypoScriptFrontendController
    {
        if (!ContextUtility::isBackend()) {
            return null;
        }
        $tsfeBackup = $GLOBALS['TSFE'] ?? null;

        $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieName'] = $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieName'] ?? 'fe_user';

        /** @var SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
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

        $controller = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $context,
            $site,
            $siteLanguage,
            $pageArguments,
            $frontendUser
        );

        $GLOBALS['TSFE'] = $controller;

        return $tsfeBackup;
    }

    /**
     * Resets $GLOBALS['TSFE'] if it was previously changed by simulateFrontendEnvironment()
     *
     * @see simulateFrontendEnvironment()
     */
    public static function resetFrontendEnvironment(?TypoScriptFrontendController $tsfeBackup): void
    {
        if (!ContextUtility::isBackend()) {
            return;
        }
        $GLOBALS['TSFE'] = $tsfeBackup;
    }
}
