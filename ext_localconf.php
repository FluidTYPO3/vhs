<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'Tx_Vhs_Service_AssetService->buildAll';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'Tx_Vhs_Service_AssetService->buildAllUncached';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'Tx_Vhs_Service_AssetService->clearCacheCommand';


if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'] = array(
		'frontend' => 'TYPO3\CMS\Core\Cache\Frontend\StringFrontend',
		'options' => array(
			'defaultLifetime' => 804600
		),
		'groups' => array('pages', 'all')
	);
}

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'] = array(
		'frontend' => 'TYPO3\CMS\Core\Cache\Frontend\StringFrontend',
		'options' => array(
			'defaultLifetime' => 804600
		),
		'groups' => array('pages', 'all')
	);
}
