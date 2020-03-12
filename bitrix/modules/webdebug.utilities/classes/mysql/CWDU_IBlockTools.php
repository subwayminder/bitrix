<?
IncludeModuleLangFile(__FILE__);

class CWDU_IBlockTools {
	
	const TableIBlockColumns = 'b_wdu_iblock_columns';
	
	function AddContextDetailLink(&$arMenuItems) {
		if (is_array($arMenuItems)) {
			foreach($arMenuItems as $Key => $arMenuItem) {
				if (is_array($arMenuItem['MENU']) && $arMenuItem['ICON']=='btn_new') {
					$resItem = CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$_GET['IBLOCK_ID'],'ID'=>$_GET['ID']),false,false,array('DETAIL_PAGE_URL'));
					if ($arItem = $resItem->GetNext(false,false)) {
						if (strlen($arItem['DETAIL_PAGE_URL'])) {
							$arMenuItems[$Key]['MENU'][] = array(
								'TEXT' => GetMessage('WDU_SHOW_ON_SITE'),
								'ACTION' => 'window.open("'.$arItem['DETAIL_PAGE_URL'].'");',
								'ICON' => 'view',
							);
						}
					}
					break;
				}
			}
		}
	}
	
	function DisplayElementIDInTabControlButtons(&$obTabControl) {
		if(defined('BX_PUBLIC_MODE')) {
			?>
			<script>
			BX.ready(function(){
				setTimeout(function(){
					if(document.getElementsByClassName) {
						var Div = document.getElementsByClassName('bx-core-adm-dialog-buttons');
					} else {
						var Div = document.querySelectorAll('.bx-core-adm-dialog-buttons');
					}
					if (Div.length==1) {
						var TmpDiv = document.createElement('div');
						TmpDiv.innerHTML = '<b>ID</b>: <?=$_GET['ID'];?>';
						while(TmpDiv.firstChild) {
								Div[0].appendChild(TmpDiv.firstChild);
						}
					}
				},250);
			});
			</script>
			<?
		} elseif (preg_match('#form_element_(\d+)#',$obTabControl->name)) {
			$obTabControl->sButtonsContent .= '<b>ID</b>: '.$_GET['ID'];
		}
	}
	
	function GetIBlockList() {
		$arResult = array();
		if (CModule::IncludeModule("iblock")) {
			// Get IBlock types
			$resIBlockTypes = CIBlockType::GetList(array(),array());
			while ($arIBlockType = $resIBlockTypes->GetNext(false,false)) {
				$arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID, false);
				$arResult[$arIBlockType["ID"]] = array(
					"NAME" => $arIBlockTypeLang["NAME"],
					"ITEMS" => array(),
				);
			}
			// Get IBlocks
			$arFilter = array();
			$arFilter["ACTIVE"] = "Y";
			$resIBlock = CIBlock::GetList(array("SORT"=>"ASC"),$arFilter);
			while ($arIBlock = $resIBlock->GetNext(false,false)) {
				$arResult[$arIBlock["IBLOCK_TYPE_ID"]]["ITEMS"][] = $arIBlock;
			}
		}
		return $arResult;
	}
	
}

?>