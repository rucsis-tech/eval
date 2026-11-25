<?php

$Smarty = new SmartyCMSClass();
// --------------------------------------------------
// 対象アイテム
$id = $_REQUEST['id'] ?? 0;
$Recommend = Info::where('type', Info::TYPE_TOPIC)
    ->where('info.id', $id)
    ->leftJoin('image', 'image.id=image_id')
    ->select(['info.*', 'image.src AS image_path'])
    ->first();
if (empty($Recommend)) $Smarty->location('index');


// --------------------------------------------------
// 更新
if (isset($_POST['id'])) {
    $Smarty->auth('ContentMod','recommend');

    $Recommend->store($_POST);

    if (!empty($_POST['image_tmp'])) {
        $tmp = APP_ROOT . 'htdocs/images/tmp/' . $_POST['image_tmp'];
        $Image = Image::transcript(Image::CATEGORY_RECOMMEND, $id, $tmp , $_POST['image_origin']);
        Info::where("id", $id)
            ->update(['image_id' => $Image->id,]);
    }


    $Smarty->location('/cms/recommend/index');
}

// --------------------------------------------------
// 表示
$Recommend->datalize();
$Smarty->assign('Recommend', $Recommend);
$Smarty->displayCMS("topic/edit.tpl");

