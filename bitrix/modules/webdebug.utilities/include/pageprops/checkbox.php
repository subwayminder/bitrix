<?
IncludeModuleLangFile(__FILE__);

class CWDU_Pageprop_CheckBox extends CWDU_PagePropsAll {
	CONST CODE = 'CHECKBOX';
	CONST NAME = 'Флажок';
	function GetName() {
		$Name = self::NAME;
		if (CWDU_PageProps::IsUtf8()) {
			$Name = $GLOBALS['APPLICATION']->ConvertCharset($Name, 'CP1251', 'UTF-8');
		}
		return $Name;
	}
	function GetCode() {
		return self::CODE;
	}
	function GetMessage($Item) {
		$arMess = array(
			'NO_SETTINGS' => 'Настройки не требуются.',
		);
		$strResult = $arMess[$Item];
		if (CWDU_PageProps::IsUtf8()) {
			$strResult = $GLOBALS['APPLICATION']->ConvertCharset($strResult, 'CP1251', 'UTF-8');
		}
		return $strResult;
	}
	function ShowSettings($PropertyCode) {
		$arFilter = CWDU_PageProps::GetFilter($PropertyCode, $SiteID);
		$resCurrentProp = CWDU_PageProps::GetList(false,$arFilter);
		if ($arCurrentItem = $resCurrentProp->GetNext()) {
			$arCurrentItem = self::TransformItem($arCurrentItem);
		}
		if (!is_array($arCurrentItem['DATA'])) {
			$arCurrentItem['DATA'] = array();
		}
		$arCurrentItem['DATA'] = array_merge(array(false), $arCurrentItem['DATA']);
		ob_start();
		?>
			<div id="wd_pageprops_settings_selectbox">
				<?=self::GetMessage('NO_SETTINGS');?>
			</div>
		<?
		return ob_get_clean();
	}
	function SaveSettings($PropertyCode, $SiteID, $arPost) {
		$arFields = array(
			'PROPERTY' => $PropertyCode,
			'SITE' => $SiteID,
			'TYPE' => self::GetCode(),
			'DATA' => '',
		);
		$arFilter = CWDU_PageProps::GetFilter($PropertyCode, $SiteID);
		$resCurrentProp = CWDU_PageProps::GetList(false,$arFilter);
		if ($arCurrentItem = $resCurrentProp->GetNext(false,false)) {
			if (CWDU_PageProps::Update($arCurrentItem['ID'], $arFields)) {
				return true;
			}
		} else {
			if (CWDU_PageProps::Add($arFields)) {
				return true;
			}
		}
		return false;
	}
	function TransformItem($arItem) {
		$arItem['DATA'] = @unserialize($arItem['~DATA']);
		if (!is_array($arItem['DATA'])) {
			$arItem['DATA'] = array();
		}
		foreach($arItem['DATA'] as $Key => $arOption) {
			if ($Key=='') {
				unset($arItem['DATA'][$Key]);
			}
		}
		return $arItem;
	}
	function ShowControls($arItem, $PropertyCode, $PropertyID, $PropertyValue, $SiteID) {
		$arItem = self::TransformItem($arItem);
		$UniqID = rand(100000000,999999999);
		ob_start();
		?>
			<input type="checkbox" name="PROPERTY[<?=$PropertyID;?>][VALUE]" value="Y" id="wd_pageprops_checkbox_<?=$UniqID;?>"<?if($PropertyValue=='Y'):?> checked="checked"<?endif?> />
			<script>
				BX.adminFormTools.modifyCheckbox(document.getElementById('wd_pageprops_checkbox_<?=$UniqID;?>'));
			</script>
		<?
		$strResult = ob_get_clean();
		return $strResult;
	}
}

?>