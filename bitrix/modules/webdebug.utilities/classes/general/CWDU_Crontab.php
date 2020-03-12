<?
class CWDU_Crontab {
	
	/**
	 *	Check if crontab can be managed by this script (only if Linux OS)
	 */
	public static function CanAutoSet(){
		$bResult = true;
		if (stripos(PHP_OS,'linux')===false) {
			$bResult = false;
		} else {
			exec('which crontab',$arExec);
			if(!is_array($arExec) || empty($arExec[0])) {
				$bResult = false;
			}
		}
		return $bResult;
	}
	
	/**
	 *	Check if hosting is timeweb
	 */
	public static function IsTimeweb(){
		exec('uname -a',$arExec);
		if(stripos($arExec[0],'timeweb.ru')!==false) {
			return true;
		}
		return false;
	}
	
	/**
	 *	Get path to php binary
	 */
	public static function GetPhpPath(){
		$strPhpPath = false;
		if (stripos(PHP_OS,'linux')!==false) {
			exec('which php',$strResult);
			if(is_array($strResult) && !empty($strResult[0])){
				$strPhpPath = $strResult[0];
			}
			if(empty($strPhpPath)) {
				$strPhpPath = 'php';
			}
		}
		return $strPhpPath;
	}
	
	/*
	public static function GetCommand($ProfileID, $strPhpPath='', $bClear=false){
		$strCommandScript = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.WDI_MODULE.'/cron.php profile='.$ProfileID.' start=Y';
		$strCommandConfig = $strCommandScript;
		$strPhpIni = '';
		if(!$bClear) {
			$strCommandConfig = '-f '.$strCommandConfig;
			$strPhpIni = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.WDI_MODULE.'/php.ini';
			if(is_file($strPhpIni) && filesize($strPhpIni)>0) {
				$strCommandConfig = '-c '.$strPhpIni.' '.$strCommandConfig;
			}
			$strCommandConfig = ' '.$strCommandConfig;
			$strPhpPath = !empty($strPhpPath) ? $strPhpPath : self::GetPhpPath();
		} else {
			$strPhpPath = '';
		}
		$strCommandFull = $strPhpPath.$strCommandConfig;
		return $strCommandFull;
	}
	*/
	
	/**
	 *	Get cron jobs
	 */
	public static function GetList(){
		$arResult = array();
		$Command = 'crontab -l;';
		exec($Command, $arCommandResult);
		foreach($arCommandResult as $Key => $Value){
			if(preg_match('#^([a-z0-9\*/,]+)[\s]+([a-z0-9\*/,]+)[\s]+([a-z0-9\*/,]+)[\s]+([a-z0-9\*/,]+)[\s]+([a-z0-9\*/,]+)[\s]+(.*?)$#',$Value,$M)) {
				$arResult[] = array(
					'ID' => $Value,
					'MINUTE' => $M[1],
					'HOUR' => $M[2],
					'DAY' => $M[3],
					'MONTH' => $M[4],
					'WEEKDAY' => $M[5],
					'PATH' => $M[6],
					'COMMAND' => $Value,
				);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Add cron job
	 */
	public static function Add($Command, $Schedule=''){
		if(empty($Command)) {
			return false;
		}
		$Schedule = !empty($Schedule) ? trim($Schedule).' ' : '* * * * * ';
		if(!self::IsExists($Command, $Schedule)) {
			$CommandEscaped = str_replace('"','\"',$Command);
			exec('(crontab -l 2>/dev/null; echo "'.$Schedule.$CommandEscaped.'") | crontab -', $Result);
		}
		return self::IsExists($Command, $Schedule);
	}

	/**
	 *	Delete cron job
	 */
	public static function Delete($Command, $Schedule=''){
		if(empty($Command)) {
			return false;
		}
		$Schedule = !empty($Schedule) ? trim($Schedule).' ' : '';
		$CommandEscaped = str_replace('"','\"',$Command);
		exec('crontab -l | grep -v -F "'.$Schedule.$CommandEscaped.'" | crontab -', $Result);
		return !self::IsExists($Command, $Schedule);
	}

	/**
	 *	Check cron job exists
	 */
	public static function IsExists($Command, $Schedule=''){
		if(empty($Command)) {
			return false;
		}
		$Schedule = !empty($Schedule) ? trim($Schedule).' ' : '';
		$Command = str_replace('"','\"',$Command);
		$strExec = 'crontab -l | grep -q -F "'.$Schedule.$Command.'" && echo "Y" || echo "N"';
		exec($strExec, $Result);
		return $Result === array('Y');
	}

	/**
	 *	Check cron schedule
	 */
	public static function GetSchedule($Command){
		if(self::IsExists($Command)) {
			$arJobs = self::GetList();
			foreach($arJobs as $strJob){
				if(stripos($strJob,$Command)!==false) {
					$arJob = explode(' ',$strJob);
					$arSchedule = array_slice($arJob,0,5);
					return implode(' ',$arSchedule);
				}
			}
		}
		return false;
	}
	
	/**
	 *	Explode full command 
	 */
	public static function ExplodeFullCommand($FullCommand){
		if(preg_match('#^([a-z0-9\*/,]+[\s]+[a-z0-9\*/,]+[\s]+[a-z0-9\*/,]+[\s]+[a-z0-9\*/,]+[\s]+[a-z0-9\*/,]+)[\s]+(.*?)$#',$FullCommand,$M)) {
			return array(
				'SCHEDULE' => $M[1],
				'COMMAND' => $M[2],
			);
		}
		return false;
	}

}
?>