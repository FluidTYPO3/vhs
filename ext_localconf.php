<?php

(function() {
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs'] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('vhs');

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
                // You should keep this value HIGHER than the lifetime of TYPO3's page caches at all times.
                'defaultLifetime' => 804600
            ],
            'groups' => ['pages', 'all']
        ];
    }

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['v'] = ['FluidTYPO3\\Vhs\\ViewHelpers'];
})();
