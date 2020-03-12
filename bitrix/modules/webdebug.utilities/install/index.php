<?
global $MESS;
IncludeModuleLangFile(__FILE__);

class webdebug_utilities extends CModule {
	var $MODULE_ID = 'webdebug.utilities';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $Errors;

	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__).'/version.php');
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->PARTNER_NAME = GetMessage('WDU_PARTNER_NAME');
		$this->PARTNER_URI = GetMessage('WDU_PARTNER_URI');
		$this->MODULE_NAME = GetMessage('WDU_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('WDU_MODULE_DESCR');
	}

	function InstallDB($arParams = array()) {
		global $DBType, $APPLICATION, $DB, $USER;
		$this->Errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/'.ToLower($DBType).'/install.sql');
		if ($this->Errors !== false) {
			$APPLICATION->ThrowException(implode("", $this->Errors));
			return false;
		}
		// Install FastSQL
		$arFastSQL = array(
			"SHOW TABLES;",
			"SELECT * FROM `b_event` ORDER BY `ID` DESC;",
			"SELECT COUNT(`ID`) FROM `b_event` WHERE `SUCCESS_EXEC`='N' ORDER BY `ID` DESC;",
			"SELECT `ID`,`C_FIELDS`,`SUCCESS_EXEC` FROM `b_event` ORDER BY `ID` DESC;",
			"SELECT * FROM `b_option`;",
			"SELECT `ID`,`LOGIN`,`ACTIVE`,`NAME`,`LAST_NAME`,`SECOND_NAME`,`EMAIL` FROM `b_user`;",
			"SELECT * FROM `b_module_to_module`;",
			"SELECT table_name AS table_name, engine, ROUND(data_length/1024/1024,2) AS total_size_mb, table_rows FROM information_schema.tables WHERE table_schema=DATABASE() ORDER BY total_size_mb DESC;"
		);
		$Sort = 0;
		$UserID = $USER->GetID();
		foreach($arFastSQL as $FastSQL) {
			$Sort += 10;
			$FastSQL = $DB->ForSQL($FastSQL);
			$SQL = "INSERT INTO `b_wdu_fastsql` (`ACTIVE`,`SORT`,`QUERY`,`USER_ID`) VALUES('Y','{$Sort}','{$FastSQL}','{$UserID}');";
			$DB->Query($SQL);
		}
		return true;
	}

	function UnInstallDB() {
		global $DB, $DBType, $APPLICATION;
		$this->Errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/db/".ToLower($DBType)."/uninstall.sql");
		if ($this->Errors !== false) {
			$APPLICATION->ThrowException(implode("", $this->Errors));
			return false;
		}
		return true;
	}
	
	function InstallFiles() {
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
		return true;
	}
	
	function UnInstallFiles($SaveTemplate=true) {
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes/.default/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default/');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components');
		return true;
	}

	function DoInstall() {
		if (!check_bitrix_sessid()) return false;
		RegisterModule($this->MODULE_ID);
		$this->InstallDB();
		$this->InstallFiles();
		require_once(__DIR__.'/hotkeys_install.php');
		RegisterModuleDependences('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CWD_Util_Handler', 'OnAdminTabControlBegin');
		RegisterModuleDependences('main', 'OnEpilog', $this->MODULE_ID, 'CWD_Util_Handler', 'OnEpilog');
		RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'CWD_Util_Handler', 'OnEndBufferContent');
		RegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'CWD_Util_Handler', 'OnPageStart');
		RegisterModuleDependences('main', 'OnAdminContextMenuShow', $this->MODULE_ID, 'CWD_Util_Handler', 'OnAdminContextMenuShow');
		RegisterModuleDependences('main', 'OnProlog', $this->MODULE_ID, 'CWD_Util_Handler', 'OnProlog');
		RegisterModuleDependences('main', 'OnAfterEpilog', $this->MODULE_ID, 'CWD_Util_Handler', 'OnAfterEpilog');
		return true;
	}

	function DoUninstall() {
		global $DB;
		if (!check_bitrix_sessid()) return false;
		COption::RemoveOption($this->MODULE_ID);
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CWD_Util_Handler', 'OnAdminTabControlBegin');
		UnRegisterModuleDependences('main', 'OnEpilog', $this->MODULE_ID, 'CWD_Util_Handler', 'OnEpilog');
		UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'CWD_Util_Handler', 'OnEndBufferContent');
		UnRegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'CWD_Util_Handler', 'OnPageStart');
		UnRegisterModuleDependences('main', 'OnAdminContextMenuShow', $this->MODULE_ID, 'CWD_Util_Handler', 'OnAdminContextMenuShow');
		UnRegisterModuleDependences('main', 'OnAfterEpilog', $this->MODULE_ID, 'CWD_Util_Handler', 'OnAfterEpilog');
		UnRegisterModuleDependences('main', 'OnProlog', $this->MODULE_ID, 'CWD_Util_Handler', 'OnProlog');
		require_once(__DIR__.'/hotkeys_uninstall.php');
		$this->UnInstallFiles();
		$this->UnInstallDB();
		UnRegisterModule($this->MODULE_ID);
		return true;
	}
	
}
?>