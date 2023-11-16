<?php

(function() {
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('vhs');

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling']) || !$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling']) {
        if (version_compare(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version(), '12.0', '<')) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['usePageCache'][] =  \FluidTYPO3\Vhs\Service\AssetService::class;
        }

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = \FluidTYPO3\Vhs\Service\AssetService::class . '->clearCacheCommand';
    }

    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'] ?? null)) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'options' => [
                'defaultLifetime' => 804600
            ],
            'groups' => ['pages', 'all']
        ];
    }

    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'] ?? null)) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'options' => [
                'defaultLifetime' => 804600
            ],
            'groups' => ['pages', 'all']
        ];
    }

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['v'] = ['FluidTYPO3\\Vhs\\ViewHelpers'];

    // add navigtion hide to fix menu viewHelpers (e.g. breadcrumb)
    $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= (empty($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields']) ? '' : ',') . 'nav_hide,shortcut,shortcut_mode';

    // add and urltype to fix the rendering of external url doktypes
    if (isset($GLOBALS['TCA']['pages']['columns']['urltype'])) {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ',url,urltype';
    }
})();
