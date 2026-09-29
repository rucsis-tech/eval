<?php

namespace trait;

use ContentKaime;

const TID_TAN_SHO = 1;  //'単勝',   // case 1'=> 単勝 1着になる馬を当てる馬券
const TID_FUKU_SHO = 2; //'複勝',   // case 2'=> 複勝 3着までに入る馬を当てる馬券 7頭以下の場合は、2着までが的中
const TID_WAKU_REN = 3;  //'枠連',   // case 3'=> 枠連 1着と2着になる馬の枠番号の組合せを当てる馬券
const TID_UMA_REN = 4;  //'馬連',   // case 4'=> 馬連 1着と2着になる馬の馬番号の組合せを当てる馬券
const TID_UMA_TAN = 5;  //'馬単',   // case 5'=> 馬単 1着と2着になる馬の馬番号を着順通りに当てる馬券
const TID_WIDE = 6;     //'ワイド',  // case 6'=> ワイド 3着までに入る2頭の組合せを馬番号で当てる馬券
const TID_3REN_PUKU = 7; //'3連複',   // case 7'=> 3連複 1着から3着に入る3頭の馬を選び、その馬たちが順番に関係なく3着以内に入れば的中
const TID_3REN_TAN = 8; //'3連単',   // case 8'=> 3連単 1着、2着、3着となる馬の馬番号を着順通りに的中させる

const VID_NONE = 0;     //'なし',
const VID_NAGASHI = 1; // '流し',           // 1'=> 特定の馬を軸にして複数の組み合わせ
const VID_BOX = 2;     //'ボックス',        // 2'=> いくつか選択した馬（枠）番について、その全ての組合せを購入する

trait KaimeTrait {

    static private array $cancel = [];
    static private array $waku = [];

    static public function tenkaiFunc($race_code, $t_id, $v_id, $arrayData01, $arrayData02, $arrayData03) {
        // ----------------------------------------------------------------
        // 出走取消と枠の頭数を取得
        // 買い目の有効点数の判定で、出走取消、競走除外を見る際、cancel値を0以外で判定するのではなく 1 ～ 3
        // $data['cancel']
        // 1	出走取消
        // 2	発走除外
        // 3	競走除外
        // 4	競走中止
        // 5	失格
        // 6	落馬再騎乗
        // 7	降着
        $UmaList = self::table('race_uma')->where('race_code', $race_code)->all();
        self::$waku = [];
        self::$cancel = [];
        foreach ($UmaList as $Uma) {
            $data = $Uma->json('info');
            $umaban = $data['umaban'];
            $wakuban = $data['wakuban'];
            // 出走取消判定
            self::$cancel[$umaban] = ($data['cancel'] == 1 || $data['cancel'] == 2 || $data['cancel'] == 3);
            //  枠の頭数設定
            if (!isset(self::$waku[$wakuban])) self::$waku[$wakuban] = 0;
            if (!self::$cancel[$umaban]) self::$waku[$wakuban]++;
        }


        // ------------------------------------------------------------------
        //　１枚買い
        if (in_array($t_id, [TID_TAN_SHO, TID_FUKU_SHO])) {
            //case TID_TAN_SHO: //　単勝 1着になる馬を当てる
            //case TID_FUKU_SHO: //　複勝 3着までに入る馬を当てる馬券 7頭以下の場合は、2着までが的中
            $array = [];
            foreach ($arrayData01 as $bango) {
                // 出走取消以外を返す
                if (!self::$cancel[$bango]) $array[] = $bango;
            }
            return $array;
        }

        // ------------------------------------------------------------------
        // ２連
        if (in_array($t_id, [TID_WAKU_REN])) {
            //case TID_WAKU_REN: // case 3: 枠連 1着と2着になる馬の枠番号の組合せを当てる馬券
            $optionsWaku = ['array1' => $arrayData01, 'ren' => 2, 't_id' => $t_id, 'v_id' => $v_id];
            if ($v_id === VID_NONE) {
                // なし・実質ボックス買い　１列目同士でかけあわせ
                $optionsWaku['array2'] = $arrayData01;
            } else if ($v_id === VID_NAGASHI) {
                // 流し２列目とのかけ合わせ
                $optionsWaku['array2'] = $arrayData02;
            } else if ($v_id === VID_BOX) {
                // ボックス １列目同士でかけあわせ
                $optionsWaku['array2'] = $arrayData01;
                // 受け取った2列目はゾロ目許可フラグとして機能させる
                $optionsWaku['arrayDouWaku'] = $arrayData02;
            }

            return self::assort($optionsWaku);
        }

        if (in_array($t_id, [TID_UMA_REN, TID_UMA_TAN, TID_WIDE])) {
            //case TID_UMA_REN: // case 4: 馬連 1着と2着になる馬の馬番号の組合せを当てる馬券
            //case TID_UMA_TAN: // case 5: 馬単 1着と2着になる馬の馬番号を着順通りに当てる馬券
            //case TID_WIDE: // case 6: ワイド 3着までに入る2頭の組合せを馬番号で当てる馬券
            $options2 = ['array1' => $arrayData01, 'ren' => 2, 't_id' => $t_id, 'v_id' => $v_id];
            if ($v_id === 2 || $v_id === 0) {
                // ボックス買い　１列目同士でかけあわせ
                $options2['array2'] = $arrayData01;
            } else if ($v_id === 14) {
                // 軸1頭マルチ
                $options2['array1'] = $arrayData01;
                $options2['array2'] = $arrayData02;
                $j1 = self::assort($options2);
                $options2['array1'] = $arrayData02;
                $options2['array2'] = $arrayData01;
                $j2 = self::assort($options2);

                return array_unique(array_merge($j1, $j2));

            } else {
                // その他は２列目とのかけ合わせ
                $options2['array2'] = $arrayData02;
            }
            return self::assort($options2);
        }


        // ------------------------------------------------------------------
        // ３連

        //case TID_3REN_PUKU: //　3連複 1着から3着に入る3頭の馬を選び、その馬たちが順番に関係なく3着以内に入れば的中
        //case TID_3REN_TAN: //　3連単 1着、2着、3着となる馬の馬番号を着順通りに的中させる
        $options3 = ['array1' => $arrayData01, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id];

        if ($v_id === 2 || $v_id === 0) {
            //  ボックス買い　    指定なし　
            //  １列目同士でかけあわせ
            $options3['array2'] = $arrayData01;
            $options3['array3'] = $arrayData01;
        } else if ($v_id === 4 || $v_id === 6 || $v_id === 8) {
            //       軸馬1着流し    軸馬1頭流し     1着流し
            //       １列目を軸に２列目で残りを埋める
            $options3['array2'] = $arrayData02;
            $options3['array3'] = $arrayData02;
        } else if ($v_id === 9) {
            //        2着流し
            //       ２列目(２着指定)を軸に１列目で残りを埋める
            $options3['array2'] = $arrayData02;
            $options3['array3'] = $arrayData01;
        } else if ($v_id === 10) {
            //        3着流し
            //       ２列目(3着指定)を軸に１列目で残りを埋める
            $options3['array2'] = $arrayData01;
            $options3['array3'] = $arrayData02;
        } else if ($v_id === 14) {
            // 3連単 軸1頭マルチ
            $j1 = self::assort(['array1' => $arrayData01, 'array2' => $arrayData02, 'array3' => $arrayData02, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);
            $j2 = self::assort(['array1' => $arrayData02, 'array2' => $arrayData01, 'array3' => $arrayData02, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);
            $j3 = self::assort(['array1' => $arrayData02, 'array2' => $arrayData02, 'array3' => $arrayData01, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);

            return array_unique(array_merge($j1, $j2, $j3));
        } else if ($v_id === 15) {
            // 3連単 軸2頭マルチ
            $j1 = self::assort(['array1' => $arrayData01, 'array2' => $arrayData02, 'array3' => $arrayData03, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);
            $j2 = self::assort(['array1' => $arrayData01, 'array2' => $arrayData03, 'array3' => $arrayData02, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);
            $j3 = self::assort(['array1' => $arrayData02, 'array2' => $arrayData01, 'array3' => $arrayData03, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);
            $j4 = self::assort(['array1' => $arrayData03, 'array2' => $arrayData01, 'array3' => $arrayData02, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);
            $j5 = self::assort(['array1' => $arrayData02, 'array2' => $arrayData03, 'array3' => $arrayData01, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);
            $j6 = self::assort(['array1' => $arrayData03, 'array2' => $arrayData02, 'array3' => $arrayData01, 'ren' => 3, 't_id' => $t_id, 'v_id' => $v_id]);

            return array_unique(array_merge($j1, $j2, $j3, $j4, $j5, $j6));

        } else {
            // その他　１列目２列目３列目の順のかけ合わせ
            $options3['array2'] = $arrayData02;
            $options3['array3'] = $arrayData03;
        }

        return self::assort($options3);
    }


    static private function assort($options) {
        $array1 = $options['array1'] ?? [];
        $array2 = $options['array2'] ?? [];
        $array3 = $options['array3'] ?? [];

        if (count($array1) === 0) $array1[] = 0;
        if (count($array2) === 0) $array2[] = 0;
        if (count($array3) === 0) $array3[] = 0;

        $kaime_set = [];
        for ($i = 0; $i < count($array1); $i++) {
            for ($j = 0; $j < count($array2); $j++) {
                for ($k = 0; $k < count($array3); $k++) {

                    $item = [];
                    if ($array1[$i] > 0) $item[] = $array1[$i];
                    if ($array2[$j] > 0) $item[] = $array2[$j];
                    if ($array3[$k] > 0) $item[] = $array3[$k];

                    // 重複チェック（ゾロ目不可）
                    $uniq = array_unique($item);
                    if (count($item) !== count($uniq)) {
                        // 枠連以外はありえないのでゾロ目NG
                        if ($options['t_id'] !== TID_WAKU_REN) continue;
                        // 枠連ではあるが、枠に2頭いない場合はどうあってもNG
                        $waku = $uniq[0];
                        if (self::$waku[$waku] < 2) continue;
                        // ボックスの場合、同枠指定があるかを確認
                        if ($options['v_id'] === VID_BOX)
                            if (!in_array($waku, $options['arrayDouWaku'])) continue;
                    }

                    // 連数必要数チェック
                    if (count($item) !== $options['ren']) continue;

                    // 馬単/3連単　以外は着順不同のため　順番固定のためソート
                    if ($options['t_id'] !== TID_UMA_TAN && $options['t_id'] !== TID_3REN_TAN) {
                        asort($item);
                    }

                    $cancel = false;
                    if ($options['t_id'] === TID_WAKU_REN) {
                        // キャンセル枠チェック
                        foreach ($item as $bango) {
                            if (self::$waku[$bango] === 0) $cancel = true;
                        }
                    } else {
                        // キャンセル馬チェック
                        foreach ($item as $bango) {
                            if (self::$cancel[$bango]) $cancel = true;
                        }
                    }
                    if ($cancel) continue;

                    $kaime = implode('-', $item);
                    if (!in_array($kaime, $kaime_set)) $kaime_set[] = $kaime;
                }
            }
        }
        return $kaime_set;

    }

    /**
     * ContentKaime/Windexオブジェクトの場合のカイメ展開
     * @return array
     * @throws \Exception
     */

    public function tenkai() {

        $pattern_id = $this->work('pattern_id');
        $arrayData01 = $this->work('selectList01');
        $arrayData02 = $this->work('selectList02');
        $arrayData03 = $this->work('selectList03');
        $v_id = ContentKaime::PATTERNS[$pattern_id]['v_id'];
        $t_id = ContentKaime::PATTERNS[$pattern_id]['t_id'];

        return self::tenkaiFunc($this->race_code, $t_id, $v_id, $arrayData01, $arrayData02, $arrayData03);

    }


    /**
     * 成績判定
     * @return array
     * @throws \Exception
     */
    public function judgement() {
        $race_code = $this->race_code;
        $pattern_id = $this->work('pattern_id');
        $ticket_id = ContentKaime::PATTERNS[$pattern_id]['t_id'];

        // 結果取得
        $Info = self::table('race_info')->where('race_code', $race_code)->first();
        if (empty($Info)) return [];
        $result = $Info->json('result');
        if (empty($result)) return [];

        // ----------------------------------------------------------------
        // 左記の「馬柱API」で取得したデータのパラメータ「commit」のデータが「5」~「7」（※注）であれば「レース確定」状態となり
        //パラメータ「haraimodoshi」の中身を使って的中判定を行う。
        //※注）「9」にレース中止時のステータスがあるので、「5」以上という判定はしないようにする。
        $commit = $result['commit'] ?? 0;
        if ($commit == 9) {
            // レース中止確定
            $this->work(['cancel' => 1]);
            return ['total' => 0, 'payoff' => 0, 'result' => json_encode([]), 'work' => $this->work];
        }
        if ($commit < 5 || 7 < $commit) return [];


        // ----------------------------------------------------------------
        // 払い戻し計算
        $haraimodoshi = $result['haraimodoshi'] ?? [];
        $ticket_key = ContentKaime::TICKET_KEYS[$ticket_id];
        $amount = $this->amount;
        $total = 0;
        $payoff = 0;
        $result = [];


        if (isset($haraimodoshi[$ticket_key])) {
            $kaime_set = $this->tenkai();   // 自身のカイメ一覧を展開
            foreach ($kaime_set as $kaime) {
                $total += $amount;
                foreach ($haraimodoshi[$ticket_key] as $win) {
                    if ($kaime == $win['umaban']) {
                        // 的中した
                        $returned = str_replace(',', '', $win['kingaku']);
                        $get = $amount / 100 * $returned;
                        $payoff += $get;
                        $result[(string)$kaime] = $get;
                    }
                }
            }
            $this->work(['kaime_count' => count($kaime_set)]);
            return ['total' => $total, 'payoff' => $payoff, 'result' => json_encode($result), 'work' => $this->work];

        } else {
            // チケットキーが無いとかそんなことある？
            echo "NO KEY {$haraimodoshi} in {$race_code}(kaime:$this->id) \n";
            return [];
        }

    }
}