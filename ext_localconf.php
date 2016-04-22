<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['usePageCache'][] = 'FluidTYPO3\\Vhs\\Service\\AssetService';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'FluidTYPO3\\Vhs\\Service\\AssetService->buildAllUncached';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'FluidTYPO3\\Vhs\\Service\\AssetService->clearCacheCommand';
$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= (true === empty($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields']) ? '' : ',') . 'nav_hide';

if (false === is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'] = array(
        'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
        'options' => array(
            'defaultLifetime' => 804600
        ),
        'groups' => array('pages', 'all')
    );
}

if (false === is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'] = array(
        'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
        'options' => array(
            'defaultLifetime' => 804600
        ),
        'groups' => array('pages', 'all')
    );
}

// add url and urltype to fix the rendering of external url doktypes
if (false === empty($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'])) {
    $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ',';
}
$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= 'url,urltype';
