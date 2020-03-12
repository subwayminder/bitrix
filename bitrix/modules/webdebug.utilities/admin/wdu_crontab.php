<?
$ModuleID = 'webdebug.utilities';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
IncludeModuleLangFile(__FILE__);

$ModuleRights = $APPLICATION->GetGroupRight($ModuleID);
if($ModuleRights=="D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$ModuleRights = $APPLICATION->GetGroupRight($ModuleID);
if($ModuleRights=="D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "WDU_Crontab";
$lAdmin = new CAdminList($sTableID);

// Processing with group actions
if(($arID = $lAdmin->GroupAction())) {
	/*
  if($_REQUEST['action_target']=='selected') {
    $rsData = CWDU_FastSQL::GetList(array($by=>$order), $arFilter, false, $arNavParams);
    while($arRes = $rsData->Fetch()) {
			$arID[] = $arRes['ID'];
		}
  }
	*/
  foreach($arID as $ID) {
		@set_time_limit(0);
    switch($_REQUEST['action']) {
			case "delete":
				$bDeleted = false;
				$arFullCommand = CWDU_Crontab::ExplodeFullCommand($ID);
				if(is_array($arFullCommand)) {
					$strSchedule = $arFullCommand['SCHEDULE'];
					$strCommand = $arFullCommand['COMMAND'];
					if(!empty($strSchedule) && !empty($strCommand)) {
						$bDeleted = CWDU_Crontab::Delete($strCommand, $strSchedule);
					}
				}
				if(!$bDeleted) {
					$lAdmin->AddGroupError('Delete error: '.$ID);
				}
				break;
    }
  }
}

if (!is_array($arFilter)) {
	$arFilter = array();
}

// Get items list
$arCrontab = array();
$arCrontab = CWDU_Crontab::GetList();
$rsData = new CDBResult();
$rsData->InitFromArray($arCrontab);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(''));

// Add headers
$arHeaders = array(
  array(
	  "id" => "MINUTE",
    "content" => GetMessage('WDU_FIELD_MINUTE'),
    "sort" => "MINUTE",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "HOUR",
    "content" => GetMessage('WDU_FIELD_HOUR'),
    "sort" => "HOUR",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "DAY",
    "content" => GetMessage('WDU_FIELD_DAY'),
    "sort" => "DAY",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "MONTH",
    "content" => GetMessage('WDU_FIELD_MONTH'),
    "sort" => "MONTH",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "WEEKDAY",
    "content" => GetMessage('WDU_FIELD_WEEKDAY'),
    "sort" => "WEEKDAY",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"PATH",
    "content" => GetMessage('WDU_FIELD_PATH'),
    "sort" => "PATH",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"COMMAND",
    "content" => GetMessage('WDU_FIELD_COMMAND'),
    "sort" => "COMMAND",
		"align" => "left",
    "default" => false,
  ),
);
$lAdmin->AddHeaders($arHeaders);

// Build items list
while ($arRes = $rsData->NavNext(true, "f_", false)) {
  $obRow = &$lAdmin->AddRow(htmlspecialcharsbx($f_ID), $arRes);
	$obRow->AddViewField('MINUTE', htmlspecialcharsbx($f_MINUTE));
	$obRow->AddViewField('HOUR', htmlspecialcharsbx($f_HOUR));
	$obRow->AddViewField('DAY', htmlspecialcharsbx($f_DAY));
	$obRow->AddViewField('MONTH', htmlspecialcharsbx($f_MONTH));
	$obRow->AddViewField('WEEKDAY', htmlspecialcharsbx($f_WEEKDAY));
	$obRow->AddViewField('COMMAND', '<a href="/bitrix/admin/wdu_crontab_edit.php?ID='.urlencode($f_COMMAND).'&lang='.LANGUAGE_ID.'">'.htmlspecialcharsbx($f_COMMAND).'</a>');
	$obRow->AddViewField('PATH', '<a href="/bitrix/admin/wdu_crontab_edit.php?ID='.urlencode($f_COMMAND).'&lang='.LANGUAGE_ID.'">'.htmlspecialcharsbx($f_PATH).'</a>');
	
	// Build context menu
  $arActions = array();
	$arActions[] = array(
		"ICON" => "edit",
		"DEFAULT" => false,
		"TEXT" => GetMessage('WDU_CONTEXT_EDIT'),
		"ACTION" => $lAdmin->ActionRedirect("/bitrix/admin/wdu_crontab_edit.php?ID=".urlencode($f_COMMAND)."&lang=".LANGUAGE_ID)
	);
	$arActions[] = array(
		"ICON" => "delete",
		"DEFAULT" => false,
		"TEXT" => GetMessage('WDU_CONTEXT_DELETE'),
		"ACTION" => "if(confirm('".GetMessage('WDU_CONTEXT_DELETE_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_ID, 'delete')
	);
  $obRow->AddActions($arActions);
}

// List Footer
$lAdmin->AddFooter(
  array(
    array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
    array("counter"=>true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
  )
);
$lAdmin->AddGroupActionTable(Array(
  "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

// Context menu
global $APPLICATION;
$aContext = array(
  array(
    "TEXT" => GetMessage('WDU_CONTEXT_ADD'),
    "LINK" => "wdu_crontab_edit.php?lang=".LANGUAGE_ID,
    "ICON" => "btn_new",
  ),
);
$lAdmin->AddAdminContextMenu($aContext);

// Start output
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('WDU_PAGE_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?if(!CWDU_Crontab::CanAutoSet()):?>
	<?print BeginNote();?>
	<?=GetMessage('WDU_CRONTAB_CANNOT_AUTOSET');?>
	<?print EndNote();?>
<?elseif(CWDU_Crontab::IsTimeweb()):?>
	<?print BeginNote();?>
	<?=GetMessage('WDU_CRONTAB_IS_TIMEWEB');?>
	<?print EndNote();?>
<?else:?>
	<?// Output ?>
	<?$lAdmin->DisplayList();?>
<?endif;?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>