<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/test.usdev.ru";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$select=array('*');
$where=array("IBLOCK_ID"=>4);

$res=CIBlockElement::GetList(
    array('NAME'=>'ASC'),
    $where,
    false,
    false,
//        array('nPageSize' => 3),
    $select
);
while ($p = $res->fetch()) {
    $temp = new CIBlockElement();
    $new_date = new DateTime($p['DATE_CREATE']);
    $new_date->add(new DateInterval('P1D'));
    $temp->update($p['ID'], array(
        'DATE_ACTIVE_FROM' => $new_date->format('d.m.Y H:i:s')
    ));
}
?>