<?
$ModuleID = 'webdebug.utilities';
IncludeModuleLangFile(__FILE__);

if(CModule::IncludeModule($ModuleID) && $APPLICATION->GetGroupRight($ModuleID)>='R') {

	$arSubmenu = array();
	
	// PropSorter
	$arSubmenu[] = array(
		'text' => GetMessage('WEBDEBUG_UTILITIES_MENU_PROPSORTER'),
		'more_url' => array(),
		'url' => 'wdu_propsorter.php?lang='.LANGUAGE_ID,
		'icon' => 'wd_utils_icon_propsorter',
	);
	
	// Fast SQL
	$arSubmenu[] = array(
		'text' => GetMessage('WEBDEBUG_UTILITIES_MENU_FASTSQL'),
		'more_url' => array('wdu_fastsql_edit.php'),
		'url' => 'wdu_fastsql_list.php?lang='.LANGUAGE_ID,
		'icon' => 'wd_utils_icon_fastsql',
	);
	
	// Options
	$arSubmenu[] = array(
		'text' => GetMessage('WEBDEBUG_UTILITIES_MENU_OPTIONS'),
		'more_url' => array('wdu_option_edit.php'),
		'url' => 'wdu_option_list.php?lang='.LANGUAGE_ID,
		'icon' => 'wd_utils_icon_options',
	);
	
	// IBlock columns
	/*
	$arSubmenu[] = array(
		'text' => GetMessage('WEBDEBUG_UTILITIES_MENU_IBLOCK_COLUMNS'),
		'more_url' => array(),
		'url' => 'wdu_iblock_columns.php?lang='.LANGUAGE_ID,
		'icon' => 'wd_utils_icon_iblock_columns',
	);
	*/
	
	// Crontab
	$arSubmenu[] = array(
		'text' => GetMessage('WEBDEBUG_UTILITIES_MENU_CRONTAB'),
		'more_url' => array('wdu_crontab_edit.php'),
		'url' => 'wdu_crontab.php?lang='.LANGUAGE_ID,
		'icon' => 'wd_utils_icon_crontab',
	);
	
	// Dir size
	$arSubmenu[] = array(
		'text' => GetMessage('WEBDEBUG_UTILITIES_MENU_DIRSIZE'),
		'more_url' => array(),
		'url' => 'wdu_dirsize.php?lang='.LANGUAGE_ID,
		'icon' => 'wd_utils_icon_dirsize',
	);

	$aMenu = array(
		'parent_menu' => 'global_menu_settings',
		'section' => 'webdebug_utilities',
		'sort' => 1810,
		'text' => GetMessage('WEBDEBUG_UTILITIES_MENU_MAIN'),
		'icon' => 'wd_utils_icon_main',
		'items_id' => 'wd_utils_submenu',
		'items' => $arSubmenu,
	);
	return $aMenu;
}


return false;
?>
