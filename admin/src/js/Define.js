export const DEBUG = true

/*********************************************************
 * API群
 *********************************************************/


export const APP_NAME = 'eval'

// dev
// const API_BASE_URL = 'http://153.122.80.131/crm/api/'

// ログイン
export const API_LOGIN = 'user/login.php'
export const API_SESSION = 'user/session.php'

//------------------------------------------------

// 会員検索
export const API_SEARCH = 'member/search.php'
// 会員詳細
export const API_M_DETAIL = 'member/detail.php'
// 会員修正
export const API_MEMBER_MOD = 'member/modify.php'
// 退会処理
export const API_MEMBER_LEAVE = 'member/leave.php'

export const API_MEMBER_QUBE = 'member/qube.php'
// 会員絞り込み
export const API_NARROW = 'member/narrow.php'
//ポイント付与
export const API_GIVE_POINTS = 'member/give_points.php'
export const API_GIVE_SCORE = 'member/give_score.php'

// 絞り込み用データ
export const API_M_DATA = 'member/data.php'
// -------------------------------------------


// プリセット取得
export const API_PRESET = 'preset/preset.php'
// チェック名
export const API_CHECK_NAMES = 'preset/check_names.php'
// チェックフラグ設定
export const API_MODIFY_CHECKS = 'preset/modify_checks.php'

// -------------------------------------------

// 会員一覧
export const API_MEMBER_LIST = 'member/list.php'

// -------------------------------------------

// 商品
export const API_PAYMENT_ITEM = 'shop/payment_item.php'
export const API_ITEM_HORSE = 'item/horse.php'

export const API_RACE_LIST = 'race/racelist.php'    // レース一覧
export const API_RACE_INFO = 'race/shussoba.php'    // レース詳細・出走馬
export const API_POPULAR_LIST = 'race/popular.php'

// -------------------------------------------

// 承認用リスト取得
export const API_APPROVAL = 'sales/approval.php'
export const API_AUCTION = 'sales/auction.php'
export const API_AUCTION_EXPORT = 'export/auction_info.php'

// 購入商品取得
export const API_SALES_ITEM = 'shop/payment_item.php'
// 売上取得

export const API_SALES = 'sales/sales.php'
export const API_SALES_UPDATE_GMO = 'sales/update_gmo.php'
export const API_KPI = 'sales/kpi.php'

export const API_SALES_SUBSCRIPTION = 'sales/subscription.php'

export const API_POINT_PURCHASED = 'point/purchased.php'

export const API_POINT_SPEND = 'point/spend.php'

export const API_POINT_INCREMENTAL = 'point/incremental.php'

// -------------------------------------------

// 媒体取得・追加・修正・削除
export const API_MEDIA = 'advertise/media.php'
// 広告コード取得・追加・修正・削除
export const API_ADVERTISE = 'advertise/advertise.php'
// 広告集計
export const API_AD_AGGREGATE = 'advertise/aggregate.php'

// -------------------------------------------

// メール取得・追加・修正・削除
export const API_MAIL = 'distribute/mail_body.php'

// 配信取得・追加・修正・削除
export const API_DISTRIBUTE = 'distribute/distribute.php'
// バナー登録
export const API_BANNER_IMAGE = 'distribute/banner.php'
// TOPIC登録
export const API_TOPIC = 'distribute/topic.php'


// -------------------------------------------

// 〒→住所取得
export const API_ZIP_TO_ADDRESS = 'z_to_a.php'


// ライセンス状況
export const API_LICENSE = 'license.php'

// 差出人取得・追加・修正・削除
export const API_FROM = 'from.php'


export const API_USER_LIST = 'user/list.php' // ユーザー一覧取得
export const API_USER_ITEM = 'user/item.php' // ユーザー詳細取得

// パスワードリセット
export const API_USER_RESET = 'user/reset.php'

// ユーザー利用履歴
export const API_USER_HISTORY = 'user/history.php'
// IP取得
export const API_GET_IP = 'get_ip.php'
// アンケート取得
export const API_QUESTIONNAIRE = 'questionnaire.php'

/*********************************************************
 * 配列定義
 *********************************************************/
export const weekDayList =
    ['日', '月', '火', '水', '木', '金', '土']

export const paymentCategoryList =
    ['', 'コイン', 'チケット', 'コイン/チケット', '月額']

export const purchaseStateList =
    ['購入', '利用', '付与', '返還', '', '失効', '', '変更(ニ)', '変更(名)', '変更(住)', '変更(電)', '回答', 'キャンペーン']

export const ticketHisStateList =
    ['購入', '利用', '付与', '自動', '失効']

export const paymentCorpList =
    ['〇〇銀行', 'クレジット', 'xxxxx', 'xxxxx', 'xxxxx', 'xxxxx', 'xxxxx']

export const gachaStateList =
    ['発行', '使用']

export const licenseTypeList =
    ['', 'コイン', 'チケット', '', 'ガチャ']

export const itemCategoryList =
    ['', '的中重視', '配当重視']

// 未使用
export const ticketCategoryList =
    ['', '安定型', '一撃型']

export const prefList = [
    {value: '1', label: '北海道'},
    {value: '2', label: '青森県'},
    {value: '3', label: '岩手県'},
    {value: '4', label: '宮城県'},
    {value: '5', label: '秋田県'},
    {value: '6', label: '山形県'},
    {value: '7', label: '福島県'},
    {value: '8', label: '茨城県'},
    {value: '9', label: '栃木県'},
    {value: '10', label: '群馬県'},
    {value: '11', label: '埼玉県'},
    {value: '12', label: '千葉県'},
    {value: '13', label: '東京都'},
    {value: '14', label: '神奈川県'},
    {value: '15', label: '新潟県'},
    {value: '16', label: '富山県'},
    {value: '17', label: '石川県'},
    {value: '18', label: '福井県'},
    {value: '19', label: '山梨県'},
    {value: '20', label: '長野県'},
    {value: '21', label: '岐阜県'},
    {value: '22', label: '静岡県'},
    {value: '23', label: '愛知県'},
    {value: '24', label: '三重県'},
    {value: '25', label: '滋賀県'},
    {value: '26', label: '京都府'},
    {value: '27', label: '大阪府'},
    {value: '28', label: '兵庫県'},
    {value: '29', label: '奈良県'},
    {value: '30', label: '和歌山県'},
    {value: '31', label: '鳥取県'},
    {value: '32', label: '島根県'},
    {value: '33', label: '岡山県'},
    {value: '34', label: '広島県'},
    {value: '35', label: '山口県'},
    {value: '36', label: '徳島県'},
    {value: '37', label: '香川県'},
    {value: '38', label: '愛媛県'},
    {value: '39', label: '高知県'},
    {value: '40', label: '福岡県'},
    {value: '41', label: '佐賀県'},
    {value: '42', label: '長崎県'},
    {value: '43', label: '熊本県'},
    {value: '44', label: '大分県'},
    {value: '45', label: '宮崎県'},
    {value: '46', label: '鹿児島県'},
    {value: '47', label: '沖縄県'}
]
