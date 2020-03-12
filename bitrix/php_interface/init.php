<?php
use Bitrix\Main;
use Bitrix\Main\Diag\Debug;
use \Bitrix\Sale\Order;
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php");

//Событие при добавлении нового элемента инфоблока

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("ChangeNewsActiveDate", "OnBeforeIBlockElementAddHandler"));
class ChangeNewsActiveDate
{
    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        Debug::dump($arFields,'$arFields','loglog.log');
        $new_date = new DateTime($arFields['DATE_CREATE']);
        $new_date->add(new DateInterval('P10D'));
        $arFields['NAME'] = 'Какая то новость';
        $arFields['ACTIVE_TO'] = (string)$new_date->format('d.m.Y H:i:s');
    }
}

//Событие при добавлении нового юзера
AddEventHandler('main','OnBeforeUserRegister', array('AddIdToNewUser','OnBeforeUserRegisterHandler'));

class AddIdToNewUser{
    function OnBeforeUserRegisterHandler(&$arFields){
        $last_user=CUser::GetList($by="id", $order="asc",false, array('nPageSize' => 1));
        while ($p = $last_user->fetch()){
            $last_id=$p['ID'] + 1;
        }
        $arFields['NAME']=(string)$arFields['NAME'].$last_id;
    }
}

//Событие при создании заказа - отправка письма
AddEventHandler('sale','OnOrderAdd', array('SendOrderMessage','OnOrderAddHandler'));

class SendOrderMessage{
    function  OnOrderAddHandler(&$ID,&$arFields){
        if(!in_array('1', CUser::GetUserGroup($arFields['USER_ID']))){
            $arEventFields = array(
                'ORDER_ID' => $arFields['DELIVERY_ID'],
                'ORDER_ACCOUNT_NUMBER_ENCODE' => '',
                'ORDER_REAL_ID' => $ID,
                'ORDER_DATE' => $arFields['DATE_STATUS_SHORT'],
                'ORDER_USER' => $arFields['USER_NAME'],
                'PRICE' => $arFields['PRICE'],
                'EMAIL' => $arFields['USER_EMAIL'],
                'BCC' => '',
                'ORDER_LIST' => '',
                'ORDER_PUBLIC_URL' => '',
                'SALE_EMAIL' => '',
            );
            CEvent::send('SALE_NEW_ORDER',false,$arEventFields);
        }
    }
}

//Добавить безнал к заказу
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderBeforeSaved',
    'AddNewPayment'
);


function AddNewPayment(\Bitrix\Main\Event $event)
{
    /** @var Payment $payment */
    $order = $event->getParameter("ENTITY");
    $collection = $order->getPaymentCollection();

    $service = \Bitrix\Sale\PaySystem\Manager::getObjectById(4);
    $payment = $collection->createItem($service);

    $payment->setField('SUM', 100);

//    $order->save();
}
?>