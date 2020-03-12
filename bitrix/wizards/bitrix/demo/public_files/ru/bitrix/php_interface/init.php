<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php");

AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("ChangeNewsActiveDate", "OnAfterIBlockElementAddHandler"));

class ChangeNewsActiveDate
{
    // создаем обработчик события "OnAfterIBlockElementAdd"
    function OnAfterIBlockElementAddHandler(&$arFields)
    {
        if($arFields["ID"]>0){
            AddMessage2Log("Запись с кодом ".$arFields["ID"]." добавлена.");
            $temp = new CIBlockElement();
            $new_date = new DateTime($arFields['DATE_CREATE']);
            $new_date->add(new DateInterval('P10D'));
            $arFields['DATE_ACTIVE_TO'] = $new_date->format('d.m.Y H:i:s');
            $temp->update($arFields['ID'], $arFields);
        }
        else
             AddMessage2Log("Ошибка добавления записи (".$arFields["RESULT_MESSAGE"].").");
    }
}

AddEventHandler('main','OnAfterUserAdd', array('AddIdToNewUser','OnAfterUserAddHandler'));

class AddIdToNewUser{
    function OnAfterUserAddHandler(&$arFields){
        if($arFields["ID"]>0){
            $arFields['NAME']=$arFields['NAME'].$arFields['ID'];
            $user = new CUser();
            $user->update($arFields['ID'],$arFields);
        }
        else
            AddMessage2Log("Ошибка добавления записи (".$arFields["RESULT_MESSAGE"].").");
    }
}
?>