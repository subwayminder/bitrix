<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Все товары в корзине пользователя с ID 1 и увеличение всех цен на 200%");
?>
<?php
Cmodule::IncludeModule("catalog");

$select=array('*');
$where=array();
$temp = new CSaleOrder();

$res = $temp->GetList(
    array('NAME'=>'ASC'),
    $where,
    false,
//    false,
    array('nPageSize' => 3),
    $select
);
while ($p = $res->fetch()){
    echo $p['ID'].' '.$p['PRICE'].'</br>';
//    echo '<pre>';
//    print_r($p);
//    echo '</pre>';

}

//Обновление цен на 10%
$db_res = CPrice::GetList(
    array(),
    array(
        "ELEMENT_IBLOCK_ID" => 3, //ID инфоблока с товарами
        "CURRENCY" => "RUB" // Валюта
    )
);
while($ar_res = $db_res->Fetch())
{
    $UpdatedPrice = $ar_res["PRICE"] * 2; // Здесь мы меняем цену
    CPrice::Update($ar_res["ID"], Array("PRICE" => $UpdatedPrice));
}



?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>