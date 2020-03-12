<?
class CWDU_Headers {
	
	/**
	 *	Remove headers
	 */
	public static function removeHeaders(){
		$arHeaders = COption::GetOptionString(WDU_MODULE, 'server_headers_remove');
		if(strlen($arHeaders)){
			$arHeaders = unserialize($arHeaders);
		}
		if(is_array($arHeaders)){
			foreach($arHeaders as $strHeader){
				if(strlen($strHeader)){
					header_remove($strHeader);
				}
			}
		}
	}
	
	/**
	 *	Add headers
	 */
	public static function addHeaders(){
		$arHeaders = COption::GetOptionString(WDU_MODULE, 'server_headers_add');
		if(strlen($arHeaders)){
			$arHeaders = unserialize($arHeaders);
		}
		if(is_array($arHeaders)){
			foreach($arHeaders as $strHeader){
				if(strlen($strHeader)){
					header($strHeader);
				}
			}
		}
	}

}
?>