<?
if(!$USER->IsAdmin()) return;

$module_id = 'webdebug.utilities';
CModule::IncludeModule($module_id);
CJSCore::Init('jquery2');

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php"); 
IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	array(
		'NAME' => GetMessage('WDU_GENERAL'),
		'ITEMS' => array(
			array('global_main_functions', GetMessage('WDU_GLOBAL_MAIN_FUNCTIONS'), false, array('checkbox'), GetMessage('WDU_GLOBAL_MAIN_FUNCTIONS_HINT')),
			array('js_debug_functions', GetMessage('WDU_JS_DEBUG_FUNCTIONS'), false, array('checkbox'), GetMessage('WDU_JS_DEBUG_FUNCTIONS_HINT')),
			array('set_admin_favicon', GetMessage('WDU_SET_ADMIN_FAVICON'), false, array('checkbox'), GetMessage('WDU_SET_ADMIN_FAVICON_HINT')),
			array('admin_favicon', GetMessage('WDU_ADMIN_FAVICON'), false, array('file'), GetMessage('WDU_ADMIN_FAVICON_HINT')),
			array('pageprops_enabled', GetMessage('WDU_PAGEPROPS_ENABLED'), false, array('checkbox'), GetMessage('WDU_PAGEPROPS_ENABLED_HINT')),
			array('prevent_logout', GetMessage('WDU_PREVENT_LOGOUT'), true, array('checkbox'), GetMessage('WDU_PREVENT_LOGOUT_HINT')),
		),
	),
	array(
		'NAME' => GetMessage('WDU_IBLOCK'),
		'ITEMS' => array(
			array('iblock_add_detail_link', GetMessage('WDU_IBLOCK_ADD_DETAIL_LINK'), false, array('checkbox'), GetMessage('WDU_IBLOCK_ADD_DETAIL_LINK_HINT')),
			array('iblock_show_element_id', GetMessage('WDU_IBLOCK_SHOW_ELEMENT_ID'), false, array('checkbox'), GetMessage('WDU_IBLOCK_SHOW_ELEMENT_ID_HINT')),
			array('iblock_rename_columns', GetMessage('WDU_IBLOCK_RENAME_COLUMNS'), false, array('checkbox'), GetMessage('WDU_IBLOCK_RENAME_COLUMNS_HINT')),
		),
	),
	array(
		'NAME' => GetMessage('WDU_FASTSQL'),
		'ITEMS' => array(
			array('fastsql_enabled', GetMessage('WDU_FASTSQL_ENABLED'), false, array('checkbox'), GetMessage('WDU_FASTSQL_ENABLED_HINT')),
			array('fastsql_auto_exec', GetMessage('WDU_FASTSQL_AUTO_EXEC'), false, array('select'), GetMessage('WDU_FASTSQL_AUTO_EXEC_HINT'), array('N'=>GetMessage('WDU_FASTSQL_AUTO_EXEC_N'),'Y'=>GetMessage('WDU_FASTSQL_AUTO_EXEC_Y'),'X'=>GetMessage('WDU_FASTSQL_AUTO_EXEC_X'))),
		),
	),
	array(
		'NAME' => GetMessage('WDU_HEADERS'),
		'ITEMS' => array(
			array('server_headers_add', GetMessage('WDU_HEADERS_ADD'), false, array('custom'), GetMessage('WDU_HEADERS_ADD_HINT')),
			array('server_headers_remove', GetMessage('WDU_HEADERS_REMOVE'), false, array('custom'), GetMessage('WDU_HEADERS_REMOVE_HINT')),
		),
	),
	array(
		'NAME' => GetMessage('WDU_MISC'),
		'ITEMS' => array(
			#array('', GetMessage(''), false, array('checkbox'), GetMessage('_HINT')),
		),
	),
);

$aTabs = array();
$aTabs[] = array("DIV" => "tab_general", "TAB" => GetMessage("WDU_TAB_GENERAL_NAME"), "TITLE" => GetMessage("WDU_TAB_GENERAL_DESC"));
$aTabs[] = array("DIV" => "tab_rights", "TAB" => GetMessage("WDU_TAB_RIGHTS_NAME"), "TITLE" => GetMessage("WDU_TAB_RIGHTS_DESC"));
$tabControl = new CAdminTabControl("tabControl", $aTabs);
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
	if(strlen($RestoreDefaults)>0) {
		$arGroups = array();
		$resGroups = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($arGroup = $resGroups->GetNext(false,false)) {
			$arGroups[] = $arGroup["ID"];
		}
		$APPLICATION->DelGroupRight($module_id, $arGroups);
		COption::RemoveOption($module_id);
		LocalRedirect($_SERVER["REQUEST_URI"]);
	} else {
		for ($i=0; $i<10; $i++) {
			COption::SetOptionString($module_id, "vote_name_".$i, $_REQUEST["webdebug_votes"][$i], GetMessage("WD_REVIEWS2_VOTE_FIELD_NAME").$i);
		}
		foreach($arAllOptions as $arOptionGroup) {
			foreach($arOptionGroup['ITEMS'] as $arOption) {
				$name = $arOption[0];
				$val = $_REQUEST[$name];
				$isUserOption = !!$arOption[2];
				#
				if(in_array($name, array('server_headers_add', 'server_headers_remove'))) {
					$val = is_array($val) ? $val : array();
					foreach($val as $key => $subVal){
						if(trim($subVal)==''){
							unset($val[$key]);
						}
					}
					if(empty($val)){
						$val = '';
					}
					else {
						$val = serialize($val);
					}
				}
				else{
					if($arOption[3][0]=="checkbox" && $val!="Y") $val="N";
				}
				#
				if($isUserOption){
					CUserOptions::SetOption($module_id, $name, $val);
				}
				else{
					COption::SetOptionString($module_id, $name, $val);
				}
			}
		}
	}
}

function wdUtilitiesHeadersBlock($strOptionName){
	global $module_id;
	$arOptions = COption::GetOptionString($module_id, $strOptionName);
	if(strlen($arOptions)){
		$arOptions = unserialize($arOptions);
	}
	if(!is_array($arOptions)){
		$arOptions = array();
	}
	if(empty($arOptions)){
		$arOptions[] = false;
	}
	$bRemove = $strOptionName == 'server_headers_remove';
	$bAdd = !$bRemove;
	$strClass = 'wd-utilities-headers-'.($bRemove ? 'remove' : 'add');
	ob_start();
	?>
	<style>
	tr#option_<?=$strOptionName;?> > td:first-child {padding-top:14px; vertical-align:top;}
	.<?=$strClass;?> input[data-role="headers-rule--delete"] {height:25px!important;}
	.<?=$strClass;?> tbody tr:first-child input[data-role="headers-rule--delete"] {display:none;}
	</style>
	<table class="<?=$strClass;?>">
		<tbody>
			<?foreach($arOptions as $strValue):?>
				<tr>
					<td>
						<input type="text" name="<?=$strOptionName;?>[]" value="<?=$strValue;?>"size="<?=($bAdd?'50':'20')?>" 
							placeholder="<?=GetMessage('WDU_HEADERS_'.($bAdd?'ADD':'REMOVE').'_PLACEHOLDER');?>" />
					</td>
					<td>
						<input type="button" value="&times;" title="<?=GetMessage('WDU_HEADERS_ITEM_DELETE');?>" data-role="headers-rule--delete" />
					</td>
				</tr>
			<?endforeach?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">
					<input type="button" value="<?=GetMessage('WDU_HEADERS_ITEM_ADD');?>" data-role="headers-rule--add" />
				</td>
			</tr>
		</tfoot>
	</table>
	<script>
	$(document).delegate('.<?=$strClass;?> input[data-role="headers-rule--add"]', 'click', function(e){
		var body = $(this).closest('table').children('tbody');
		var row = body.find('tr').first().clone();
		row.find('input[type=text]').val('');
		body.append(row);
	});
	$(document).delegate('.<?=$strClass;?> input[data-role="headers-rule--delete"]', 'click', function(e){
		var row = $(this).closest('tr');
		var body = $(this).closest('tbody');
		if(body.children('tr').length > 1){
			row.remove();
		}
	});
	</script>
	<?
	return ob_get_clean();
}

?>

<?if(CModule::IncludeModule($module_id)):?>
	<?$tabControl->Begin();?>
	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>" id="webdebug-reviews-table">
		<?$tabControl->BeginNextTab();?>
		<?foreach($arAllOptions as $arOptionGroup):?>
			<?if(!is_array($arOptionGroup['ITEMS']) || empty($arOptionGroup['ITEMS'])){continue;}?>
			<tr class="heading"><td colspan="2"><?=$arOptionGroup['NAME'];?></td></tr>
			<?foreach($arOptionGroup['ITEMS'] as $arOption):?>
				<?
					$type = $arOption[3];
					$OptionHint = $arOption[4];
					$OptionValues = $arOption[5];
					$isUserOption = !!$arOption[2];
					if($isUserOption){
						$val = CUserOptions::GetOption($module_id, $arOption[0]);
					}
					else {
						$val = COption::GetOptionString($module_id, $arOption[0]);
					}
				?>
				<tr id="option_<?=$arOption[0];?>">
					<td width="50%">
						<?=CWD_Util::AdminShowHint($OptionHint);?>
						<?if(in_array($arOption[0], array('server_headers_add', 'server_headers_remove'))):?>
							<?=$arOption[1];?>:
						<?else:?>
							<?if(in_array($type[0],array('checkbox','file','select'))):?>
								<label for="<?=htmlspecialchars($arOption[0]);?>"><?=$arOption[1];?></label>:
							<?else:?>
								<?=$arOption[1];?>:
							<?endif?>
						<?endif?>
					</td>
					<td width="50%">
						<?if($type[0]=="checkbox"):?>
							<input type="checkbox" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked='checked'";?> />
						<?elseif($type[0]=="text"):?>
							<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>" />
						<?elseif($type[0]=="select"):?>
							<select name="<?echo htmlspecialchars($arOption[0])?>">
								<?foreach ($OptionValues as $OptionValue => $OptionName):?>
									<option value="<?=$OptionValue?>"<?if($OptionValue==$val)echo" selected='selected'";?>><?=$OptionName?></option>
								<?endforeach?>
							</select>
						<?elseif($type[0]=="file"):?>
							<script>
							function WDU_<?=$arOption[0];?>_OnSelectFile(FileName,Path,Site){
								if (Path.length>1) {
									Path = Path + '/';
								}
								document.getElementById('<?=$arOption[0];?>').value = Path + FileName;
							}
							</script>
							<?
								$arDialogParams = array(
									'event' => 'WDU_'.$arOption[0].'_Open',
									'arResultDest' => array('FUNCTION_NAME' => 'WDU_'.$arOption[0].'_OnSelectFile'),
									'arPath' => array(),
									'select' => 'F',
									'operation' => 'O',
									'showUploadTab' => true,
									'showAddToMenuTab' => false,
									'fileFilter' => str_replace(' ','','ico,gif,png'),
									'allowAllFiles' => true,
									'saveConfig' => true,
								);
								CAdminFileDialog::ShowScript($arDialogParams);
							?>
							<input type="text" name="<?echo htmlspecialchars($arOption[0])?>" id="<?=$arOption[0];?>" value="<?=$val;?>" style="width:80%" />
							<input type="button" value="..." onclick="WDU_<?=$arOption[0];?>_Open()" />
						<?elseif($type[0]=="custom"):?>
							<?if(in_array($arOption[0], array('server_headers_add', 'server_headers_remove'))):?>
								<?=wdUtilitiesHeadersBlock($arOption[0]);?>
							<?endif?>
						<?endif?>
					</td>
				</tr>
			<?endforeach?>
		<?endforeach?>
		<?$tabControl->BeginNextTab();?>
			<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?$tabControl->Buttons();?>
			<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" class="adm-btn-save" />
			<input type="hidden" name="Update" value="Y" />
			<input type="submit" name="Apply" value="<?=GetMessage("MAIN_APPLY")?>" />
			<input type="submit" name="RestoreDefaults" value="<?=GetMessage("MAIN_RESET")?>" onclick="return confirm('<?=AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>');" />
			<?if(strlen($_REQUEST["back_url_settings"])>0):?>
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
			<?endif?>
			<?=bitrix_sessid_post();?>
		<?$tabControl->End();?>
		<script>
		$(document).delegate('#set_admin_favicon', 'change', function(e){
			var faviconRow = $('#admin_favicon').closest('tr');
			if($(this).is(':checked')){
				faviconRow.show();
			}
			else {
				faviconRow.hide();
			}
		});
		$('#set_admin_favicon').trigger('change');
		</script>
	</form>
<?else:?>
	<p><?=GetMessage("WD_REVIEWS2_ERROR_MODULE_NOT_INCLUDED")?></p>
<?endif?>

<?
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
	if (strlen($Apply)>0) {
		LocalRedirect($_SERVER["REQUEST_URI"]."&".$tabControl->ActiveTabParam());
	} elseif (strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0) {
		LocalRedirect($_REQUEST["back_url_settings"]);
	} else {
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
	}
}
?>