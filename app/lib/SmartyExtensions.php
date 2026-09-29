<?php

use Smarty\Smarty;

class SmartyExtensions {

    static public function setExtensions($Smarty) {
        $Smarty->registerPlugin(Smarty::PLUGIN_FUNCTION, 'setValues', 'SmartyExtensions::setValues');
        $Smarty->registerPlugin(Smarty::PLUGIN_FUNCTION, 'phpinfo', 'SmartyExtensions::phpinfo');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'br', 'SmartyExtensions::br');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'config', 'SmartyExtensions::config');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'dump', 'SmartyExtensions::dump');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'json', 'SmartyExtensions::json');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'cf', 'SmartyExtensions::cf');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 's3', 'SmartyExtensions::s3');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'uniq', 'SmartyExtensions::uniq');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'abs', 'SmartyExtensions::abs');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'filetime', 'SmartyExtensions::filetime');
        $Smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'pad_left', 'SmartyExtensions::pad_left');
    }

    static public function smarty_tag_eightball($params, \Smarty\Template $template): string {
        $answers = [
            'はい',
            'いいえ',
            'わかりません',
            '可能性は低い',
            '今は答えられません',
            '実はもう実現しているかも……'
        ];

        $result = array_rand($answers);
        return $answers[$result];
    }

    /**
     * カスタム関数
     * @param $params
     * @param \Smarty\Template $template
     * @return string
     * @throws Exception
     */
    static public function setValues($params, \Smarty\Template $template): string {
        foreach ($params as $key => $value) {
            $template->assign($key, $value);
        }
        return '';
    }

    static public function phpinfo($params, \Smarty\Template $template): string {
        ob_start();
        phpinfo();
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

    /**
     * モディファイア
     * @param $key
     * @param $default
     * @return string
     * @throws Exception
     */

    static public function config($key, $default = null): string {
        return config($key, $default);

    }

    /**
     * 変数をダンプする
     * @param $val
     * @return string
     */

    static public function dump($val, $level = 0): string {
        $top = ($level == 0) ? '<div class="result_bg">' : '';
        $end = ($level == 0) ? '</div>' : '';

        $level++;
        if ($level > 10) return "...";
        if ($val === NULL) return "{$top}NULL{$end}";

        if (is_array($val)) {
            $top .= "";
            $allow = "=>";
        }
        if (is_object($val)) {
            $top .= "Object";
            $allow = "->";
            $val = json_decode(json_encode($val), true);
        }

        if (is_array($val)) {
            $result = '';
            foreach ($val as $key => $item) {
                if (is_array($item) || is_object($item)) {
                    // $result .= "[{$key}] {$allow} {<div>" . self::dump($item, $level) . "</div>}<br>";
                    $result .= "{<div>" . self::dump($item, $level) . "</div>}<br>";
                } else {
                    if ($item === NULL) {
                        $item = "NULL";
                    } else if (is_string($item)) {
                        $item = "{$item}";
                    } else {
                        $item = (string)$item;
                    }
                    $item = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
                    // $result .= "<div>[$key] {$allow} $item</div>";
                    $result .= "<div>$item</div>";
                }
            }
            $rem = ($level == 1) ? 0 : 20;
            return "{$top}<div class='result_text'>$result</div>{$end}";

        } else {
            return "{$top}{$val}{$end}";
        }

    }

    static public function json($val): string {
        return json_encode($val, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 本文があったら改行する
     * @param $value
     * @return string
     */

    static public function br($value): string {
        if (!empty($value)) return "$value<br>";
        else return "";
    }

    /**
     * 環境に合わせたS3パスを出力
     * @param $value
     * @return string
     */
    static public function cf($value): string {
        if (empty($value)) return "/images/common/icon_user200.png";
        // 既にFQDN
        if (str_starts_with($value, 'http')) return $value;

        $env = AWSS3::getEnvDir();
        return AWSS3::$CLOUDFRONT_URL . "{$env}{$value}";
    }

    static public function s3($value): string {
        if (empty($value)) return "/images/common/icon_user200.png";
        // 既にFQDN
        if (str_starts_with($value, 'http')) return $value;

        $env = AWSS3::getEnvDir();
        return AWSS3::$S3_URL . "{$env}{$value}";
    }

    /**
     * ABS関数
     * @param $value
     * @return string
     */
    static public function abs($value): string {
        if (empty($value)) return '';
        return abs($value);
    }

    /**
     * 左を空白で埋める
     * @param $value
     * @param $num
     * @return string
     */
    static public function pad_left($value, $num): string {
        return str_pad($value, $num, " ", STR_PAD_LEFT);
    }

    static public function uniq($value): string {
        $uniq = substr(hash('sha256', uniqid(mt_rand(), true)), 0, 10);
        if (str_contains($value, '?')) return "{$value}&uniq=$uniq";
        return "{$value}?uniq=$uniq";
    }

    static public function filetime($value): string {
        $path = APP_ROOT . 'htdocs' . $value;
        if (file_exists($path)) {
            $filetime = filemtime($path);
        } else {
            $filetime = filemtime(str_replace('/htdocs/', '/src/', $path));
        }
        return $value . (str_contains($value, '?') ? '&' : '?') . $filetime;
    }


}