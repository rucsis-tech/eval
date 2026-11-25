<?php

$Smarty = new SmartyCMSClass();

// --------------------------------------------------
// 新規　
if (isset($_POST['insert'])) {
    $Smarty->auth('ContentAdd', 'Enquete');
    $id = Enquete::insert(['status'=>Enquete::STATUS_END,'work'=>"[]",]);
    $Smarty->location('/cms/enquete/edit?id=' . $id);
}
// --------------------------------------------------
// 削除
if (isset($_REQUEST['closed'])) {
    $Smarty->auth('ContentDel', 'Enquete');

    $One = Enquete::first($_REQUEST['closed']);
    $One->save(['closed' => 1]);
}
// --------------------------------------------------

$Smarty->auth('ContentList', 'Enquete');

$Query = Enquete::getDefaultQuery();

$EnqueteMasters = $Query->all();
foreach ($EnqueteMasters as $Item) {
    $Item->datalize();
}



$Smarty->assign('STATUSES', Enquete::STATUSES);
$Smarty->assign('EnqueteMasters', $EnqueteMasters);

$Smarty->displayCMS("enquete/index.tpl");
