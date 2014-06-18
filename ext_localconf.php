<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = '\FluidTYPO3\Vhs\Service\AssetService->buildAll';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = '\FluidTYPO3\Vhs\Service\AssetService->buildAllUncached';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = '\FluidTYPO3\Vhs\Service\AssetService->clearCacheCommand';


if (FALSE === is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'] = array(
		'frontend' => 'TYPO3\CMS\Core\Cache\Frontend\StringFrontend',
		'options' => array(
			'defaultLifetime' => 804600
		),
		'groups' => array('pages', 'all')
	);
}

if (FALSE === is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'] = array(
		'frontend' => 'TYPO3\CMS\Core\Cache\Frontend\StringFrontend',
		'options' => array(
			'defaultLifetime' => 804600
		),
		'groups' => array('pages', 'all')
	);
}
