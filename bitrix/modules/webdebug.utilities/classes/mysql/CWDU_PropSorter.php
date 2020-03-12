<?
IncludeModuleLangFile(__FILE__);

class CWDU_PropSorter {
	
	const TableName = 'b_wdu_propsorter';
	
	/**
	 *	Get all iblock properties
	 */
	public static function getIBlockProperties($intIBlockId){
		$arResult = [];
		$resProps = CIBlockProperty::GetList(['SORT'=>'ASC','ID'=>'ASC'], ['IBLOCK_ID'=>$intIBlockId,'ACTIVE'=>'Y']);
		while ($arProp = $resProps->GetNext(false,false)) {
			$arResult[$arProp['ID']] = $arProp;
		}
		return $arResult;
	}
	
	/*
	function OnEpilog_Handler() {
		$IBlockID = IntVal($_GET['ID']);
		CJSCore::Init(array('jquery','translit'));
		$arGroups = self::GetPropGroupsArray($IBlockID);
		?>
		<script src="/bitrix/js/webdebug.utilities/jquery.ui.sortable.js"></script>
		<script>
		///
		$(document).ready(function(){
			var Table = $('#ib_prop_list');
			Table.addClass('wd_propsorter');
			var TableHeader = Table.find('tr.heading').prepend('<td class="wd_sortable wd_sortable_header"></td>');
			// Move header to thead
			if (Table.find('thead').size()==0) {
				Table.prepend('<thead></thead>');
				TableHeader.appendTo(Table.find('thead'));
			}
			Table.find('tbody').find('tr').each(function(){
				// Add handle
				var RowID = $(this).attr('id');
				if (RowID!=undefined) {
					if (RowID.match(/IB_PROPERTY_(n|)(\d)/g)) {
						$(this).prepend('<td><div class="wd_sortable wd_sortable_y"><span class="wd_sortable_move"></span></div></td>');
					}
				}
			});
			// Sortable
			var WD_SortableHelper = function(Event, TR) {
				var $originals = TR.children();
				var $helper = TR.clone();
				$helper.children().each(function(index) {
					$(this).width($originals.eq(index).width());
				});
				return $helper;
			},
			WD_SortableOnStop = function(Event, UI) {
				$('td.index', UI.item.parent()).each(function (i) {
					$(this).html(i + 1);
				});
			};
			WD_SortableSetPropsSort = function(){
				var Sort = 0,
						Step = 10;
				Table.find('tbody tr').each(function(){
					$(this).find('input[name$=_SORT],input[name^=WD_GROUP_SORT]').each(function(){
						Sort += Step;
						$(this).val(Sort);
					});
				});
			}
			var WD_GroupIndex=0;
			WD_SortableCheckboxID = function(){
				return 'wd_checkbox_'+Math.round(Math.random() * 100000000);
			}
			WD_SortableCreateGroup = function(Name,Code,Sort,Active,CheckboxID){
				WD_GroupIndex++;
				if (Code==false) {
					Code = BX.translit(Name,{
						'max_len': '100',
						'change_case': 'U',
						'replace_space': '_',
						'replace_other': '_',
						'delete_repeat_replace': true,
						'use_google': false,
					});
				}
				var strResult = '';
				strResult += '<tr class="wd_group">';
				strResult += 	'<td><div class="wd_group_sortable wd_sortable wd_sortable_y"><span class="wd_sortable_move"></span></div></td>';
				strResult += 	'<td></td>';
				strResult += 	'<td colspan="2" class="wd_group_name"><input type="text" size="40" maxlength="255" name="WD_GROUP_NAME['+WD_GroupIndex+']" value="'+Name+'" style="width:96%" /></td>';
				strResult += 	'<td style="text-align:center"><input type="checkbox" name="WD_GROUP_ACTIVE['+WD_GroupIndex+']" value="Y" id="'+CheckboxID+'_active" '+(Active=='Y'?'checked="checked"':'')+' /></td>';
				strResult += 	'<td></td>';
				strResult += 	'<td></td>';
				strResult += 	'<td><input type="text" size="3" maxlength="10" name="WD_GROUP_SORT['+WD_GroupIndex+']" value="'+Sort+'" /></td>';
				strResult += 	'<td class="wd_group_code"><input type="text" size="20" maxlength="50" name="WD_GROUP_CODE['+WD_GroupIndex+']" value="'+Code+'" /></td>';
				strResult += 	'<td><input type="button" value="..." style="visibility:hidden"/></td>';
				strResult += 	'<td class="wd_group_delete" align="center"><input type="checkbox" name="WD_GROUP_DEL['+WD_GroupIndex+']" value="Y" id="'+CheckboxID+'_delete" /></td>';
				strResult += '</tr>';
				return strResult;
			}
			WD_SortableStylizeCheckbox = function(CheckboxID){
				BX.adminFormTools.modifyCheckbox(document.getElementById(CheckboxID+'_active'));
				BX.adminFormTools.modifyCheckbox(document.getElementById(CheckboxID+'_delete'));
			}
			Table.find('tbody').sortable({
				handle:'.wd_sortable_y',
				helper: WD_SortableHelper,
				stop: WD_SortableOnStop,
				update: function(Event, UI) {
					WD_SortableSetPropsSort();
				}
			}).keydown(function(e){
				if (e.keyCode == 65 && e.ctrlKey) {
					e.target.select()
				}
			});
			// Add
			$('#wd_add_group').css('width',Table.outerWidth()).prependTo(Table.parent());
			$('#wd_add_group_btn').click(function(){
				var NewGroupName = $.trim($('#wd_add_group_name').val());
				if (NewGroupName!='') {
					var CheckboxID = WD_SortableCheckboxID();
					Table.find('tbody').prepend(WD_SortableCreateGroup(NewGroupName,false,'','Y',CheckboxID));
					WD_SortableStylizeCheckbox(CheckboxID);
					WD_SortableSetPropsSort();
					$('#wd_add_group_name').val('');
				}
			});
			// Insert groups
			<?foreach($arGroups as $arGroup):?>
			var WD_LastSort = 0,
					WD_GroupInserted = false;
			Table.find('tbody').find('tr').each(function(){
				var Sort = $(this).find('input[name$=_SORT],input[name^=WD_GROUP_SORT]').val();
				if (WD_GroupInserted==false && Sort>=<?=$arGroup['SORT']?> && WD_LastSort<=<?=$arGroup['SORT']?>) {
					var CheckboxID = WD_SortableCheckboxID();
					$(this).before(WD_SortableCreateGroup('<?=$arGroup['NAME']?>','<?=$arGroup['CODE']?>','<?=$arGroup['SORT']?>','<?=$arGroup['ACTIVE']?>',CheckboxID));
					WD_SortableStylizeCheckbox(CheckboxID);
					WD_GroupInserted = true;
				}
				WD_LastSort = Sort;
			});
			<?endforeach?>
			BX.addCustomEvent('onAdminTabsChange', function(){
				$('#wd_add_group').css('width',$('#ib_prop_list').outerWidth());
			});
			obIBProps.CELLS[0] = '<div class="wd_group_sortable wd_sortable wd_sortable_y"><span class="wd_sortable_move"></span></div>';
			obIBProps.CELLS.splice(1, 0, '');
			obIBProps.CELL_IND = 9;
		});
		</script>
		<div style="display:none">
			<div id="wd_add_group" style="margin:4px auto 12px;">
				<table class="internal">
					<tbody>
						<tr class="heading">
							<td><?=GetMessage('WDU_PROPSORTER_ADD_GROUP');?></td>
							<td><input type="text" name="new_group_name" value="" size="30" id="wd_add_group_name" /></td>
							<td><input type="button" value="<?=GetMessage('WDU_PROPSORTER_ADD_GROUP_BTN');?>" id="wd_add_group_btn" /></td>
						</tr>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?
	}
	function OnAfterIBlockUpdate_Handler($arFields) {
		if ($arFields['ID']==$_POST['ID']) {
			$arGroups = array();
			if (is_array($_POST['WD_GROUP_NAME'])) {
				foreach($_POST['WD_GROUP_NAME'] as $Key => $Value) {
					if ($_POST['WD_GROUP_DEL'][$Key]=='Y') {
						continue;
					}
					$arGroups[] = array(
						trim($Value),
						trim(ToUpper($_POST['WD_GROUP_CODE'][$Key])),
						IntVal($_POST['WD_GROUP_SORT'][$Key]),
						$_POST['WD_GROUP_ACTIVE'][$Key] == 'Y' ? 'Y' : 'N',
					);
				}
			}
			self::SetValue('iblock_groups_'.$arFields['ID'],serialize($arGroups));
		}
	}
	function GetPropGroupsArray($intIBlockID) {
		$arResult = array();
		$strData = self::GetValue('iblock_groups_'.$intIBlockID);
		$arGroups = unserialize($strData);
		if ($arGroups!==false) {
			$GroupIndex = 0;
			foreach($arGroups as $arGroup) {
				$GroupCode = $arGroup[1];
				if (!strlen($arGroup[1])) {
					$GroupCode = ++$GroupIndex;
				}
				$arResult[$GroupCode] = array(
					'NAME' => $arGroup[0],
					'CODE' => $arGroup[1],
					'SORT' => $arGroup[2],
					'ACTIVE' => $arGroup[3]=='Y' ? 'Y' : 'N',
				);
			}
		}
		return $arResult;
	}
	function GetPropGroupsParentGroupKey($arGroups, $ItemSort) {
		$strResultKey = false;
		if (is_array($arGroups) && $ItemSort>0) {
			foreach($arGroups as $GroupKey => $arGroup) {
				$GroupSort = $arGroup['SORT'];
				if ($ItemSort>$GroupSort) {
					$strResultKey = $GroupKey;
				}
			}
		}
		return $strResultKey;
	}
	function GetValue($Name) {
		global $DB;
		$Name = $DB->ForSQL($Name);
		$Table = self::TableName;
		$SQL = "SELECT `VALUE` FROM `{$Table}` WHERE `NAME`='{$Name}';";
		$resItem = $DB->Query($SQL);
		if ($arItem = $resItem->GetNext()) {
			return $arItem['~VALUE'];
		}
		return false;
	}
	function SetValue($Name, $Value) {
		global $DB;
		$Name = $DB->ForSQL($Name);
		$Value = $DB->ForSQL($Value);
		$Table = self::TableName;
		$SQL = "SELECT `ID` FROM `{$Table}` WHERE `NAME`='{$Name}';";
		$resItem = $DB->Query($SQL);
		if ($arItem = $resItem->GetNext(false,false)) {
			$SQL = "UPDATE `{$Table}` SET `NAME`='{$Name}',`VALUE`='{$Value}' WHERE `ID`='{$arItem['ID']}';";
		} else {
			$SQL = "INSERT INTO `{$Table}` (`NAME`,`VALUE`) VALUES ('{$Name}','{$Value}');";
		}
		$resResult = $DB->Query($SQL);
		return $resResult->result===true;
	}
	*/
}

?>