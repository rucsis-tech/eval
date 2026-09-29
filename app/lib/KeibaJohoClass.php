<?php
const MAKE_KEIBA_API_BASE_URL = 'https://api.winsight.jp/keiba/api/json/';		// データ生成用サーバー
const KEIBA_API_BASE_URL = 'https://api2.winsight.jp/keiba/api/json/';			// データ取得用サーバー
const DEV_KEIBA_API_BASE_URL = 'http://13.158.37.30:1080/keiba/api/json/';		// テストサーバー
const UMA_DATA_BEGIN = 2000;	// 競走馬データがある生年
const BACHU_DATA_BEGIN = 2015;	// 馬柱データの最低年

class KeibaJohoClass {
	// 月の開催一覧(http://mail01.meta-pog.com/mail/uma/json/kaisai/kaisai.php?ym=202503)
	private $MonthKaisaiJohoRequestURL  = KEIBA_API_BASE_URL . "kaisai/kaisai.php";

	// 開催日のレース一覧とその前後の開催予定()
	private $KaisaiJohoRequestURL = KEIBA_API_BASE_URL . "race/racelist.php";

	// 馬柱(?date=yyyymmdd&jyo=05&bango=2)
	private $BachuJohoRequestURL  = KEIBA_API_BASE_URL . "uma/umabashira.php";
	// 馬柱(DEV)
	//private $BachuJohoRequestURL  = DEV_KEIBA_API_BASE_URL . "uma/dev_umabashira.php";

	// レース傾向（?date=yyyymmdd)
	private $RaceKeikouRequesURL = KEIBA_API_BASE_URL . "keikou/keikou.php";

	// 日別結果払戻（?date=yyyymmdd)
	private $RaceDayHaraiRequesURL =  KEIBA_API_BASE_URL . "dayharai/dayharai.php";

	// 出走予定馬
	private $ShutubaJohoRequesURL =  KEIBA_API_BASE_URL . "myuma/runlist.php";
	// 出走予定馬(DEV)
	//private $ShutubaJohoRequesURL =  DEV_KEIBA_API_BASE_URL . "myuma/runlist.php";

	// 騎乗予定
	private $KijyoJohoRequesURL =  KEIBA_API_BASE_URL . "myuma/runlistKishu.php";
	// 騎乗予定（DEV）
	//private $KijyoJohoRequesURL =  DEV_KEIBA_API_BASE_URL . "myuma/runlistKishu.php";


	// レースの全オッズデータ
	private $RaceOddsJohoRequesURL = KEIBA_API_BASE_URL . "odds/odds.php";

	// 過去10年・枠順
	private $Kako10WakuJunJohoRequesURL =  KEIBA_API_BASE_URL . "last10/wakuban.php";
	//private $Kako10WakuJunJohoRequesURL =  DEV_KEIBA_API_BASE_URL . "last10/wakuban.php";

	// 過去10年・騎手
	private $Kako10KishuJohoRequesURL =  KEIBA_API_BASE_URL . "last10/kishu.php";
	// 過去10年・騎手(DEV)
	//private $Kako10KishuJohoRequesURL =  DEV_KEIBA_API_BASE_URL . "last10/kishu.php";

	// 過去10年・種牡馬
	private $Kako10ShubobaJohoRequesURL = KEIBA_API_BASE_URL . "last10/shuboba.php";

	// 過去10年・母父
	private $Kako10HahachichiJohoRequesURL = KEIBA_API_BASE_URL . "last10/hahachichi.php";

	// 過去10年・前走
	private $Kako10ZensoJohoRequesURL = KEIBA_API_BASE_URL . "last10/zenso.php";

	// 過去10年・脚質
	private $Kako10KyakushitsuJohoRequesURL = KEIBA_API_BASE_URL . "last10/kyakushitsu.php";

	// 過去10年・人気
	private $Kako10NinkiJohoRequesURL = KEIBA_API_BASE_URL . "last10/ninki.php";

	// 過去10年・斤量
	private $Kako10KinryoJohoRequesURL = KEIBA_API_BASE_URL . "last10/kinryo.php";

	// 過去10年・世代
	private $Kako10SedaiJohoRequesURL = KEIBA_API_BASE_URL . "last10/sedai.php";

	// 過去10年・東西
	private $Kako10TozaiJohoRequesURL = KEIBA_API_BASE_URL . "last10/tozai.php";

	// 競走馬データ
	private $kyosobaJohoRequesURL = KEIBA_API_BASE_URL . "data/kyosoba.php";
	// 競走馬データ(DEV)
	//private $kyosobaJohoRequesURL = DEV_KEIBA_API_BASE_URL . "data/kyosoba.php";

	// 騎手データ
	private $kishuJohoRequesURL = KEIBA_API_BASE_URL . "data/kishu.php";
	// 騎手データ(DEV)
	//private $kishuJohoRequesURL = DEV_KEIBA_API_BASE_URL . "data/kishu.php";

	// Win5データ
	private $win5JohoRequesURL = KEIBA_API_BASE_URL . "data/win5.php";
//	private $win5JohoRequesURL = DEV_KEIBA_API_BASE_URL . "data/win5.php";

	//-------------------------------------------------------------------------------------------
	private $grade_priority = [	"ＧⅠ" => 1,"ＧⅡ" => 2,"ＧⅢ" => 3,
								"Ｊ･ＧⅠ" => 4,"Ｊ･ＧⅡ" => 5,"Ｊ･ＧⅢ" => 6,
								"ＪｐｎⅠ" => 7,"ＪｐｎⅡ" => 8,"ＪｐｎⅢ" => 9,
							 ];
	private $circledNumbers = [	'-', '①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨', '⑩',
								'⑪', '⑫', '⑬', '⑭', '⑮', '⑯', '⑰', '⑱', '⑲', '⑳' ];
	private $daysOfWeek = ['日', '月', '火', '水', '木', '金', '土'];
	private $jyo_list = ['01'=>'札幌','02'=>'函館','03'=>'福島','04'=>'新潟','05'=>'東京',
					  	'06'=>'中山','07'=>'中京','08'=>'京都','09'=>'阪神','10'=>'小倉' ];
	public $jyo_name = ['札幌'=>'01','函館'=>'02','福島'=>'03','新潟'=>'04','東京'=>'05',
					  	'中山'=>'06','中京'=>'07','京都'=>'08','阪神'=>'09','小倉'=>'10' ];

	private $kyakushitsu_text = ['0'=>'','1'=>'逃げ','2'=>'先行','3'=>'差し','4'=>'追込' ];

	private $massho_kubun_text = ['0'=>'現役','1'=>'抹消' ];
	private $tozaishozoku_text = ['0'=>'','1'=>'東', '2'=>'西', '3'=>'地', '4'=>'外' ];
	public $cancel_text = ['0'=>'','1'=>'出走取消', '2'=>'発走除外', '3'=>'競走除外', '4'=>'競走中止', '5'=>'失格', '6'=>'落馬再騎乗', '7'=>'降着' ];
	public $cancel_2line_text = ['0'=>'','1'=>'出走<br>取消', '2'=>'発走<br>除外', '3'=>'競走<br>除外', '4'=>'競走<br>中止', '5'=>'失格', '6'=>'落馬<br>再騎乗', '7'=>'降着' ];

	//-----------------------------------------------------------------------------------------
	private $kaisai_joho;			// APIから取得データ（元：開催日レース一覧）
	private	$bachu_joho;			// APIからの取得データ（元：馬柱情報）
	private $month_kaisai_joho;		// 月の開催情報（元：開催日一覧）
	private $kaisai_list;			// 対象日からみた開始日と開催場所のリスト（元：開催日レース一覧）
	private $bangumi_joho;			// 対象日の番組情報（元：開催日レース一覧）
	private $race_joho;				// 指定レースのレース情報（元：馬柱データ）
	private $uma_joho;				// 指定レースの出走馬情報（元：馬柱データ）
	private $race_keikou_joho;		// レース傾向の情報（元：レース傾向）
	private $day_harai_joho;		// 日別の結果払戻情報
	private $shutuba_joho;			// 現在日より5日前から30日後の間に出走予定の馬
	private $race_odds_joho;		// レースの全オッズデータ
	private $kako10_wakuJun_joho;	// 過去10年 枠順
	private $kako10_kishu_joho;		// 過去10年 騎手
	private $kako10_shuboba_joho;	// 過去10年 種牡馬
	private $kako10_hahachichi_joho;	// 過去10年 母父
	private $kako10_zenso_joho;			// 過去10年 前走
	private $kako10_kyakushitsu_joho;	// 過去10年 脚質
	private $kako10_ninki_joho;			// 過去10年 人気
	private $kako10_kinryo_joho;		// 過去10年 斤量
	private $kako10_sedai_joho;			// 過去10年 世代
	private $kako10_tozai_joho;			// 過去10年 東西

	private $current_date = "";			// YYYYMMDD
	private $current_jyo = ""; 			// 01:札幌 02:函館 03:福島 04:新潟 05:東京 06:中山 07:中京 08:京都 09:阪神 10:小倉
	private $current_kai = "";			// KK:回
	private $current_nichi = "";		// NN：日目
	private $current_raceno = "";		// RR：レース番号
	private $current_kaisaiCode = "";	// YYYYMMDDBBKKNN

	public $month_kaisai_days;

	//-----------------------------------------------------------------------------------------
	public function getCurrentDate(){ return $this->current_date; }
	public function getCurrentJyo(){ return $this->current_jyo; }
	public function getCurrentRaceNo(){ return $this->current_raceno; }
	public function getCurrentKaisaiCode(){ return $this->current_kaisaiCode; }
	public function resetKaisaiList(){
		$this->kaisai_joho = null;
		$this->kaisai_list = null;
		$this->bangumi_joho = null;
	}

	public function numberToJpn( $num )
	{
	    // 整数以外は除外
	    $num = intval($num);
	    if ($num == 0) {
	        return "0";
	    }
	    $result = "";
	    // 億
	    if ($num >= 100000000) {
	        $oku = floor($num / 100000000);
	        $result .= $oku . "億";
	        $num %= 100000000;
	    }

	    // 万
	    if ($num >= 10000) {
	        $man = floor($num / 10000);
	        $result .= $man . "万";
	        $num %= 10000;
	    }
/* 1万未満切り捨てなので、コメントアウト
	    // 残り
	    if ($num > 0) {
	        $result .= $num;
	    }
*/
	    return $result;
	}

	// --------------------------------------------------------
	//　競走馬データがあるか（年で判定）
	// --------------------------------------------------------
	public function isExistUmaData( $ktb )
	{
	    if (empty($ktb)) return 0;
	    if (substr($ktb, 0, 4) < UMA_DATA_BEGIN) return 0;
	    return 1;
	}
	// --------------------------------------------------------
	//　馬柱データがあるか（年で判定）
	// --------------------------------------------------------
	public function isExistBachuData( $rc )
	{
	    if (empty($rc)) return 0;
	    if (substr($rc, 0, 4) < BACHU_DATA_BEGIN) return 0;
	    return 1;
	}
	// --------------------------------------------------------
	//　東西所属の文字列を取得
	// --------------------------------------------------------
	public function getTozaishozokuText( $code )
	{
		if( empty($this->tozaishozoku_text[$code]) ) return '';
		return $this->tozaishozoku_text[$code];
	}
	// --------------------------------------------------------
	//　抹消区分の文字列を取得
	// --------------------------------------------------------
	public function getMasshoText( $code )
	{
		if( empty($this->massho_kubun_text[$code]) ) return '';
		return $this->massho_kubun_text[$code];
	}

    // --------------------------------------------------------
	//　コーナー順位の文字列を取得
	// --------------------------------------------------------
	public function getCornerText($corners)
	{
		$result_str = '';
		foreach ($corners as $key => $value) {
			if ($value > 0 && $value <= 20) {
				$result_str = $result_str . $this->circledNumbers[$value];
			}
		}
		return $result_str;
	}

	// --------------------------------------------------------
	//　グレード名からグレード番号を取得する
	//  ※グレード番号はJRAの定めたものではない。
	// --------------------------------------------------------
	public function getGradeCode( $grade_name )
	{
		if( $grade_name == "" ) return 0;
		return $this->grade_priority[$grade_name];
	}

	// --------------------------------------------------------
	//　ymdから月/日（曜日）のテキストを取得
	// --------------------------------------------------------
	public function getMonthDayText( $ymd )
	{
		$ymd = substr( $ymd, 0, 8 );

		$dateObj = DateTime::createFromFormat('Ymd', $ymd);
		if ($dateObj === false) {
			return "";
		}
		$formattedDate = $dateObj->format('n/j') . '(' . $this->daysOfWeek[$dateObj->format('w')] . ')';
		return $formattedDate;
	}
	public function getMonthDayTextZen( $ymd )
	{
		$ymd = substr( $ymd, 0, 8 );
		$dateObj = DateTime::createFromFormat('Ymd', $ymd);
		if ($dateObj === false) {
			return "";
		}
		$formattedDate = $dateObj->format('n月j日') . '(' . $this->daysOfWeek[$dateObj->format('w')] . ')';
		return $formattedDate;
	}

	public function getNengappiText( $ymd ){
		$ymd = substr( $ymd, 0, 8 );
		$dateObj = DateTime::createFromFormat('Ymd', $ymd);
		if ($dateObj === false) {
			return "";
		}
		$formattedDate = $dateObj->format('Y年n月j日') . '(' . $this->daysOfWeek[$dateObj->format('w')] . ')';
		return $formattedDate;
	}
	public function getYmdText( $ymd ){
		$ymd = substr( $ymd, 0, 8 );
		$dateObj = DateTime::createFromFormat('Ymd', $ymd);
		if ($dateObj === false) {
			return "";
		}
		$formattedDate = $dateObj->format('Y/m/d');
		return $formattedDate;
	}
	public function getYnjText( $ymd ){
		$ymd = substr( $ymd, 0, 8 );
		$dateObj = DateTime::createFromFormat('Ymd', $ymd);
		if ($dateObj === false) {
			return "";
		}
		$formattedDate = $dateObj->format('Y/n/j');
		return $formattedDate;
	}
	// --------------------------------------------------------
	// 運営コード（1:中央、2:地方、3:海外）
	// --------------------------------------------------------
	public function getUneiCode( $race_code )
	{
		$result = 0;
		$jyo_code = substr( $race_code, 8, 2  );
		if( $jyo_code != '' ){
			if( isset( $this->jyo_list[$jyo_code]) ){
				$result = 1;
			}else if( ctype_digit($jyo_code) ){
				$result = 2;
			}else{
				$result = 3;
			}
		}
		return $result;
	}

	// --------------------------------------------------------
	// 競馬場コードで競馬場名を取得
	// --------------------------------------------------------
	public function getJyoText( $jyoCode )
	{
		if( isset($this->jyo_list[$jyoCode] ) ){
			return $this->jyo_list[$jyoCode];
		}
		return "";
	}
	public function getJyoCode( $jyoText)
	{
		if( isset($this->jyo_name[$jyoText] ) ){
			return $this->jyo_name[$jyoText];
		}
		return "";
	}

	// --------------------------------------------------------
	// 脚質傾向番号　→  文字列
	// --------------------------------------------------------
	public function getKyakushitsuText( $Kyakushitsu_code )
	{
		if( isset($this->kyakushitsu_text[$Kyakushitsu_code] ) ){
			return $this->kyakushitsu_text[$Kyakushitsu_code];
		}
		return "";
	}
	// --------------------------------------------------------
	// データのロード（共通）
	// --------------------------------------------------------
	private function loadata( $url )
	{
		try {
			$contextOptions = [
			    'http' => [
			        'timeout' => 10,
			        'ignore_errors' => true,
			    ],
			];

			$scheme = parse_url($url, PHP_URL_SCHEME);
			if ($scheme === 'https') {
				    $contextOptions['ssl'] = [
			        'verify_peer' => true,
			        'verify_peer_name' => true,
			    ];
			}
			$context = stream_context_create($contextOptions);

			global $http_response_header;
			$result = @file_get_contents($url, false, $context);
			if ($result === false) {
	            // 接続不可・タイムアウト
	            return null;
	        }
			if (!empty($http_response_header[0]) &&
	            preg_match('#^HTTP/\d+\.\d+\s+(\d+)#', $http_response_header[0], $m)) {

	            if ((int)$m[1] >= 400) {
	                return null;
	            }
	        }

			$data = json_decode($result, true);
			if (json_last_error() !== JSON_ERROR_NONE) return null;
			if (isset($data['error'])) return null;
			return $data;
		} catch (Throwable $e) {
		    return null;
		}
	}
	// ========================================================
	// 月の開催スケジュールを取得（競馬API使用）
	// ym = yyyymm
	// データは「month_kaisai_joho」に格納
	// ========================================================
	private function loadMonthKaisaiJoho( $ym, $reload=0 )
	{
	  if( $reload ) $this->month_kaisai_joho = null;
	  try{
		if( empty($this->month_kaisai_joho) ){
			$url = $this->MonthKaisaiJohoRequestURL . "?ym=" . $ym;
			$result = $this->loadata( $url );
			$this->month_kaisai_joho = $result;
		}
		return $this->month_kaisai_joho;
	}catch (Exception $e){ return null; }
	}

	// ========================================================
	// 開催日レース情報の取得（競馬API使用）
	// ymd = yyyymmdd
	// データは「kaisai_joho」に格納
	// 読み込み後も基準日を「current_date」に格納。
	// ========================================================
	private function loadKaisaiJoho( $ymd, $reload=0 )
	{
	  if( $reload ) $this->kaisai_joho = null;
	  try{
		if( empty($this->kaisai_joho) ){
			$this->kaisai_joho = null;
			$url = $this->KaisaiJohoRequestURL . "?date=" . $ymd;
			$result = $this->loadata( $url );
			if( empty($result['dates'])) return null;

			foreach ($result['keibajo'] as $parentKey => $parent) {
				$allCommit9 = true;
			    foreach ($parent['race'] as $race) {
			        if( $race['commit'] != 9 ) {
			            $allCommit9 = false;
			            break;
			        }
			    }
			    if ($allCommit9) {
			       unset($result['keibajo'][$parentKey]);
			    }
			}
//		echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "</pre>";
			$this->current_date = $result['dates'][0]['date'];
			$this->kaisai_joho = $result;
		}
		return $this->kaisai_joho;
	}catch (Exception $e){ return null; }
	}

	// ========================================================
	// 馬柱データの取得（競馬API使用）
	// racecode = yyyy . mm . dd . 場所(2) .　回(2) . 日(2) . レース番号(2)
	// データは「bachu_joho」に格納
	// ========================================================
	private function loadBachuJoho( $racecode, $reload=0 )
	{
	  if( $reload ) $this->bachu_joho = null;
	  if( strlen($racecode) != 16 ) return false;
	  try{
		if( empty($this->bachu_joho) ){
			$d = substr( $racecode, 0, 8 );
			$j = substr( $racecode, 8, 2 );
			$r = substr( $racecode, 14, 2 );

			$url = $this->BachuJohoRequestURL . "?date=" . $d . "&jyo=" . $j . "&bango=" . $r;
			$result = $this->loadata( $url );
 			if( is_null( $result) ) return false;

			$racecode = $result['race']['race_code'];
			$this->current_date = substr( $racecode, 0, 8 );
			$this->current_jyo = substr( $racecode, 8, 2 );
			$this->current_raceno = $result['race']['race_bango'];

			// レース情報
			$this->race_joho = $result['race'];

			// 払戻のデータはない部分を削除
			foreach ($result['race']['haraimodoshi'] as $h_key => &$h_value) {
				foreach ($h_value as $key => $value) {
					if( $value['ninki']+0 == 0 ){
						unset($h_value[$key]);
					}
				}
			}
			unset($h_value);

			$bachu_state = 0;		// 馬柱の確定
			if( isset( $result['uma']['1']['umaban'] )){
				if( $result['uma']['1']['umaban'] ) $bachu_state = 1;
			}
			$result['race']['bachu_state'] = $bachu_state;

			// 馬柱情報
			$kako = [];			// 過去走処理用

			foreach ($result['uma'] as $key => $value) {
				$result['uma'][$key]['shozoku_ryaku'] = mb_substr( $value['tozai_shozoku'], 0, 1);
				$result['uma'][$key]['banushimei'] = str_replace("　", "", $value['banushimei'] );
				if(is_numeric($result['uma'][$key]['zogen_sa'])) {
					$result['uma'][$key]['weight_alert'] = abs($result['uma'][$key]['zogen_sa'])>=10?1:0;
				}else{
					$result['uma'][$key]['weight_alert'] = 0;
				}
				$result['uma'][$key]['corners_text'] = $this->getCornerText( $value['corner'] );
				$result['uma'][$key]['chakujun'] = intval($value['chakujun']);
				if( $value['bataiju'] == '0' ){
					$result['uma'][$key]['bataiju'] = '';
				}

				$kyakushitu_marks = array_fill(0, 4, 0);	// 脚質マークの情報
				$tenkai_type = 0;	// 展開予想用
				// 過去走
				if( empty($value['old']) ){
					$result['uma'][$key]['old'] = [];
				}else{
					// 過去走のデータ処理
					$_kyakushitu_count = array_fill(0, 5, 0);
					$_kyakushitu_max_count = 0;
					foreach ($value['old']  as $past_key => $past_value) {
						$result['uma'][$key]['old'][$past_key]['kyosomei'] = mb_substr($past_value['kyosomei'], 0, 8, 'UTF-8');
						$result['uma'][$key]['old'][$past_key]['weight_alert'] = abs($result['uma'][$key]['old'][$past_key]['zogen_sa'])>=10?1:0;
						$result['uma'][$key]['old'][$past_key]['corners_text'] =  $this->getCornerText( $past_value['corner'] );
						$d = $past_value['date'];
						$result['uma'][$key]['old'][$past_key]['kaisai_text'] =
							sprintf( "%02d/%02d/%02d %s %s%s",
								substr( $d, 2, 2 ),	substr( $d, 4, 2 ),	substr( $d, 6, 2 ),
								mb_substr( $past_value['jyo'], 0, 4), $past_value['track'], $past_value['kyori'] );

						// VS用として同レースのカウント
						$race_code = $past_value['race_code'];
						if( empty( $kako[$race_code]) ){
							$kako[$race_code] = 1;
						}else{
							$kako[$race_code]++;
						}

						// JRA開催レースを開催場所で判定
						$is_jra = 0;
						if( isset( $this->jyo_name[$past_value['jyo']]) ){
							$is_jra = 1;
						}
						$result['uma'][$key]['old'][$past_key]['is_jra'] = $is_jra;
						$result['uma'][$key]['old'][$past_key]['unei_code'] = $this->getUneiCode($past_value['race_code']);

						// 脚質判定を使用して、脚質マークを作るための前処理
						$_kyakusitu_type = is_numeric($past_value['kyakushitsu']) ? (int)$past_value['kyakushitsu'] : 0;
						if( $_kyakusitu_type >= 1 && $_kyakusitu_type <= 4  ){
							$_kyakushitu_count[$_kyakusitu_type]++;
							if( $_kyakushitu_max_count < $_kyakushitu_count[$_kyakusitu_type] ) $_kyakushitu_max_count = $_kyakushitu_count[$_kyakusitu_type];
						}

					}
					/* 過去走のループ処理（end）*/
/*デバッグ用
					echo "馬番：" . $value['umaban'] . "　" . $value['bamei'] . "<br>";
					echo "最大回数：" . $_kyakushitu_max_count . "<br>";
					echo "　逃　先　差　追<br>";
					echo implode(',　', $_kyakushitu_count);
					echo "<br>------------<br>";
*/

					//　脚質マーク情報 ・展開分類
					if( $_kyakushitu_max_count ){
						for( $i = 1; $i < 5; $i++ ){
							if( $_kyakushitu_max_count == $_kyakushitu_count[$i] ){
								$kyakushitu_marks[$i-1] = 2;	// 最大回数の脚質
								$tenkai_type = $i;
							}else if( $_kyakushitu_count[$i] >= 2 ){
								$kyakushitu_marks[$i-1] = 1;	// 2回以上の脚質
							}
						}
					}
				}
				/* 過去走処理（end) */
				// 脚質マークの情報を追加
				$result['uma'][$key]['kyakushitu_marks'] = $kyakushitu_marks;
				$result['uma'][$key]['tenkai_type'] = $tenkai_type;
			}
			/* 出走馬処理（end）*/


			// 過去走データにVS用データを埋め込む
			foreach ($result['uma'] as &$uma) {
			    if (!isset($uma['old'])) continue;
			    foreach ($uma['old'] as &$past_value) {
			        $race_code = $past_value['race_code'];
			        if (isset($kako[$race_code])) {
			            $past_value['vs_count'] = $kako[$race_code];
			        }
			    }
				unset( $past_value );
			}
			unset( $uma );
			$this->bachu_joho = $result;
/* デバッグ用
			echo "<br><br>";
			foreach ($result['uma'] as $uma) {
				echo "馬番：" . $uma['umaban'] . "　" . $uma['bamei'] . "　　";
				echo implode(', ', $uma['kyakushitu_marks']);
				echo "<br>";
			}
*/
		}
		return true;
	}catch (Exception $e){ return false; }
	}

	// ========================================================
	// レース傾向のデータ取得
	// ymdb = yyyymmddBB
	// データは「race_keikou_joho」に格納
	// ========================================================
	public function loadRaceKeikouJoho( $ymdb, $reload=0 )
	{
	  if( $reload ) $this->race_keikou_joho = null;
	  if( strlen($ymdb) != 10 ) return false;
	  try{
		if( empty($this->race_keikou_joho) ){
			try{
				$url = $this->RaceKeikouRequesURL . "?date=" . $ymdb;
				$result = $this->loadata( $url );
				if( is_null($result) ) return false;
				$this->race_keikou_joho = $result;
			}catch (Exception $e) {
				return false;
			}
		}
		return true;
	  }catch (Exception $e){ return false; }
	}

	// ========================================================
	// 日別の結果・払戻データデータ取得
	// ymdb = yyyymmdd
	// データは「day_harai_joho」に格納
	// ========================================================
	public function loadDayHaraiJoho( $ymd, $reload=0 )
	{
		if( $reload ) $this->day_harai_joho = null;
		if( strlen($ymd) != 8 ) return false;
		try{
			if( empty($this->day_harai_joho) ){
				$url = $this->RaceDayHaraiRequesURL . "?date=" . $ymd;
				$result = $this->loadata( $url );
				if( is_null($result) ) return false;
				if( empty($result['keibajo']) ) return false;

				// 払戻のデータとして成立しないものを削除
				foreach ($result['keibajo'] as &$j_value) {
				    if (empty($j_value['race'])) continue;

				    foreach ($j_value['race'] as $race_key => &$r_value) {
				        // commitが5,6,7以外ならレースごと削除
				        if (!in_array((int)$r_value['commit'], [5, 6, 7], true)) {
						// テストコード
				        //if (!in_array((int)$r_value['commit'], [3, 4, 5, 6, 7], true)) {
				            unset($j_value['race'][$race_key]);
				            continue;
				        }

				        if (empty($r_value['haraimodoshi'])) continue;

				        foreach ($r_value['haraimodoshi'] as $hk => $h_value) {
				            $r_value['haraimodoshi'][$hk] = array_filter($h_value, function ($item) {
				                return !empty($item['ninki']);
				            });
				        }
				    }
/*
					// race自体が全部消えたら、keibajoからも削除する場合
				    if (empty($j_value['race'])) {
				        $j_value = null; // or unset($j_value); if numeric keys not needed
				    }
*/
				}
				$this->day_harai_joho = $result;
			}
			return true;
		}catch (Exception $e){
			 return false;
		}
	}

	// ========================================================
	// 現在日より5日前から30日後の間に出走予定の馬を取得
	// データは「day_harai_joho」に格納
	// ========================================================
	public function loadShutubaJoho( $reload=0 )
	{
		if( $reload ) $this->shutuba_joho = null;
		try{
			if( empty($this->shutuba_joho) ){
				$url = $this->ShutubaJohoRequesURL;
				$result = $this->loadata( $url );
				if( is_null($result) ) return false;
				$this->shutuba_joho = $result;
				return true;
			}
			return false;
		}catch (Exception $e){
			 return false;
		}
	}
	// ========================================================
	// 現在日より5日前から30日後間の騎乗情報を取得
	//
	// ========================================================
	public function loadKijyoJoho( $kishu_code )
	{
		try{
			$url = $this->KijyoJohoRequesURL . "?kishu=" . $kishu_code;
			$result = $this->loadata( $url );
			return $result;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// レースの全オッズデータ取得
	// データは「race_odds_joho」にも保持
	// ========================================================
	public function loadRaceOdds( $race_code, $reload=0 )
	{
		if( $reload ) $this->race_odds_joho = null;
		try{
			if( empty($this->race_odds_joho) ){
				$url = $this->RaceOddsJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->race_odds_joho = $result;
			}
			return $this->race_odds_joho;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// 過去10年・枠順のデータ取得
	// データは「kako10_wakuJun_joho」にも保持
	// ========================================================
	public function loadKako10Waku( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_wakuJun_joho = null;
		try{
			if( empty($this->kako10_waku_joho) ){
				$url = $this->Kako10WakuJunJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_wakuJun_joho = $result;
			}
			return $this->kako10_wakuJun_joho;
		}catch (Exception $e){
			 return null;
		}

	}
	// ========================================================
	// 過去10年・騎手のデータ取得
	// データは「kako10_kishu_joho」にも保持
	// ========================================================
	public function loadKako10Kishu( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_kishu_joho = null;
		try{
			if( empty($this->kako10_kishu_joho) ){
				$url = $this->Kako10KishuJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_kishu_joho = $result;
			}
			return $this->kako10_kishu_joho;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// 過去10年・種牡馬のデータ取得
	// データは「kako10_shuboba_joho」にも保持
	// ========================================================
	public function loadKako10Shuboba( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_shuboba_joho = null;
		try{
			if( empty($this->kako10_shuboba_joho) ){
				$url = $this->Kako10ShubobaJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_shuboba_joho = $result;
			}
			return $this->kako10_shuboba_joho;
		}catch (Exception $e){
			 return null;
		}
	}
	// ========================================================
	// 過去10年・母父のデータ取得
	// データは「kako10_hahachichi_joho」にも保持
	// ========================================================
	public function loadKako10Hahachichi( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_shuboba_joho = null;
		try{
			if( empty($this->kako10_hahachichi_joho) ){
				$url = $this->Kako10HahachichiJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_hahachichi_joho = $result;
			}
			return $this->kako10_hahachichi_joho;
		}catch (Exception $e){
			 return null;
		}
	}
	// ========================================================
	// 過去10年・前走のデータ取得
	// データは「kako10_zenso_joho」にも保持
	// ========================================================
	public function loadKako10Zenso( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_zenso_joho = null;
		try{
			if( empty($this->kako10_zenso_joho) ){
				$url = $this->Kako10ZensoJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_zenso_joho = $result;
			}
			return $this->kako10_zenso_joho;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// 過去10年・脚質のデータ取得
	// データは「kako10_kyakushitsu_joho」にも保持
	// ========================================================
	public function loadKako10Kyakusitu( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_kyakushitsu_joho = null;
		try{
			if( empty($this->kako10_kyakushitsu_joho) ){
				$url = $this->Kako10KyakushitsuJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_kyakushitsu_joho = $result;
			}
			return $this->kako10_kyakushitsu_joho;
		}catch (Exception $e){
			 return null;
		}
	}
	// ========================================================
	// 過去10年・人気のデータ取得
	// データは「kako10_ninki_joho」にも保持
	// ========================================================
	public function loadKako10Ninki( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_ninki_joho = null;
		try{
			if( empty($this->kako10_ninki_joho) ){
				$url = $this->Kako10NinkiJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_ninki_joho = $result;
			}
			return $this->kako10_ninki_joho;
		}catch (Exception $e){
			 return null;
		}
	}
	// ========================================================
	// 過去10年・斤量のデータ取得
	// データは「kako10_ninki_joho」にも保持
	// ========================================================
	public function loadKako10Kinryo( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_kinryo_joho = null;
		try{
			if( empty($this->kako10_kinryo_joho) ){
				$url = $this->Kako10KinryoJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_kinryo_joho = $result;
			}
			return $this->kako10_kinryo_joho;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// 過去10年・世代のデータ取得
	// データは「kako10_sedai_joho」にも保持
	// ========================================================
	public function loadKako10Sedai( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_sedai_joho = null;
		try{
			if( empty($this->kako10_sedai_joho) ){
				$url = $this->Kako10SedaiJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_sedai_joho = $result;
			}
			return $this->kako10_sedai_joho;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// 過去10年・東西のデータ取得
	// データは「kako10_tozai_joho」にも保持
	// ========================================================
	public function loadKako10Tozai( $race_code, $reload=0 )
	{
		if( $reload ) $this->kako10_tozai_joho = null;
		try{
			if( empty($this->kako10_tozai_joho) ){
				$url = $this->Kako10TozaiJohoRequesURL . "?racecode=" . $race_code;
				$result = $this->loadata( $url );
				if( is_null($result) ) return null;
				$this->kako10_tozai_joho = $result;
			}
			return $this->kako10_tozai_joho;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// 競走馬プロフィール取得
	// ========================================================
	public function loadKyosobaJoho( $ketto )
	{
		try{
			$url = $this->kyosobaJohoRequesURL . "?ketto=" . $ketto;
			$result = $this->loadata( $url );
			if( is_null($result) ) return null;
			if( empty($result['uma'])) return null;
			if( empty($result['uma']['ketto_toroku_bango'])) return null;
			return $result;
		}catch (Exception $e){
			 return null;
		}
	}
	// ========================================================
	// 騎手プロフィール取得
	// ========================================================
	public function loadKishuJoho( $kishu_code )
	{
		try{
			$url = $this->kishuJohoRequesURL . "?kishu=" . $kishu_code;
			$result = $this->loadata( $url );
			return $result;
		}catch (Exception $e){
			 return null;
		}
	}

	// ========================================================
	// WIN5のデータ取得
	// date = yyyymmdd  or yyyymm
	// ========================================================
	public function loadWin5Joho( $date )
	{
		try{
			$url = $this->win5JohoRequesURL . "?date=" . $date;
			$result = $this->loadata( $url );
			if( is_null($result) ) return null;
			krsort($result);		// 日付降順
			return $result;
		}catch (Exception $e){
			 return null;
		}
	}

	// --------------------------------------------------------
	//
	// --------------------------------------------------------
	public function getShutubaList( $reload=0)
	{
		if( !$this->loadShutubaJoho() ) return null;
		return $this->shutuba_joho;
	}
	public function getKijyoList( $kishu_code )
	{
		return $this->loadKijyoJoho( $kishu_code );
	}

	// --------------------------------------------------------
	// 日別の結果・払戻
	// 戻り：配列
	// --------------------------------------------------------
	public function getDayHarai( $ymd, $reload=0)
	{
		if( !$this->loadDayHaraiJoho( $ymd, $reload ) ) return null;
//		echo "<pre>" . json_encode($this->day_harai_joho , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "</pre>";
		return $this->day_harai_joho;
	}
	// --------------------------------------------------------
	// 指定日の週に含まれる日をリストアップする
	// 戻り：配列
	// --------------------------------------------------------
	public function getWeekDays($date=null)
	{
		$ts= null;
		if( !is_null($date) ) $ts =  strtotime($date);
		$dates = Race::getWeekDays($ts);

		$weekDays = array_fill_keys(
    		array_map(fn($date) => str_replace("-", "", $date), $dates),
    		true
		);
	    return $weekDays;
	}
	// --------------------------------------------------------
	// 【開催日程データ】
	// 指定月の開催情報一覧を取得する
	// 引数：ym = 年+月 (yyyymm)
	// --------------------------------------------------------
	public function getMonthKaisaiJoho( $ym, $reload=0 )
	{
		$result = $this->loadMonthKaisaiJoho($ym, $reload);
		return $result;
	}

	// 月の開催日を配列にする。
	public function getMonthKaisaiDays( $ym )
	{
		$date_list = [];
		$result = $this->loadMonthKaisaiJoho($ym, 1 );
		if( !empty( $result["dates"] ) ){
			$date_list = array_keys($result["dates"]);
		}
		$this->month_kaisai_days = $date_list;
		return $date_list;
	}

	public function __getLastestDay( $ym, $ymd )
	{
		$days = $this->getMonthKaisaiDays( $ym, 1);
		$_ymd = 0;
		foreach ($days as $key => $value) {
			if( $value <= $ymd ) $_ymd = $value;
		}
		return $_ymd;
	}
	public function getLatestKaisaiDays( $ymd )
	{
		$ym = substr( $ymd, 0, 6 );
		$_ymd = $this->__getLastestDay( $ym, $ymd );
		if( !$_ymd ){
			$date = DateTime::createFromFormat('Ym', $ym);
			$date->modify('-1 month');
			$ym = $date->format('Ym');
			$_ymd = $this->__getLastestDay( $ym, $ymd );
		}
		return $_ymd;
	}

	// --------------------------------------------------------
	// 【開催日レース一覧データ】
	// 開催一覧のデータから開催日情報を取得
	// 対象日に場所コードが存在しない場合、先頭の場所コードに変更
	// 引数：ymd = 年+月+日 (yyyymmdd)、from = 過去分、to = 未来分、param_jyo = 場所コード
	// --------------------------------------------------------
	public function getKaisaiList( $ymd=DEFAULT_YMD, $from=-3, $to=2, $param_jyo=0 )
	{
		if( !$this->loadKaisaiJoho( $ymd ) ){
			$this->kaisai_list = null;
		}
		if( is_null( $this->kaisai_joho) ) return null;
		if( empty($this->kaisai_list) ){
			// 情報が空ならば開催リストを作成する
			$fst_jyo = "";
			$fst_kaisaiCode = "";
			$target_jyo = "";
			$target_kaisaiCode = "";
			$_tozai = ['','','']; // 東・西・裏
			foreach ($this->kaisai_joho['keibajo'] as $key => $value) {
				$_tozai[$value['keibajo_category']-1] = '' . $key;
			}
			foreach ($this->kaisai_joho['dates'] as $key => $value) {
				if( $key >= $from && $key <= $to ){		//取得範囲を確認
					$k = $value['date'];
					$this->kaisai_list[$k]['date'] = $value['date'];
					$this->kaisai_list[$k]['date_text'] = $this->getMonthDayText( $value['date'] );

					foreach ($value['keibajo'] as $index => $jyo_value) {
						$jyo_code = substr( $jyo_value, 8, 2);		// 場コードの取り出し
						$this->kaisai_list[$k]['keibajo'][$jyo_value]['jyo_code'] = $jyo_code;
						$this->kaisai_list[$k]['keibajo'][$jyo_value]['jyo_text'] = $this->getJyoText($jyo_code);
						$this->kaisai_list[$k]['keibajo'][$jyo_value]['kaisai_code'] = $jyo_value;
						if( $key == 0 ) {			// 表示させる日のデータなら
							if( $param_jyo == $jyo_code ){
								$target_jyo = $jyo_code;
								$target_kaisaiCode = $jyo_value;
							}
						}
					}
					if( $key == 0 ) {
						$this->kaisai_list[$k]['tozai']  = $_tozai;
						foreach ($_tozai as  $_tozai_value) {
							if( $_tozai_value != '' ){
								$fst_kaisaiCode = $_tozai_value;
								break;
							}
						}
					}
				}
			}
			if( $target_jyo == "" ){	// 場所コードが無かった場合の処理（先頭の場所を入れる）
				$target_kaisaiCode = $fst_kaisaiCode;
				$target_jyo = substr( $fst_kaisaiCode, 8, 2 );
			}
			$this->current_jyo = $target_jyo;
			$this->current_kaisaiCode = $target_kaisaiCode;
		}
//		echo "<pre>" . json_encode($this->kaisai_list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
		return $this->kaisai_list;
	}

	// --------------------------------------------------------
	// 【開催日レース一覧データ】
	// 番組情報の取得
	// 引数：ymdb = 年+月+日 (yyyymmdd)
	// --------------------------------------------------------
	public function getBangumiJoho( $ymd, $reload=0 )
	{
		if (!isset($ymd) || $ymd === "") return false;
		if( $reload ) $this->bangumi_joho = null;
		if( $this->loadKaisaiJoho( $ymd, $reload ) ){
			if (empty($this->bangumi_joho) ) {
				if( empty($this->kaisai_joho['keibajo']) ) return false;
				$this->bangumi_joho = $this->kaisai_joho['keibajo'];
			}
		}
		return $this->bangumi_joho;
	}

	// --------------------------------------------------------
	// 「開催日レース一覧データ」を使用
	// 番組内の重賞レースのみ抽出（順番はグレード順）
	// ※先にgetBangumiJohoの呼び出しが必須
	// 引数：取得したい最大数、0は全て。
	// --------------------------------------------------------
	public function getJyushoList( $chois_num = 0 )
	{
		 if( empty($this->bangumi_joho) ) return null;
	     $jyusho_list = [];
	     foreach ($this->bangumi_joho as $key => $basho) {
	         foreach ($basho['race'] as $key => $race) {
	             if ( $race['grade'] != "") {
	                 $jyusho_list[$race['race_code']] = $race;
					 $jyusho_list[$race['race_code']]['grade_code'] = $this->getGradeCode($race['grade']);
					 $jyusho_list[$race['race_code']]['date'] = substr($race['race_code'],0,8);
					 $jyusho_list[$race['race_code']]['jyo_no'] = intval(substr($race['race_code'],8,2));
					 $jyusho_list[$race['race_code']]['jyo_code'] = substr($race['race_code'],8,2);
	             }
	         }
	     }
	     usort($jyusho_list, function ($a, $b)  {
	         return $this->getGradeCode($a['grade']) <=> $this->getGradeCode($b['grade']);
	     });
		 if( !$chois_num ) return( $jyusho_list );
		 return array_slice($jyusho_list, 0, $chois_num);
	}


	// --------------------------------------------------------
 	// 「馬柱データ」を使用
	// 引数：racecode = 年+月+日+場所+回+日目+レース番号 (yyyymmddBBKKNNRR)
	// --------------------------------------------------------
	public function getBachuJoho( $race_code, $reload=0 )
	{
		if( !$this->loadBachuJoho( $race_code, $reload ) ) return null;
		return $this->bachu_joho;
 	}

	// --------------------------------------------------------
 	// 【馬柱データ】 ※馬柱のレース情報の取得
	// 引数：racecode = 年+月+日+場所+回+日目+レース番号 (yyyymmddBBKKNNRR)
	// --------------------------------------------------------
	public function getBachuRaceJoho( $race_code, $reload=0 )
	{
		if( !$this->loadBachuJoho( $race_code, $reload ) ) return null;
		return $this->bachu_joho['race'];
 	}
	// --------------------------------------------------------
 	// 【馬柱データ】　※馬柱の馬情情報の取得
	// 引数：racecode = 年+月+日+場所+回+日目+レース番号 (yyyymmddBBKKNNRR)
	// --------------------------------------------------------
	public function getBachuUmaJoho( $race_code, $reload=0 )
	{
		if( !$this->loadBachuJoho( $race_code, $reload ) ) return null;
		return $this->bachu_joho['uma'];
	}

	// --------------------------------------------------------
 	// 【レース傾向データ】
	// レース傾向情報の取得
	// 引数：開催コード（ymdb）= 年+月+日+場所
	// --------------------------------------------------------
	public function getRaceKeikoJoho( $kaisai_code)
	{
		if( !$this->loadRaceKeikouJoho( $kaisai_code )) return null;
		return $this->race_keikou_joho;
	}

	// --------------------------------------------------------
	// 馬場状態のデータを取得
	// 引数：当週基準日（ymd）
	// --------------------------------------------------------
	public function getBabaJoho( $ymd )
	{
		$days = $this->getWeekDays( $ymd );
		$baba_data = [];
		$now = Date( 'Y/m/d H:i');
		foreach ($days as $d_key => $d ) {
			$result = Race::getKaisaiList($d_key);
			if( empty($result) ) continue;
			$result = json_decode(json_encode($result), true);
			foreach ( $result as $key => $value) {
				if( !empty( $value['work'] ) ){
					if( $value['work']['start_at'] > $now  ) continue;
					$baba_data[$value['place']]['jyo_text'] = $value['info']['keibajo'];
					$kc = substr( $value['kaisai_code'], 0, 8 );
					$baba_data[$value['place']]['datas'][$kc] = $value['work'];
					$baba_data[$value['place']]['datas'][$kc]['text'] = sprintf( "%d/%d(%s)", $value['info']['kaisai_tsuki'], $value['info']['kaisai_hi'], $value['info']['kaisai_yobi']);
					$baba_data[$value['place']]['datas'][$kc]['hikaku'] = 'even';
				}
			}
		}
		return $baba_data;
	}
	public function getWeekJyushoList( $chois_num = 0 )
	{
		$weekPickupJoho = [];
		$days = $this->getWeekDays();
		$kaisai_list = $this->loadKaisaiJoho( date('Ymd'), 1 );
		if( !empty($kaisai_list) ){
			$valid_dates = array_column($kaisai_list['dates'], 'date');
			$filtered_days = array_filter($days, function ($key) use ($valid_dates) {
			    return in_array((int)$key, $valid_dates, true);
			}, ARRAY_FILTER_USE_KEY);

			foreach ($filtered_days as $day => $value) {
				$bangumi = $this->getBangumiJoho( $day,1 );
				if( empty($bangumi) ) continue;
				$jyusho = $this->getJyushoList();
				foreach ($bangumi as $kc => $k_value) {
					$weekday_kind = '';
					if( $k_value['kaisai_yobi'] == '土' )  $weekday_kind = 'Sat';
					else if( $k_value['kaisai_yobi'] == '日' ) $weekday_kind = 'Sun';
					$weekPickupJoho['dates'][$day]['info'] = [
						'kaisai_nen' => $k_value['kaisai_nen'],
						'kaisai_tsuki' => $k_value['kaisai_tsuki'],
						'kaisai_hi' => $k_value['kaisai_hi'],
						'kaisai_yobi' => $k_value['kaisai_yobi'],
						'weekday_kind' => $weekday_kind,
						'kaisai_yobi_code' => $k_value['kaisai_yobi_code'],
					];
					$weekPickupJoho['dates'][$day]['keibajo'][$k_value['keibajo_code']] = [
						'kaisai_code' => $kc,
						'kaisaimei' => $k_value['kaisaimei'],
						'keibajo' => $k_value['keibajo'],
						'keibajo_category' => $k_value['keibajo_category']
					];
				}
				$weekPickupJoho['jyusho'] = array_merge($weekPickupJoho['jyusho'] ?? [], $jyusho);
			}
			if( !empty($weekPickupJoho['jyusho'] ) ){
				usort($weekPickupJoho['jyusho'], function($a, $b) {
					// 第1ソート：grade_code 昇順（数値）
				    if ($a['grade_code'] !== $b['grade_code']) {
				        return $a['grade_code'] - $b['grade_code'];
				    }

				    // 第2ソート：jyo_no 昇順（数値）
				    if ($a['jyo_no'] !== $b['jyo_no']) {
				        return $a['jyo_no'] - $b['jyo_no'];
				    }

				    // 第3ソート：date 降順（文字列）
				    return strcmp($b['date'], $a['date']);
				});
			}
			//echo "<pre>" . json_encode($weekPickupJoho , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "</pre>";
		}
		return $weekPickupJoho;
	}

	// --------------------------------------------------------
	// レースコードからレースの情報を取得する
	// 引数：レースコード
	// --------------------------------------------------------
	public function getRaceInfo( $race_code )
	{
		$result = $this->getBachuJoho( $race_code );
		$result_data = [];
		if( !empty( $result )){
			$ymd = substr( $result['race']['race_code'], 0, 8 );
			$jyo = '' . substr( $result['race']['race_code'], 8, 2 );

			$result_data = [
		        'race_bango' => $result['race']['race_bango'],
		        'kyosomei' => $result['race']['kyosomei'],
		        'grade' => $result['race']['grade'],
		        'win5' => $result['race']['win5'],
		        'kaisai' => $this->getNengappiText($ymd),
		        'kaisai_basyo' => $this->getJyoText( $jyo )
		    ];
		}
//		echo "<pre>" . json_encode($result_data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "</pre>";
		return $result_data;
	}

//Class終端
}
