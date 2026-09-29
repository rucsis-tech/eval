<?php
namespace classes;
use Exception;

class CryptClass {

    /* @var string 暗号化キー */
    static public $crypt_key = 'crypt_key';

    /* @var object 初期化ベクトル */
    static private $initial_vector = null;

    const DEFAULT_METHOD = 'aes-256-cbc';

    /**
     * 初期化ベクトルを設定
     *
     * @param string $initial_vector 初期化ベクトル (bin2hex()したもの)
     */
    static public function setInitialVector($initial_vector) {
        self::$initial_vector = hex2bin($initial_vector);
    }

    /**
     * @return string
     * @throws Exception
     */

    static public function getCryptMethod() {
        $methods = openssl_get_cipher_methods();
        if (in_array(self::DEFAULT_METHOD, $methods, true)) {
            return self::DEFAULT_METHOD;
        }
        throw new Exception();
    }

    /**
     * 初期化ベクトルを取得
     *
     * 初期化ベクトルは内部ではバイナリで扱うが、DB保存など外部での取り回しを意識してbin2hex()で16進数表記したものを返す
     * 外部から初期化ベクトルが与えられていない場合、ここでランダムな初期化ベクトルを生成して返す
     *
     * @return string 初期化ベクトル (hex2bin()したもの)
     */
    static public function getInitialVector() {
        if (is_null(self::$initial_vector)) {
            self::$initial_vector = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::getCryptMethod()));
        }
        return bin2hex(self::$initial_vector);
    }

    /**
     * 暗号化処理
     *
     * 暗号化したデータはDB保存など外部での取り回しを意識してbin2hex()で16進数表記したものを返す
     *
     * @param string $plain_text 暗号化したい文字列
     * @return string 暗号化されたデータ(bin2hex()済み)
     */
    static public function encrypt($plain_text, $key = null) {

        $input = self::pkcs5_padding($plain_text);
        $key = $key ?: self::$crypt_key;
        return self::getInitialVector() . '-' . bin2hex(
                openssl_encrypt(
                    $input,
                    self::getCryptMethod(),
                    $key,
                    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                    hex2bin(self::getInitialVector())
                )
            );
    }

    /**
     * 復号処理
     * *
     * * @param string $encrypted_text 復号したいデータ (16進数表記されたもの)
     * * @return string 復号された文字列
     * @return false|string
     */
    static public function decrypt($encrypted_text, $key = null): false|string {
        try {
            $key = $key ?: self::$crypt_key;
            list($vector, $enc) = explode('-', $encrypted_text);
            return self::pkcs5_suppress(
                openssl_decrypt(
                    hex2bin($enc),
                    self::getCryptMethod(),
                    $key,
                    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                    hex2bin($vector)
                )
            );
        } catch (Exception $e) {
            return '';
        }

    }

    /**
     * パディング処理
     *
     * 暗号化方式で指定されているブロックサイズに合わせて文字列を埋める
     *
     * @param string $text 対象文字列
     * @return string パディング済みの文字列
     */
    static private function pkcs5_padding($text) {
        $block_size = self::openssl_cipher_block_length(self::getCryptMethod());
        $pad = $block_size - (strlen($text) % $block_size);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * サプレス処理
     *
     * 暗号化の際にブロックサイズ調整で埋められた文字を取り除く
     *
     * @param string $text 対象文字列
     * @return string ブロックサイズ調整文字を取り除いた文字列
     */
    static private function pkcs5_suppress($text) {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, strpos($text, chr($pad)));
    }

    /**
     * 暗号化方式が指定するブロック長を算出
     *
     * @param string $cipher 暗号化方式
     * @return int 暗号化方式が指定しているブロック長
     */
    static function openssl_cipher_block_length($cipher) {
        $ivSize = @openssl_cipher_iv_length($cipher);

        // サポートしていない暗号化方式だった
        if ($ivSize === false) {
            return false;
        }

        $iv = str_repeat("a", $ivSize);

        // 1バイトから1024バイトまで順に暗号化可能なブロック長を試していく
        for ($size = 1; $size < 1024; $size++) {
            $output = openssl_encrypt(
                str_repeat("a", $size),
                $cipher,
                "a",
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $iv
            );

            if ($output !== false) {
                return $size;
            }
        }

        return false;
    }

    static function make_openssl_blowfish_key($key) {
        if ("$key" === '')
            return $key;
        $len = (16 + 2) * 4;
        while (strlen($key) < $len) {
            $key .= $key;
        }
        $key = substr($key, 0, $len);
        return $key;
    }


    /**
     * GUIDっぽいもの生成 戻り値は 40 文字の 16 進数
     * @return string
     */
    public static function createGUID() {
        return sha1(uniqid(mt_rand(), true));
    }
}