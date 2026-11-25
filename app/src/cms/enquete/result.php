<?php

$Smarty = new SmartyCMSClass();

// ID指定
$id = $_REQUEST['id'] ?? '';

// 問題
$Item = Enquete::getDefaultQuery()->first('id', $id);
if (empty($Item)) $Smarty->location('index.php');

$Item->datalize();

// 回答
$answers = [];
$table = App::logDB('enquete');

foreach ($Item->questions as $q_index => $q) {
    $selected = [];
    $counts = [];
    $Answers = Enquete::from($table)
        ->where('enquete_id', $id)
        ->where('question_id', $q_index + 1)
        ->all();
    $answer_count = count($Answers);
    foreach ($Answers as $Answer) {
        $ans = $Answer->work();
        $count = count($ans['answers']);
        $counts[$count] = ($counts[$count] ?? 0) + 1;
        foreach ($ans['answers'] as $an) {
            $selected[$an] = ($selected[$an] ?? 0) + 1;
        }
    }


    $Item->questions[$q_index]['counts'] = $counts;
    foreach ($selected as $qn => $num) {
        $index = intval(substr($qn, 0)) - 1;
        $Item->questions[$q_index]['selects'][$index]['selected'] = $num;
    }

    arsort($selected);
    $Item->questions[$q_index]['selected'] = array_keys($selected);
    $Item->questions[$q_index]['answer_count'] = count($Answers);


}


$Smarty->assign('EnqueteMaster', $Item);


$Smarty->displayCMS("enquete/result.tpl");

pre($Item);
