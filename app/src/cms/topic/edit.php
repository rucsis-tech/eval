<?php

(function () {
    $Smarty = new SmartyCMSClass();

    // --------------------------------------------------
    // 対象アイテム
    $id = $_REQUEST['id'] ?? 0;
    $Topic = Info::where('type', Info::TYPE_TOPIC)
        ->where('info.id', $id)
        ->leftJoin('image', 'image.id=image_id')
        ->select(['info.*', 'image.src AS image_path'])
        ->first();
    if (empty($Topic)) $Smarty->location('index');


    // --------------------------------------------------
    // 更新
    if (isset($_POST['id'])) {
        $Smarty->auth('ContentMod', 'topic');

        $Topic->store($_POST);

        if (!empty($_POST['image_tmp'])) {
            $tmp = APP_ROOT . 'htdocs/images/tmp/' . $_POST['image_tmp'];
            $Image = Image::transcript(Image::CATEGORY_RECOMMEND, $id, $tmp, $_POST['image_origin']);
            Info::where("id", $id)
                ->update(['image_id' => $Image->id,]);
        }


        $Smarty->location('/cms/topic/index');
    }

    // --------------------------------------------------
    // 表示
    $Topic->datalize();
    $Smarty->assign('Topic', $Topic);
    $Smarty->displayCMS("topic/edit.tpl");

})();


