<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup'] = unserialize($_EXTCONF);
if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling']) || !$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling']) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['usePageCache'][] = 'FluidTYPO3\\Vhs\\Service\\AssetService';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'FluidTYPO3\\Vhs\\Service\\AssetService->buildAllUncached';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'FluidTYPO3\\Vhs\\Service\\AssetService->clearCacheCommand';
}

if (FALSE === is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_main'] = [
		'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
		'options' => [
			'defaultLifetime' => 804600
		],
		'groups' => ['pages', 'all']
	];
}

if (FALSE === is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vhs_markdown'] = [
		'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend',
		'options' => [
			'defaultLifetime' => 804600
		],
		'groups' => ['pages', 'all']
	];
}

// add navigtion hide, url and urltype to fix the rendering of external url doktypes
$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= (TRUE === empty($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields']) ? '' : ',') . 'nav_hide,url,urltype';

// manual patching to add namelesscoder/typo3-cms-fluid-gap, but only when TYPO3 is *NOT* in composer mode and only
// if the classes are *NOT* already loaded by another extension using this same trick. Note, on TYPO3 8.4+ the classes
// will already exist in Fluid itself and we can get by with a simple class alias.
if (
    !(
        defined('TYPO3_COMPOSER_MODE')
        && TYPO3_COMPOSER_MODE
        && class_exists(\NamelessCoder\FluidGap\Traits\CompileWithRenderStatic::class)
        && class_exists(\NamelessCoder\FluidGap\Traits\CompileWithContentArgumentAndRenderStatic::class)
    )
) {
    if (defined('TYPO3_branch') && TYPO3_branch === '7.6') {
        $fluidGapTraitsPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('vhs', 'Resources/Private/Php/FluidGap/');
        require_once $fluidGapTraitsPath . 'CompileWithRenderStatic.php';
        require_once $fluidGapTraitsPath . 'CompileWithContentArgumentAndRenderStatic.php';
        unset($fluidGapTraitsPath);
    } else {
        class_alias(\TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic::class, 'NamelessCoder\\FluidGap\\Traits\\CompileWithRenderStatic');
        class_alias(\TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic::class, 'NamelessCoder\\FluidGap\\Traits\\CompileWithContentArgumentAndRenderStatic');
    }
}
