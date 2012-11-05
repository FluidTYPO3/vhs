<?php

########################################################################
# Extension Manager/Repository config file for ext "vhs".
#
# Auto generated 05-11-2012 01:47
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'VHS: Fluid ViewHelpers',
	'description' => 'A collection of ViewHelpers to perform rendering tasks which are not natively supported by Fluid - for example: advanced formatters, math calculators, specialized conditions and Iterator/Array calculators and processors',
	'category' => 'misc',
	'author' => 'Claus Due',
	'author_email' => 'claus@wildside.dk',
	'author_company' => 'Wildside A/S',
	'shy' => '',
	'dependencies' => 'cms,extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5-0.0.0',
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:66:{s:12:"ext_icon.gif";s:4:"68b4";s:9:"README.md";s:4:"ab23";s:38:"Classes/ViewHelpers/CaseViewHelper.php";s:4:"49eb";s:40:"Classes/ViewHelpers/SwitchViewHelper.php";s:4:"47d0";s:51:"Classes/ViewHelpers/Condition/BackendViewHelper.php";s:4:"4b91";s:47:"Classes/ViewHelpers/Condition/CliViewHelper.php";s:4:"cf8e";s:52:"Classes/ViewHelpers/Condition/FrontendViewHelper.php";s:4:"109f";s:61:"Classes/ViewHelpers/Extension/AbstractExtensionViewHelper.php";s:4:"ff80";s:50:"Classes/ViewHelpers/Extension/LoadedViewHelper.php";s:4:"45cb";s:57:"Classes/ViewHelpers/Extension/Path/AbsoluteViewHelper.php";s:4:"bcfa";s:57:"Classes/ViewHelpers/Extension/Path/RelativeViewHelper.php";s:4:"a67b";s:58:"Classes/ViewHelpers/Extension/Path/ResourcesViewHelper.php";s:4:"59ab";s:61:"Classes/ViewHelpers/Extension/Path/SiteRelativeViewHelper.php";s:4:"42ad";s:45:"Classes/ViewHelpers/Form/SelectViewHelper.php";s:4:"2162";s:54:"Classes/ViewHelpers/Form/Select/OptgroupViewHelper.php";s:4:"7a5a";s:52:"Classes/ViewHelpers/Form/Select/OptionViewHelper.php";s:4:"66d9";s:50:"Classes/ViewHelpers/Format/EliminateViewHelper.php";s:4:"6f1b";s:45:"Classes/ViewHelpers/Format/HideViewHelper.php";s:4:"41de";s:49:"Classes/ViewHelpers/Format/MarkdownViewHelper.php";s:4:"721b";s:50:"Classes/ViewHelpers/Format/PlaintextViewHelper.php";s:4:"b76f";s:45:"Classes/ViewHelpers/Format/TidyViewHelper.php";s:4:"d74a";s:45:"Classes/ViewHelpers/Format/TrimViewHelper.php";s:4:"1a4e";s:48:"Classes/ViewHelpers/Format/UcfirstViewHelper.php";s:4:"b15f";s:59:"Classes/ViewHelpers/Format/Placeholder/LipsumViewHelper.php";s:4:"f1ba";s:59:"Classes/ViewHelpers/Format/Url/SanitizeStringViewHelper.php";s:4:"793a";s:59:"Classes/ViewHelpers/Iterator/AbstractIteratorViewHelper.php";s:4:"1f9b";s:51:"Classes/ViewHelpers/Iterator/ContainsViewHelper.php";s:4:"2593";s:50:"Classes/ViewHelpers/Iterator/ExplodeViewHelper.php";s:4:"d3c5";s:48:"Classes/ViewHelpers/Iterator/FirstViewHelper.php";s:4:"5ed8";s:50:"Classes/ViewHelpers/Iterator/ImplodeViewHelper.php";s:4:"351f";s:50:"Classes/ViewHelpers/Iterator/IndexOfViewHelper.php";s:4:"1a41";s:47:"Classes/ViewHelpers/Iterator/LastViewHelper.php";s:4:"8f6b";s:47:"Classes/ViewHelpers/Iterator/LoopViewHelper.php";s:4:"ba71";s:47:"Classes/ViewHelpers/Iterator/NextViewHelper.php";s:4:"9559";s:51:"Classes/ViewHelpers/Iterator/PreviousViewHelper.php";s:4:"ddce";s:47:"Classes/ViewHelpers/Iterator/SortViewHelper.php";s:4:"de0e";s:59:"Classes/ViewHelpers/Math/AbstractMultipleMathViewHelper.php";s:4:"f563";s:57:"Classes/ViewHelpers/Math/AbstractSingleMathViewHelper.php";s:4:"bf60";s:43:"Classes/ViewHelpers/Math/CeilViewHelper.php";s:4:"9566";s:47:"Classes/ViewHelpers/Math/DivisionViewHelper.php";s:4:"fdf9";s:44:"Classes/ViewHelpers/Math/FloorViewHelper.php";s:4:"38e4";s:45:"Classes/ViewHelpers/Math/ModuloViewHelper.php";s:4:"42d4";s:46:"Classes/ViewHelpers/Math/ProductViewHelper.php";s:4:"20be";s:44:"Classes/ViewHelpers/Math/RoundViewHelper.php";s:4:"ea63";s:47:"Classes/ViewHelpers/Math/SubtractViewHelper.php";s:4:"57a9";s:42:"Classes/ViewHelpers/Math/SumViewHelper.php";s:4:"9ae0";s:46:"Classes/ViewHelpers/Media/ExistsViewHelper.php";s:4:"d077";s:50:"Classes/ViewHelpers/Page/AbsoluteUrlViewHelper.php";s:4:"698a";s:51:"Classes/ViewHelpers/Page/AbstractMenuViewHelper.php";s:4:"7f4e";s:49:"Classes/ViewHelpers/Page/BreadCrumbViewHelper.php";s:4:"4171";s:51:"Classes/ViewHelpers/Page/LanguageMenuViewHelper.php";s:4:"356d";s:43:"Classes/ViewHelpers/Page/MenuViewHelper.php";s:4:"02f0";s:53:"Classes/ViewHelpers/Page/Content/FooterViewHelper.php";s:4:"e971";s:50:"Classes/ViewHelpers/Page/Content/GetViewHelper.php";s:4:"6ce5";s:53:"Classes/ViewHelpers/Page/Content/RenderViewHelper.php";s:4:"d8c2";s:50:"Classes/ViewHelpers/Page/Header/LinkViewHelper.php";s:4:"015d";s:50:"Classes/ViewHelpers/Page/Header/MetaViewHelper.php";s:4:"7af1";s:51:"Classes/ViewHelpers/Page/Header/TitleViewHelper.php";s:4:"a9e8";s:59:"Classes/ViewHelpers/Security/AbstractSecurityViewHelper.php";s:4:"a7e3";s:48:"Classes/ViewHelpers/Security/AllowViewHelper.php";s:4:"a282";s:47:"Classes/ViewHelpers/Security/DenyViewHelper.php";s:4:"4c33";s:41:"Classes/ViewHelpers/Var/GetViewHelper.php";s:4:"b1da";s:43:"Classes/ViewHelpers/Var/IssetViewHelper.php";s:4:"2db1";s:41:"Classes/ViewHelpers/Var/SetViewHelper.php";s:4:"05c8";s:48:"Classes/ViewHelpers/Var/TyposcriptViewHelper.php";s:4:"b6e0";s:43:"Classes/ViewHelpers/Var/UnsetViewHelper.php";s:4:"e594";}',
);

?>