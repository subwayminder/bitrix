<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новости2");
?>
<?php
use CIBlockElement;

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
while ($p = $res->fetch()){
    echo $p['ID'].' '.$p['NAME'].'</br>'.'Дата создания: '.$p['DATE_CREATE'].'</br>'.'Дата активности с: '.$p['ACTIVE_FROM'].'</br>'.$p['PREVIEW_TEXT'].'</br>'.'</br>';
//    $temp= new CIBlockElement();
//    $new_date = new DateTime($p['DATE_CREATE']);
//    $new_date->add(new DateInterval('P1D'));
//    $temp->update($p['ID'],array(
//            'DATE_ACTIVE_FROM'=>$new_date->format('d.m.Y H:i:s')
//    ));
//    echo '<pre>';
//    print_r($p);
//    echo '</pre>';

}

$res2 = CSaleOrderPropsValue::GetList();

while ($p = $res2->fetch()){
    echo '<pre>';
    print_r($p);
    echo '</pre>';
}

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

