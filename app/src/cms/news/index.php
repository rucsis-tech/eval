<?php

$Smarty = new SmartyCMSClass();

// --------------------------------------------------
// 新規　
if (isset($_POST['insert'])) {
    $Smarty->auth('CmsAdd','topic');
    $id = Info::insert([
        'type' => Info::TYPE_NEWS,
        'title' => '新規'
    ]);
    $Smarty->location('/cms/topic/edit?id=' . $id);
}
// --------------------------------------------------
// 削除
if (isset($_REQUEST['closed'])) {
    $Smarty->auth('CmsDel','topic');
    $Topic = Info::first($_REQUEST['closed']);
    $Topic->save(['closed' => 1]);
}

// --------------------------------------------------
// 一覧表示
$Smarty->auth('CmsList','topic');
$Topics = Info::where('type', Info::TYPE_NEWS)
    ->orderBy('start_at', 'DESC')
    ->where('closed', 0)
    ->all();
foreach ($Topics as $Topic) {
    $Topic->datalizeTopic();
}

$Smarty->assign('Topics', $Topics);
$Smarty->displayCMS("news/index.tpl");