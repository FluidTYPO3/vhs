<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/**
 * Polyfill ext-mbstring if not present. Can be removed with TYPO3 8.7 minimum-compatibility.
 */
if (false === function_exists('mb_strlen') || false === function_exists('mb_chr')) {
    if (!function_exists('mb_strlen')) {
        define('MB_CASE_UPPER', 0);
        define('MB_CASE_LOWER', 1);
        define('MB_CASE_TITLE', 2);

        function mb_convert_encoding($s, $to, $from = null) { return FluidTYPO3\Vhs\Mbstring::mb_convert_encoding($s, $to, $from); }
        function mb_decode_mimeheader($s) { return FluidTYPO3\Vhs\Mbstring::mb_decode_mimeheader($s); }
        function mb_encode_mimeheader($s, $charset = null, $transferEnc = null, $lf = null, $indent = null) { return FluidTYPO3\Vhs\Mbstring::mb_encode_mimeheader($s, $charset, $transferEnc, $lf, $indent); }
        function mb_convert_case($s, $mode, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_convert_case($s, $mode, $enc); }
        function mb_internal_encoding($enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_internal_encoding($enc); }
        function mb_language($lang = null) { return FluidTYPO3\Vhs\Mbstring::mb_language($lang); }
        function mb_list_encodings() { return FluidTYPO3\Vhs\Mbstring::mb_list_encodings(); }
        function mb_encoding_aliases($encoding) { return FluidTYPO3\Vhs\Mbstring::mb_encoding_aliases($encoding); }
        function mb_check_encoding($var = null, $encoding = null) { return FluidTYPO3\Vhs\Mbstring::mb_check_encoding($var, $encoding); }
        function mb_detect_encoding($str, $encodingList = null, $strict = false) { return FluidTYPO3\Vhs\Mbstring::mb_detect_encoding($str, $encodingList, $strict); }
        function mb_detect_order($encodingList = null) { return FluidTYPO3\Vhs\Mbstring::mb_detect_order($encodingList); }
        function mb_parse_str($s, &$result = array()) { parse_str($s, $result); }
        function mb_strlen($s, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strlen($s, $enc); }
        function mb_strpos($s, $needle, $offset = 0, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strpos($s, $needle, $offset, $enc); }
        function mb_strtolower($s, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strtolower($s, $enc); }
        function mb_strtoupper($s, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strtoupper($s, $enc); }
        function mb_substitute_character($char = null) { return FluidTYPO3\Vhs\Mbstring::mb_substitute_character($char); }
        function mb_substr($s, $start, $length = 2147483647, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_substr($s, $start, $length, $enc); }
        function mb_stripos($s, $needle, $offset = 0, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_stripos($s, $needle, $offset, $enc); }
        function mb_stristr($s, $needle, $part = false, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_stristr($s, $needle, $part, $enc); }
        function mb_strrchr($s, $needle, $part = false, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strrchr($s, $needle, $part, $enc); }
        function mb_strrichr($s, $needle, $part = false, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strrichr($s, $needle, $part, $enc); }
        function mb_strripos($s, $needle, $offset = 0, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strripos($s, $needle, $offset, $enc); }
        function mb_strrpos($s, $needle, $offset = 0, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strrpos($s, $needle, $offset, $enc); }
        function mb_strstr($s, $needle, $part = false, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strstr($s, $needle, $part, $enc); }
        function mb_get_info($type = 'all') { return FluidTYPO3\Vhs\Mbstring::mb_get_info($type); }
        function mb_http_output($enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_http_output($enc); }
        function mb_strwidth($s, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_strwidth($s, $enc); }
        function mb_substr_count($haystack, $needle, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_substr_count($haystack, $needle, $enc); }
        function mb_output_handler($contents, $status) { return FluidTYPO3\Vhs\Mbstring::mb_output_handler($contents, $status); }
        function mb_http_input($type = '') { return FluidTYPO3\Vhs\Mbstring::mb_http_input($type); }
        function mb_convert_variables($toEncoding, $fromEncoding, &$a = null, &$b = null, &$c = null, &$d = null, &$e = null, &$f = null) { return FluidTYPO3\Vhs\Mbstring::mb_convert_variables($toEncoding, $fromEncoding, $a, $b, $c, $d, $e, $f); }
    }

    if (!function_exists('mb_chr')) {
        function mb_ord($s, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_ord($s, $enc); }
        function mb_chr($code, $enc = null) { return FluidTYPO3\Vhs\Mbstring::mb_chr($code, $enc); }
        function mb_scrub($s, $enc = null) { $enc = null === $enc ? mb_internal_encoding() : $enc; return mb_convert_encoding($s, $enc, $enc); }
    }
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

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['v'] = ['FluidTYPO3\\Vhs\\ViewHelpers'];

// add navigtion hide to fix menu viewHelpers (e.g. breadcrumb)
$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= (TRUE === empty($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields']) ? '' : ',') . 'nav_hide';

// add and urltype to fix the rendering of external url doktypes
if (isset($GLOBALS['TCA']['pages']['columns']['urltype'])) {
    $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ',url,urltype';
}
