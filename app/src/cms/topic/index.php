<?php

$Smarty = new SmartyCMSClass();

// --------------------------------------------------
// 新規　
if (isset($_POST['insert'])) {
    $Smarty->auth('ContentAdd', 'recommend');
    $id = Info::insert([
        'type' => Info::TYPE_TOPIC,
        'title' => '新規'
    ]);
    $Smarty->location('/cms/recommend/edit?id=' . $id);
}
// --------------------------------------------------
// 削除
if (isset($_REQUEST['closed'])) {
    $Smarty->auth('ContentDel', 'recommend');
    $Item = Info::first($_REQUEST['closed']);
    $Item->save(['closed' => 1]);
}

// --------------------------------------------------
// 一覧表示
$Smarty->auth('ContentList', 'recommend');
$List = Info::where('type', Info::TYPE_TOPIC)
    ->orderBy('start_at', 'DESC')
    ->where('closed', 0)
    ->leftJoin('image', 'image.id=image_id')
    ->select(['info.*', 'image.src AS image_path'])
    ->all();
foreach ($List as $Item) {
    Info::cast($Item)->datalize();
}

$Smarty->assign('List', $List);
$Smarty->displayCMS("topic/index.tpl");