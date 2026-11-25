// 出馬表情報を取得したい ----------------------------------
// ダミーとしてひとまず取得できたとする --------------------
// ---------------------------------------------------------

const horse_data = [
    {horse_num: 1, waku_num: 1, waku_tousuu: 2, horse_name: "サンライズアース",},
    {horse_num: 2, waku_num: 1, waku_tousuu: 2, horse_name: "レガレイラ",},
    {horse_num: 3, waku_num: 2, waku_tousuu: 2, horse_name: "ジューンテイク",},
    {horse_num: 4, waku_num: 2, waku_tousuu: 2, horse_name: "ビザンチンドリーム",},
    {horse_num: 5, waku_num: 3, waku_tousuu: 2, horse_name: "ダノンデサイル",},
    {horse_num: 6, waku_num: 3, waku_tousuu: 2, horse_name: "コスモキュランダ",},
    {horse_num: 7, waku_num: 4, waku_tousuu: 2, horse_name: "ミスタージーティー",},
    {horse_num: 8, waku_num: 4, waku_tousuu: 2, horse_name: "アーバンシック",},
    {horse_num: 9, waku_num: 5, waku_tousuu: 2, horse_name: "ダノンエアズロック",},
    {horse_num: 10, waku_num: 5, waku_tousuu: 2, horse_name: "サンライズジパング",},
    {horse_num: 11, waku_num: 6, waku_tousuu: 2, horse_name: "シュガークン",},
    {horse_num: 12, waku_num: 6, waku_tousuu: 2, horse_name: "シックスペンス",},
    {horse_num: 13, waku_num: 7, waku_tousuu: 3, horse_name: "シンエンペラー",},
    {horse_num: 14, waku_num: 7, waku_tousuu: 3, horse_name: "ゴンバデカーブース",},
    {horse_num: 15, waku_num: 7, waku_tousuu: 3, horse_name: "ジャスティンミラノ",},
    {horse_num: 16, waku_num: 8, waku_tousuu: 3, horse_name: "メイショウタバル",},
    {horse_num: 17, waku_num: 8, waku_tousuu: 3, horse_name: "ショウナンラプンタ",},
    {horse_num: 18, waku_num: 8, waku_tousuu: 3, horse_name: "エコロヴァルツ",},
];


// 買い目選択＆展開 ---------------------------------------------------------------------------


// 券種とバリエーションの定義 ------------------------------
// min_selectは列ごとのチェックボックスに対する ------------
// 最低必須選択数／ラジオボタンは常に1頭のみを必須で選択 ---
// multipleはその列が0なら表示なし、1ならラジオタイプ、 ----
// 2なら複数選択可能なチェックボックスタイプ）--------------
// ---------------------------------------------------------

const TicketPattern = [

    {pattern_id: 0, ticket_id: 0, ticket_name: "なし", v_id: 0, v_name: "なし", min_select: 0, multiple01: 0, m01_name: "なし", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},

    {pattern_id: 1, ticket_id: 1, ticket_name: "単勝", v_id: 0, v_name: "なし", min_select: 1, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},

    {pattern_id: 2, ticket_id: 2, ticket_name: "複勝", v_id: 0, v_name: "なし", min_select: 1, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},

    {pattern_id: 3, ticket_id: 3, ticket_name: "枠連", v_id: 0, v_name: "なし", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 4, ticket_id: 3, ticket_name: "枠連", v_id: 1, v_name: "流し", min_select: 2, multiple01: 1, m01_name: "軸", multiple02: 2, m02_name: "相手", multiple03: 0, m03_name: "なし",},
    {pattern_id: 5, ticket_id: 3, ticket_name: "枠連", v_id: 2, v_name: "ボックス", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 2, m02_name: "同枠", multiple03: 0, m03_name: "なし",},

    {pattern_id: 6, ticket_id: 4, ticket_name: "馬連", v_id: 0, v_name: "なし", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 7, ticket_id: 4, ticket_name: "馬連", v_id: 1, v_name: "流し", min_select: 2, multiple01: 1, m01_name: "軸馬", multiple02: 2, m02_name: "相手", multiple03: 0, m03_name: "なし",},
    {pattern_id: 8, ticket_id: 4, ticket_name: "馬連", v_id: 2, v_name: "ボックス", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 9, ticket_id: 4, ticket_name: "馬連", v_id: 3, v_name: "フォーメーション", min_select: 2, multiple01: 2, m01_name: "1頭目", multiple02: 2, m02_name: "2頭目", multiple03: 0, m03_name: "なし",},

    {pattern_id: 10, ticket_id: 5, ticket_name: "馬単", v_id: 0, v_name: "なし", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 11, ticket_id: 5, ticket_name: "馬単", v_id: 4, v_name: "軸1着", min_select: 2, multiple01: 1, m01_name: "軸馬1着", multiple02: 2, m02_name: "相手", multiple03: 0, m03_name: "なし",},
    {pattern_id: 12, ticket_id: 5, ticket_name: "馬単", v_id: 5, v_name: "軸2着", min_select: 2, multiple01: 2, m01_name: "相手", multiple02: 1, m02_name: "軸馬2着", multiple03: 0, m03_name: "なし",},
    {pattern_id: 13, ticket_id: 5, ticket_name: "馬単", v_id: 2, v_name: "ボックス", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 14, ticket_id: 5, ticket_name: "馬単", v_id: 3, v_name: "フォーメーション", min_select: 2, multiple01: 2, m01_name: "1着", multiple02: 2, m02_name: "2着", multiple03: 0, m03_name: "なし",},

    {pattern_id: 15, ticket_id: 6, ticket_name: "ワイド", v_id: 0, v_name: "なし", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 16, ticket_id: 6, ticket_name: "ワイド", v_id: 1, v_name: "流し", min_select: 2, multiple01: 1, m01_name: "軸馬", multiple02: 2, m02_name: "相手", multiple03: 0, m03_name: "なし",},
    {pattern_id: 17, ticket_id: 6, ticket_name: "ワイド", v_id: 2, v_name: "ボックス", min_select: 2, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 18, ticket_id: 6, ticket_name: "ワイド", v_id: 3, v_name: "フォーメーション", min_select: 2, multiple01: 2, m01_name: "1頭目", multiple02: 2, m02_name: "2頭目", multiple03: 0, m03_name: "なし",},

    {pattern_id: 19, ticket_id: 7, ticket_name: "3連複", v_id: 0, v_name: "なし", min_select: 3, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 20, ticket_id: 7, ticket_name: "3連複", v_id: 6, v_name: "軸1頭", min_select: 3, multiple01: 1, m01_name: "軸馬", multiple02: 2, m02_name: "相手", multiple03: 0, m03_name: "なし",},
    {pattern_id: 21, ticket_id: 7, ticket_name: "3連複", v_id: 7, v_name: "軸2頭", min_select: 3, multiple01: 1, m01_name: "軸馬1頭目", multiple02: 1, m02_name: "軸馬2頭目", multiple03: 2, m03_name: "相手",},
    {pattern_id: 22, ticket_id: 7, ticket_name: "3連複", v_id: 2, v_name: "ボックス", min_select: 3, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 23, ticket_id: 7, ticket_name: "3連複", v_id: 3, v_name: "フォーメーション", min_select: 3, multiple01: 2, m01_name: "1頭目", multiple02: 2, m02_name: "2頭目", multiple03: 2, m03_name: "3頭目",},

    {pattern_id: 24, ticket_id: 8, ticket_name: "3連単", v_id: 0, v_name: "なし", min_select: 3, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 25, ticket_id: 8, ticket_name: "3連単", v_id: 8, v_name: "軸1着", min_select: 3, multiple01: 1, m01_name: "軸馬1着", multiple02: 2, m02_name: "相手", multiple03: 0, m03_name: "なし",},
    {pattern_id: 26, ticket_id: 8, ticket_name: "3連単", v_id: 9, v_name: "軸2着", min_select: 3, multiple01: 2, m01_name: "相手", multiple02: 1, m02_name: "軸馬2着", multiple03: 0, m03_name: "なし",},
    {pattern_id: 27, ticket_id: 8, ticket_name: "3連単", v_id: 10, v_name: "軸3着", min_select: 3, multiple01: 2, m01_name: "相手", multiple02: 1, m02_name: "軸馬3着", multiple03: 0, m03_name: "なし",},
    {pattern_id: 28, ticket_id: 8, ticket_name: "3連単", v_id: 11, v_name: "1・2着流し", min_select: 3, multiple01: 1, m01_name: "軸馬1着", multiple02: 1, m02_name: "軸馬2着", multiple03: 2, m03_name: "相手",},
    {pattern_id: 29, ticket_id: 8, ticket_name: "3連単", v_id: 12, v_name: "1・3着流し", min_select: 3, multiple01: 1, m01_name: "軸馬1着", multiple02: 2, m02_name: "相手", multiple03: 1, m03_name: "軸馬3着",},
    {pattern_id: 30, ticket_id: 8, ticket_name: "3連単", v_id: 13, v_name: "2・3着流し", min_select: 3, multiple01: 2, m01_name: "相手", multiple02: 1, m02_name: "軸馬2着", multiple03: 1, m03_name: "軸馬3着",},
    {pattern_id: 31, ticket_id: 8, ticket_name: "3連単", v_id: 2, v_name: "ボックス", min_select: 3, multiple01: 2, m01_name: "選択", multiple02: 0, m02_name: "なし", multiple03: 0, m03_name: "なし",},
    {pattern_id: 32, ticket_id: 8, ticket_name: "3連単", v_id: 3, v_name: "フォーメーション", min_select: 3, multiple01: 2, m01_name: "1着", multiple02: 2, m02_name: "2着", multiple03: 2, m03_name: "3着",},

];

var kaime_count = 0;
var tenkai_html = "";


// 券種と組み合わせの選択によるイベント --------------------
// ---------------------------------------------------------

window.onload = function () {

    const kensyu = document.querySelector('[name="ticket_id"]');
    const v_id = document.querySelector('[name="v_id"]');
    const pickup = document.getElementById('kaime_pickup');

    //券種の内容が変更された時
    kensyu.onchange = event => {

        //option要素を新しく作る
        const option = document.createElement('option');

        //option要素にvalueと表示名を設定
        while (0 < v_id.childNodes.length) {
            v_id.removeChild(v_id.childNodes[0]);
        }
        var option_text = "";
        for (i = 0; i < TicketPattern.length; i++) {
            if (TicketPattern[i].ticket_id == kensyu.value) {
                option_text += "<option value=" + TicketPattern[i].v_id + ">" + TicketPattern[i].v_name + "</option>";
            }
        }
        v_id.innerHTML = option_text;

        ListMake(kensyu.value, v_id.value);
    }

    //組み合わせの内容が変更された時
    v_id.onchange = event => {
        ListMake(kensyu.value, v_id.value);
    }

    //展開確認ボタンが押された時
    pickup.onclick = event => {
        kaime_pickup(kensyu.value, v_id.value)
    }

}


// 選択された券種と組み合わせから選択出馬リストを作成
// ---------------------------------------------------------

function ListMake(ticket_id, v_id) {
    var p_id = 0;

    for (i = 0; i < TicketPattern.length; i++) {
        if (TicketPattern[i].ticket_id == ticket_id && TicketPattern[i].v_id == v_id) {
            p_id = TicketPattern[i].pattern_id;
        }
    }

    var list_text = "";

    if (p_id != 0) {
        list_text += '<tr>';
        list_text += '  <th class="th umaban Fs26">馬番</th>';
        list_text += '  <th class="th mymark Fs26">印</th>';
        list_text += '  <th class="th bamei Fs26">馬名</th>';

        if (TicketPattern[p_id].multiple01 != 0) {
            list_text += '  <th class="th btn_area Fs26" id="m01_name">' + TicketPattern[p_id].m01_name + '</th>';
        }
        if (TicketPattern[p_id].multiple02 != 0) {
            list_text += '  <th class="th btn_area Fs26" id="m02_name">' + TicketPattern[p_id].m02_name + '</th>';
        }
        if (TicketPattern[p_id].multiple03 != 0) {
            list_text += '  <th class="th btn_area Fs26" id="m03_name">' + TicketPattern[p_id].m03_name + '</th>';
        }
        list_text += '</tr>';

        var before_num = 0;

        for (i = 0; i < horse_data.length; i++) {
            list_text += '<tr>';
            list_text += '  <td class="td umaban">' + horse_data[i].horse_num + '</td>'; //動的に出馬表から埋め込み
            list_text += '  <td class="td mymark">' + +'</td>'; //動的に印設定から埋め込み
            list_text += '  <td class="td">';
            list_text += '    <div class="bamei_wrap">';
            list_text += '      <span class="bamei Fs26">' + horse_data[i].horse_name + '</span>'; //動的に出馬表から埋め込み
            list_text += '    </div>';
            list_text += '  </td>';


            if (TicketPattern[p_id].ticket_id == 3) { //枠連かどうか

                if (horse_data[i].horse_num == 1 || horse_data[i].waku_num != before_num) { //同一枠判定
                    list_text += '  <td class="td btn_area" rowspan="' + horse_data[i].waku_tousuu + '">';
                    if (TicketPattern[p_id].multiple01 == 1) {
                        list_text += '    <input type="radio" name="check_waku_1st" class="radio_common" value=' + horse_data[i].horse_num + ' id="check_1st_' + horse_data[i].horse_num + '">';
                    } else {
                        list_text += '    <input type="checkbox" name="check_waku_1st" class="checkbox_common" value=' + horse_data[i].horse_num + ' id="check_1st_' + horse_data[i].horse_num + '">';
                    }
                    list_text += '  </td>';

                    if (TicketPattern[p_id].multiple02 != 0) {
                        list_text += '  <td class="td btn_area" rowspan="' + horse_data[i].waku_tousuu + '">';
                        if (TicketPattern[p_id].multiple02 == 1) {
                            list_text += '    <input type="radio" name="check_waku_2nd" class="radio_common" value=' + horse_data[i].horse_num + ' id="check_2nd_' + horse_data[i].horse_num + '">';
                        } else {
                            list_text += '    <input type="checkbox" name="check_waku_2nd" class="checkbox_common" value=' + horse_data[i].horse_num + ' id="check_2nd_' + horse_data[i].horse_num + '">';
                        }
                        list_text += '  </td>';
                    }

                } else {
                }
                before_num = horse_data[i].waku_num;


            } else {
                //枠連以外

                if (TicketPattern[p_id].multiple01 != 0) {
                    list_text += '  <td class="td btn_area">';
                    if (TicketPattern[p_id].multiple01 == 1) {
                        list_text += '    <input type="radio" name="check_1st" class="radio_common" value=' + horse_data[i].horse_num + ' id="check_1st_' + horse_data[i].horse_num + '">';
                    } else {
                        list_text += '    <input type="checkbox" name="check_1st" class="checkbox_common" value=' + horse_data[i].horse_num + ' id="check_1st_' + horse_data[i].horse_num + '">';
                    }
                    list_text += '  </td>';
                }

                if (TicketPattern[p_id].multiple02 != 0) {
                    list_text += '  <td class="td btn_area">';
                    if (TicketPattern[p_id].multiple02 == 1) {
                        list_text += '    <input type="radio" name="check_2nd" class="radio_common" value=' + horse_data[i].horse_num + ' id="check_2nd_' + horse_data[i].horse_num + '">';
                    } else {
                        list_text += '    <input type="checkbox" name="check_2nd" class="checkbox_common" value=' + horse_data[i].horse_num + ' id="check_2nd_' + horse_data[i].horse_num + '">';
                    }
                    list_text += '  </td>';
                }

                if (TicketPattern[p_id].multiple03 != 0) {
                    list_text += '  <td class="td btn_area">';
                    if (TicketPattern[p_id].multiple03 == 1) {
                        list_text += '    <input type="radio" name="check_3rd" class="radio_common" value=' + horse_data[i].horse_num + ' id="check_3rd_' + horse_data[i].horse_num + '">';
                    } else {
                        list_text += '    <input type="checkbox" name="check_3rd" class="checkbox_common" value=' + horse_data[i].horse_num + ' id="check_3rd_' + horse_data[i].horse_num + '">';
                    }
                    list_text += '  </td>';
                }


            }

            list_text += '</tr>';

        }

    }

    var select_list_make = document.getElementById("select_table_kaime");
    select_list_make.innerHTML = list_text;

}


// リストのチェック状態から設定項目を抽出 ------------------
// ---------------------------------------------------------

function kaime_pickup(ticket_id, v_id) {
    var p_id = 0;

    for (i = 0; i < TicketPattern.length; i++) {
        if (TicketPattern[i].ticket_id == ticket_id && TicketPattern[i].v_id == v_id) {
            p_id = TicketPattern[i].pattern_id;
        }
    }

    //選択した内容を配列に追加
    var kaime01 = [];
    if (ticket_id == 3) {
        var input_nums = document.querySelectorAll('[name="check_waku_1st"]');
    } else {
        var input_nums = document.querySelectorAll('[name="check_1st"]');
    }
    for (i = 0; i < input_nums.length; i++) {
        if (input_nums[i].checked === true) {
            kaime01.push(i + 1);
        }
    }

    var kaime02 = [];
    if (ticket_id == 3) {
        var input_nums = document.querySelectorAll('[name="check_waku_2nd"]');
    } else {
        var input_nums = document.querySelectorAll('[name="check_2nd"]');
    }
    for (i = 0; i < input_nums.length; i++) {
        if (input_nums[i].checked === true) {
            kaime02.push(i + 1);
        }
    }

    var kaime03 = [];
    if (ticket_id == 3) {
        var input_nums = document.querySelectorAll('[name="check_waku_3rd"]');
    } else {
        var input_nums = document.querySelectorAll('[name="check_3rd"]');
    }
    for (i = 0; i < input_nums.length; i++) {
        if (input_nums[i].checked === true) {
            kaime03.push(i + 1);
        }
    }

    //選択数が基準にそっているか？
    select_check = 0;
    if ((kaime01.length + kaime02.length + kaime03.length) < TicketPattern[p_id].min_select) {
        select_check = 1;
    }

    if (p_id == 7 && v_id == 3) {
        if (kaime01.length == 0 || kaime02.length == 0 || kaime03.length == 0) {
            select_check = 2;
        }
    }

    if (p_id == 8 && v_id != 2) {
        if (v_id != 0) {
            if (v_id == 8 || v_id == 9 || v_id == 10) {
                if (kaime01.length == 0 || kaime02.length == 0) {
                    select_check = 2;
                }
            } else {
                if (kaime01.length == 0 || kaime02.length == 0 || kaime03.length == 0) {
                    select_check = 2;
                }
            }
        }
    }


    if (select_check == 0) {

        //ピックアップエリアのタイトルをページにセット
        const pickup_title_1st = document.getElementById("pickup_title_1st");
        const pickup_title_2nd = document.getElementById("pickup_title_2nd");
        const pickup_title_3rd = document.getElementById("pickup_title_3rd");
        var set_title01 = "";
        if (TicketPattern[p_id].m01_name != "選択") {
            set_title01 += TicketPattern[p_id].m01_name;
        }
        if (TicketPattern[p_id].ticket_id == 3) {
            set_title01 += "枠番";
        } else {
            if (TicketPattern[p_id].m01_name == "選択") {
                set_title01 += "馬番";
            }
        }
        pickup_title_1st.innerHTML = set_title01;

        if (TicketPattern[p_id].multiple02 != 0) {
            const pickup_title_2nd = document.getElementById("pickup_title_2nd");
            var set_title02 = "";
            if (TicketPattern[p_id].m02_name != "選択") {
                set_title02 += TicketPattern[p_id].m02_name;
            }
            if (TicketPattern[p_id].ticket_id == 3) {
                set_title02 += "枠番";
            } else {
                if (TicketPattern[p_id].m02_name == "選択") {
                    set_title02 += "馬番";
                }
            }
            pickup_title_2nd.innerHTML = set_title02;
        }


        if (TicketPattern[p_id].multiple03 != 0) {
            var set_title03 = "";
            if (TicketPattern[p_id].m03_name != "選択") {
                set_title03 += TicketPattern[p_id].m03_name;
            }
            if (TicketPattern[p_id].ticket_id == 3) {
                set_title03 += "枠番";
            } else {
                if (TicketPattern[p_id].m03_name == "選択") {
                    set_title03 += "馬番";
                }
            }
            pickup_title_3rd.innerHTML = set_title03;
        }


        const kaime_1st = document.getElementById("kaime_1st");
        //セットした配列をページにセット
        if (kaime01.length != 0) {
            var set_number01 = "";
            var set_number01_text = "";
            for (i = 0; i < kaime01.length; i++) {
                set_number01 += '<span>' + kaime01[i] + '</span>';
                set_number01_text += kaime01[i];
                if (i != kaime01.length - 1) {
                    set_number01 += ',';
                    set_number01_text += ',';
                }
            }

            kaime_1st.innerHTML = set_number01;

        } else {

            kaime_1st.innerHTML = "";
        }

        const kaime_2nd = document.getElementById("kaime_2nd");
        //セットした配列をページにセット
        if (kaime02.length != 0) {
            var set_number02 = "";
            var set_number02_text = "";
            for (i = 0; i < kaime02.length; i++) {
                set_number02 += '<span>' + kaime02[i] + '</span>';
                set_number02_text += kaime02[i];
                if (i != kaime02.length - 1) {
                    set_number02 += ',';
                    set_number02_text += ',';
                }
            }

            kaime_2nd.innerHTML = set_number02;

        } else {

            kaime_2nd.innerHTML = "";
        }

        const kaime_3rd = document.getElementById("kaime_3rd");
        //セットした配列をページにセット
        if (kaime03.length != 0) {
            var set_number03 = "";
            var set_number03_text = "";
            for (i = 0; i < kaime03.length; i++) {
                set_number03 += '<span>' + kaime03[i] + '</span>';
                set_number03_text += kaime03[i];
                if (i != kaime03.length - 1) {
                    set_number03 += ',';
                    set_number03_text += ',';
                }
            }

            kaime_3rd.innerHTML = set_number03;

        } else {

            kaime_3rd.innerHTML = "";
        }


    } else {

        if (ticket_id == 3) {
            alert(TicketPattern[p_id].min_select + "枠以上選択してください");
        } else {
            if (select_check == 2) {
                alert("各列で1頭以上選択してください");
            } else {
                alert(TicketPattern[p_id].min_select + "頭以上選択してください");
            }
        }
    }


    //ピックアップ内容に合わせて保存用買い目データを作成を指示してページに表示
    const tenkai_table = document.getElementById("tenkai_table");
    const kaime_tensuu = document.getElementById("kaime_tensuu");
    const kaime_text = document.getElementById("kaime_text");

    switch (Number(ticket_id)) {

        case 1:
        case 2:
            tenkai(Number(ticket_id), kaime01);
            tenkai_table.innerHTML = tenkai_html;
            kaime_tensuu.innerHTML = kaime_count;
            kaime_text.value = set_number01_text + '（点数：' + kaime_count + '点）';
            break;

        case 3:
        case 4:
        case 5:
        case 6:
            tenkai(Number(ticket_id), kaime01, kaime02, Number(v_id));
            tenkai_table.innerHTML = tenkai_html;
            kaime_tensuu.innerHTML = kaime_count;
            if (TicketPattern[p_id].multiple02 == 0) {
                kaime_text.value = set_number01_text + '（点数：' + kaime_count + '点）';
            } else {
                kaime_text.value = set_number01_text + '⇒' + set_number02_text + '（点数：' + kaime_count + '点）';
            }
            break;

        case 7:
        case 8:
            tenkai(Number(ticket_id), kaime01, kaime02, Number(v_id), kaime03);
            tenkai_table.innerHTML = tenkai_html;
            kaime_tensuu.innerHTML = kaime_count;
            if (TicketPattern[p_id].multiple02 == 0) {
                kaime_text.value = set_number01_text + '（点数：' + kaime_count + '点）';
            } else {
                if (TicketPattern[p_id].multiple03 == 0) {
                    kaime_text.value = set_number01_text + '⇒' + set_number02_text + '（点数：' + kaime_count + '点）';
                } else {
                    kaime_text.value = set_number01_text + '⇒' + set_number02_text + '⇒' + set_number03_text + '（点数：' + kaime_count + '点）';
                }
            }
            break;
    }

}


// 保存用買い目データ作成 ----------------------------------
// ---------------------------------------------------------

function tenkai(type, arraydata, arraydata02, v_id, arraydata03) {
    console.log(type, arraydata, arraydata02, v_id, arraydata03);
    tenkai_html = '<tr><th class="th Fs26 FwB">内部セット用組合せ</th></tr>';

    switch (type) {

        case 1: //単勝
        case 2: //複勝

            // HTML作成
            kaime_count = 0;
            for (i = 0; i < arraydata.length; i++) {
                tenkai_html += '<tr>';
                tenkai_html += '<td class="td Fs26 FwB">' + arraydata[i] + '</td>';
                tenkai_html += '</tr>';
                kaime_count += 1;
            }
            break;


        case 3: //枠連
        case 4: //馬連
        case 5: //馬単
        case 6: //ワイド

            // 買い目の組み合わせを作成
            kaime_array = [];
            if (v_id != 0) { //組み合わせなし以外であれば

                if (v_id != 2) { //ボックスじゃなければ

                    const kaime_set = new Set; //オブジェクトにして重複をカット

                    for (i = 0; i < arraydata.length; i++) {
                        for (j = 0; j < arraydata02.length; j++) {

                            if (arraydata[i] != arraydata02[j]) { //同じ馬番の同士の組み合わせは除外

                                if (type == 5) { //馬単の場合は順番重視
                                    kaime_set.add("" + arraydata[i] + "→" + arraydata02[j] + "");
                                } else {
                                    if (Number(arraydata[i]) < Number(arraydata02[j])) {
                                        kaime_set.add("" + arraydata[i] + "-" + arraydata02[j] + "");
                                    } else {
                                        kaime_set.add("" + arraydata02[j] + "-" + arraydata[i] + "");
                                    }
                                }
                            }
                        }
                    }
                    kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                } else {

                    if (type == 3) { //枠連ボックス
                        //kaime_merge = $.merge(arraydata,arraydata02);
                        kaime_merge = arraydata.concat(arraydata02);

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < kaime_merge.length; i++) {
                            for (j = 0; j < kaime_merge.length; j++) {

                                if (arraydata02.length > 0) { //(同枠あり)

                                    for (k = 0; k < arraydata02.length; k++) {

                                        if (arraydata02[k] == kaime_merge[i]) {

                                            //組み合わせセット
                                            if (Number(kaime_merge[i]) < Number(kaime_merge[j])) {
                                                kaime_set.add("" + kaime_merge[i] + "-" + kaime_merge[j] + "");
                                            } else {
                                                kaime_set.add("" + kaime_merge[j] + "-" + kaime_merge[i] + "");
                                            }

                                        }

                                    }

                                } else {

                                    //組み合わせセット(同枠なし)
                                    if (Number(kaime_merge[i]) != Number(kaime_merge[j])) {
                                        if (Number(kaime_merge[i]) < Number(kaime_merge[j])) {
                                            kaime_set.add("" + kaime_merge[i] + "-" + kaime_merge[j] + "");
                                        } else {
                                            kaime_set.add("" + kaime_merge[j] + "-" + kaime_merge[i] + "");
                                        }
                                    }
                                }

                            }
                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    } else {
                        // 枠連以外のボックス
                        //kaime_merge = $.merge(arraydata,arraydata02);
                        kaime_merge = arraydata.concat(arraydata02);

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < kaime_merge.length; i++) {
                            for (j = 0; j < kaime_merge.length; j++) {

                                //組み合わせセット
                                //同じ馬番の同士の組み合わせは除外
                                if (kaime_merge[i] != kaime_merge[j]) {

                                    if (type == 5) { //馬単の場合は順番重視
                                        kaime_set.add(kaime_merge[i] + "→" + kaime_merge[j]);
                                    } else {

                                        if (Number(kaime_merge[i]) < Number(kaime_merge[j])) {
                                            kaime_set.add(kaime_merge[i] + "-" + kaime_merge[j]);
                                        } else {
                                            kaime_set.add(kaime_merge[j] + "-" + kaime_merge[i]);
                                        }
                                    }

                                }

                            }
                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }

                }

            } else {
                //組み合わせなしであれば
                const kaime_set = new Set; //オブジェクトにして重複をカット
                for (i = 0; i < arraydata.length; i++) {
                    for (j = 0; j < arraydata.length; j++) {

                        if (arraydata[i] != arraydata[j]) { //同じ馬番の同士の組み合わせは除外

                            if (type == 5) { //馬単の場合は順番重視
                                kaime_set.add("" + arraydata[i] + "→" + arraydata02[j] + "");
                            } else {
                                if (Number(arraydata[i]) < Number(arraydata[j])) {
                                    kaime_set.add("" + arraydata[i] + "-" + arraydata[j] + "");
                                } else {
                                    kaime_set.add("" + arraydata[j] + "-" + arraydata[i] + "");
                                }
                            }
                        }
                    }
                }
                kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

            }

            // HTML作成
            kaime_count = 0;
            for (i = 0; i < kaime_array.length; i++) {
                tenkai_html += '<tr>';
                tenkai_html += '<td class="td Fs26 FwB">' + kaime_array[i] + '</td>';
                tenkai_html += '</tr>';
                kaime_count += 1;
            }
            break;


        case 7: //三連複
            // 買い目の組み合わせを作成
            kaime_array = [];

            if (v_id != 0) { //組み合わせなし以外であれば

                if (v_id != 2) { //ボックスじゃなければ

                    if (v_id == 6) { //軸馬1頭流しの場合

                        //kaime_merge = $.merge(arraydata02,arraydata03);
                        kaime_merge = arraydata02.concat(arraydata03);

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata.length; i++) {

                            for (j = 0; j < kaime_merge.length; j++) {

                                for (k = 0; k < kaime_merge.length - 1; k++) {

                                    if (arraydata[i] != kaime_merge[j] && arraydata[i] != kaime_merge[k] && kaime_merge[j] != kaime_merge[k]) {

                                        if (Number(arraydata[i]) < Number(kaime_merge[j])) {

                                            if (Number(kaime_merge[j]) < Number(kaime_merge[k])) {
                                                kaime_set.add(arraydata[i] + "-" + kaime_merge[j] + "-" + kaime_merge[k]);
                                            } else {
                                                if (Number(arraydata[i]) < Number(kaime_merge[k])) {
                                                    kaime_set.add(arraydata[i] + "-" + kaime_merge[k] + "-" + kaime_merge[j]);
                                                } else {
                                                    kaime_set.add(kaime_merge[k] + "-" + arraydata[i] + "-" + kaime_merge[j]);
                                                }
                                            }

                                        } else {

                                            if (Number(arraydata[i]) < Number(kaime_merge[k])) {
                                                kaime_set.add(kaime_merge[j] + "-" + arraydata[i] + "-" + kaime_merge[k]);
                                            } else {
                                                if (Number(kaime_merge[j]) < Number(kaime_merge[k])) {
                                                    kaime_set.add(kaime_merge[j] + "-" + kaime_merge[k] + "-" + arraydata[i]);
                                                } else {
                                                    kaime_set.add(kaime_merge[k] + "-" + kaime_merge[j] + "-" + arraydata[i]);
                                                }
                                            }

                                        }

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }

                    if (v_id == 7) { //軸馬2頭流しの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata.length; i++) {

                            for (j = 0; j < arraydata02.length; j++) {

                                for (k = 0; k < arraydata03.length; k++) {

                                    if (arraydata[i] != arraydata02[j] && arraydata[i] != arraydata03[k] && arraydata02[j] != arraydata03[k]) {

                                        if (Number(arraydata[i]) < Number(arraydata02[j])) {

                                            if (Number(arraydata02[j]) < Number(arraydata03[k])) {
                                                kaime_set.add(arraydata[i] + "-" + arraydata02[j] + "-" + arraydata03[k]);
                                            } else {
                                                if (Number(arraydata[i]) < Number(arraydata03[k])) {
                                                    kaime_set.add(arraydata[i] + "-" + arraydata03[k] + "-" + arraydata02[j]);
                                                } else {
                                                    kaime_set.add(arraydata03[k] + "-" + arraydata[i] + "-" + arraydata02[j]);
                                                }
                                            }

                                        } else {

                                            if (Number(arraydata[i]) < Number(arraydata03[k])) {
                                                kaime_set.add(arraydata02[j] + "-" + arraydata[i] + "-" + arraydata03[k]);
                                            } else {
                                                if (Number(arraydata02[j]) < Number(arraydata03[k])) {
                                                    kaime_set.add(arraydata02[j] + "-" + arraydata03[k] + "-" + arraydata[i]);
                                                } else {
                                                    kaime_set.add(arraydata03[k] + "-" + arraydata02[j] + "-" + arraydata[i]);
                                                }
                                            }

                                        }

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す
                    }

                    if (v_id == 3) { //フォーメーションの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata.length; i++) {

                            for (j = 0; j < arraydata02.length; j++) {

                                for (k = 0; k < arraydata03.length; k++) {

                                    if (arraydata[i] != arraydata02[j] && arraydata[i] != arraydata03[k] && arraydata02[j] != arraydata03[k]) {

                                        if (Number(arraydata[i]) < Number(arraydata02[j])) {

                                            if (Number(arraydata02[j]) < Number(arraydata03[k])) {
                                                kaime_set.add(arraydata[i] + "-" + arraydata02[j] + "-" + arraydata03[k]);
                                            } else {
                                                if (Number(arraydata[i]) < Number(arraydata03[k])) {
                                                    kaime_set.add(arraydata[i] + "-" + arraydata03[k] + "-" + arraydata02[j]);
                                                } else {
                                                    kaime_set.add(arraydata03[k] + "-" + arraydata[i] + "-" + arraydata02[j]);
                                                }
                                            }

                                        } else {

                                            if (Number(arraydata[i]) < Number(arraydata03[k])) {
                                                kaime_set.add(arraydata02[j] + "-" + arraydata[i] + "-" + arraydata03[k]);
                                            } else {
                                                if (Number(arraydata02[j]) < Number(arraydata03[k])) {
                                                    kaime_set.add(arraydata02[j] + "-" + arraydata03[k] + "-" + arraydata[i]);
                                                } else {
                                                    kaime_set.add(arraydata03[k] + "-" + arraydata02[j] + "-" + arraydata[i]);
                                                }
                                            }

                                        }

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }

                } else {  //ボックスの場合

                    //kaime_merge = $.merge(arraydata,arraydata02);
                    kaime_merge = arraydata.concat(arraydata02);

                    const kaime_set = new Set; //オブジェクトにして重複をカット

                    for (i = 0; i < kaime_merge.length; i++) {
                        for (j = 0; j < kaime_merge.length; j++) {
                            for (k = 0; k < kaime_merge.length; k++) {

                                //組み合わせセット
                                //同じ馬番の同士の組み合わせは除外
                                if (kaime_merge[i] != kaime_merge[j] && kaime_merge[i] != kaime_merge[k] && kaime_merge[j] != kaime_merge[k]) {

                                    if (Number(kaime_merge[i]) < Number(kaime_merge[j])) {

                                        if (Number(kaime_merge[j]) < Number(kaime_merge[k])) {

                                            kaime_set.add(kaime_merge[i] + "-" + kaime_merge[j] + "-" + kaime_merge[k]);

                                        } else {

                                            if (Number(kaime_merge[i]) < Number(kaime_merge[k])) {

                                                kaime_set.add(kaime_merge[i] + "-" + kaime_merge[k] + "-" + kaime_merge[j]);

                                            } else {

                                                kaime_set.add(kaime_merge[k] + "-" + kaime_merge[i] + "-" + kaime_merge[j]);

                                            }

                                        }

                                    } else {

                                        if (Number(kaime_merge[i]) < Number(kaime_merge[k])) {

                                            kaime_set.add(kaime_merge[j] + "-" + kaime_merge[i] + "-" + kaime_merge[k]);

                                        } else {

                                            if (Number(kaime_merge[j]) < Number(kaime_merge[k])) {

                                                kaime_set.add(kaime_merge[j] + "-" + kaime_merge[k] + "-" + kaime_merge[i]);

                                            } else {

                                                kaime_set.add(kaime_merge[k] + "-" + kaime_merge[j] + "-" + kaime_merge[i]);

                                            }
                                        }
                                    }

                                }

                            }
                        }

                    }
                    kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                }


            } else {
                //組み合わせなしであれば
                const kaime_set = new Set; //オブジェクトにして重複をカット
                for (i = 0; i < arraydata.length; i++) {

                    for (j = 0; j < arraydata.length; j++) {

                        for (k = 0; k < arraydata.length - 1; k++) {

                            if (arraydata[i] != arraydata[j] && arraydata[i] != arraydata[k] && arraydata[j] != arraydata[k]) {

                                if (Number(arraydata[i]) < Number(arraydata[j])) {

                                    if (Number(arraydata[j]) < Number(arraydata[k])) {
                                        kaime_set.add(arraydata[i] + "-" + arraydata[j] + "-" + arraydata[k]);
                                    } else {
                                        if (Number(arraydata[i]) < Number(arraydata[k])) {
                                            kaime_set.add(arraydata[i] + "-" + arraydata[k] + "-" + arraydata[j]);
                                        } else {
                                            kaime_set.add(arraydata[k] + "-" + arraydata[i] + "-" + arraydata[j]);
                                        }
                                    }

                                } else {

                                    if (Number(arraydata[i]) < Number(arraydata[k])) {
                                        kaime_set.add(arraydata[j] + "-" + arraydata[i] + "-" + arraydata[k]);
                                    } else {
                                        if (Number(arraydata[j]) < Number(arraydata[k])) {
                                            kaime_set.add(arraydata[j] + "-" + arraydata[k] + "-" + arraydata[i]);
                                        } else {
                                            kaime_set.add(arraydata[k] + "-" + arraydata[j] + "-" + arraydata[i]);
                                        }
                                    }

                                }

                            }

                        }

                    }

                }
                kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す
            }

            // HTML作成
            kaime_count = 0;
            for (i = 0; i < kaime_array.length; i++) {
                tenkai_html += '<tr>';
                tenkai_html += '<td class="td Fs26 FwB">' + kaime_array[i] + '</td>';
                tenkai_html += '</tr>';
                kaime_count += 1;
            }
            break;


        case 8: //三連単
            // 買い目の組み合わせを作成
            kaime_array = [];

            if (v_id != 0) { //組み合わせなし以外であれば

                if (v_id != 2) { //ボックスじゃなければ

                    if (v_id == 8) { //1着流しの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata.length; i++) {

                            for (j = 0; j < arraydata02.length; j++) {

                                for (k = 0; k < arraydata02.length; k++) {

                                    if (arraydata[i] != arraydata02[j] && arraydata[i] != arraydata02[k] && arraydata02[j] != arraydata02[k]) {

                                        kaime_set.add(arraydata[i] + "→" + arraydata02[j] + "→" + arraydata02[k]);

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }

                    if (v_id == 9) { //2着流しの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata02.length; i++) {

                            for (j = 0; j < arraydata.length; j++) {

                                for (k = 0; k < arraydata.length; k++) {

                                    if (arraydata02[i] != arraydata[j] && arraydata02[i] != arraydata[k] && arraydata[j] != arraydata[k]) {

                                        kaime_set.add(arraydata[j] + "→" + arraydata02[i] + "→" + arraydata[k]);

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }

                    if (v_id == 10) { //3着流しの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata02.length; i++) {

                            for (j = 0; j < arraydata.length; j++) {

                                for (k = 0; k < arraydata.length; k++) {

                                    if (arraydata02[i] != arraydata[j] && arraydata02[i] != arraydata[k] && arraydata[j] != arraydata[k]) {

                                        kaime_set.add(arraydata[j] + "→" + arraydata[k] + "→" + arraydata02[i]);

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }


                    if (v_id == 11) { //1・2着流しの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata.length; i++) {

                            for (j = 0; j < arraydata02.length; j++) {

                                for (k = 0; k < arraydata03.length; k++) {

                                    if (arraydata[i] != arraydata02[j] && arraydata[i] != arraydata03[k] && arraydata02[j] != arraydata03[k]) {

                                        kaime_set.add(arraydata[i] + "→" + arraydata02[j] + "→" + arraydata03[k]);

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }


                    if (v_id == 12) { //1・3着流しの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata.length; i++) {

                            for (j = 0; j < arraydata03.length; j++) {

                                for (k = 0; k < arraydata02.length; k++) {

                                    if (arraydata[i] != arraydata02[k] && arraydata[i] != arraydata03[j] && arraydata02[k] != arraydata03[j]) {

                                        kaime_set.add(arraydata[i] + "→" + arraydata02[k] + "→" + arraydata03[j]);

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }


                    if (v_id == 13) { //2・3着流しの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata02.length; i++) {

                            for (j = 0; j < arraydata03.length; j++) {

                                for (k = 0; k < arraydata.length; k++) {

                                    if (arraydata02[i] != arraydata03[j] && arraydata02[i] != arraydata[k] && arraydata03[j] != arraydata[k]) {

                                        kaime_set.add(arraydata[k] + "→" + arraydata02[i] + "→" + arraydata03[j]);

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }


                    if (v_id == 3) { //フォーメーションの場合

                        const kaime_set = new Set; //オブジェクトにして重複をカット

                        for (i = 0; i < arraydata.length; i++) {

                            for (j = 0; j < arraydata02.length; j++) {

                                for (k = 0; k < arraydata03.length; k++) {

                                    if (arraydata[i] != arraydata02[j] && arraydata[i] != arraydata03[k] && arraydata02[j] != arraydata03[k]) {

                                        kaime_set.add(arraydata[i] + "→" + arraydata02[j] + "→" + arraydata03[k]);

                                    }

                                }

                            }

                        }
                        kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                    }

                } else {  //ボックスの場合

                    //kaime_merge = $.merge(arraydata,arraydata02);
                    kaime_merge = arraydata.concat(arraydata02);

                    const kaime_set = new Set; //オブジェクトにして重複をカット

                    for (i = 0; i < kaime_merge.length; i++) {
                        for (j = 0; j < kaime_merge.length; j++) {
                            for (k = 0; k < kaime_merge.length; k++) {

                                //組み合わせセット
                                //同じ馬番の同士の組み合わせは除外
                                if (kaime_merge[i] != kaime_merge[j] && kaime_merge[i] != kaime_merge[k] && kaime_merge[j] != kaime_merge[k]) {

                                    kaime_set.add(kaime_merge[i] + "→" + kaime_merge[j] + "→" + kaime_merge[k]);

                                }

                            }
                        }

                    }
                    kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す

                }


            } else {
                //組み合わせなしであれば
                const kaime_set = new Set; //オブジェクトにして重複をカット
                for (i = 0; i < arraydata.length; i++) {

                    for (j = 0; j < arraydata.length; j++) {

                        for (k = 0; k < arraydata.length - 1; k++) {

                            if (arraydata[i] != arraydata[j] && arraydata[i] != arraydata[k] && arraydata[j] != arraydata[k]) {

                                kaime_set.add(arraydata[i] + "-" + arraydata[j] + "-" + arraydata[k]);

                            }

                        }

                    }

                }
                kaime_array = Array.from(kaime_set); //オブジェクトから配列に戻す
            }

            // HTML作成
            kaime_count = 0;
            for (i = 0; i < kaime_array.length; i++) {
                tenkai_html += '<tr>';
                tenkai_html += '<td class="td Fs26 FwB">' + kaime_array[i] + '</td>';
                tenkai_html += '</tr>';
                kaime_count += 1;
            }
            break;

    }

}


