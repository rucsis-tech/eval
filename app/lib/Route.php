<?php

class Route {

    /**
     * 適切な　src を include する
     * @param $uri
     * @return bool
     */
    public static function include($uri): bool {

        $url_query = explode('?', $uri);
        $request_path = $url_query[0] ?? '';
        $request_query = $url_query[1] ?? '';

        // APIサーバでは /api ディレクトリ以外は通さない
        if (App::isAPI()) {
            if (!str_starts_with($request_path, '/api/')) {
                $request_path = '/no_api';
            }
        }

        // 相対パスあったら殺す
        $request_path = str_replace('../', '', $request_path);

        // include src の root
        $src_dir = APP_ROOT . "src";

        // ディレクトリが指定されているので index　とする
        if (is_dir($src_dir . $request_path)) $request_path .= '/index';
        if (str_ends_with($request_path, '/')) $request_path .= 'index';

        // todo 個別特殊ルールがあれば処置

        if (str_ends_with($request_path, '.js')) {

            if (!file_exists($src_dir . $request_path)) {
                // 存在しない
                self::outputNoFile($uri);
                return false;
            }

            // jsファイル
            header('Content-Type: text/javascript;');
            echo file_get_contents($src_dir . $request_path);
            return true;

        } else if (str_contains($request_path, '.src.vue')) {
            // vueファイルJS変換
            self::outputVue($src_dir . $request_path);
            return true;

        }

        if (!str_contains($request_path, '.php')) {
            // PHP拡張子がなければ追加
            $request_path .= '.php';
        }

        if (!file_exists($src_dir . $request_path)) {
            // 存在しない
            self::outputNoFile($uri);
            return false;
        } else {
            // PHP include
            // 存在する
            require $src_dir . $request_path;
            return true;
        }

    }

    private static function outputVue(string $request_path): void {
        header('Content-Type: text/javascript;');
        $src = file_get_contents($request_path);
        $script = self::getTagContent($src, 'script');
        $template = self::getTagContent($src, 'template');
        $style = self::getTagContent($src, 'style');
        $script = str_replace(
            "export default {",
            "export default {\ntemplate: `$template`,\nstyle: `$style`,\n",
            $script);

        $script .= "let styleTag = document.createElement('style');styleTag.textContent = `"
            . $style
            . "`;document.head.appendChild(styleTag);";

        echo $script;

    }

    private static function getTagContent($src, $tagName): string {
        $tag = "<$tagName>";
        $start = strpos($src, $tag);
        if ($start === false) return "";
        $start += strlen($tagName) + 2;

        $end = strrpos($src, "</$tagName>");
        return substr($src, $start, $end - $start);
    }

    private static function outputNoFile($uri): string {
        header("HTTP/1.1 404 Not Found");
        //echo "<html lang=\"ja\"><h3>404 Not Found</h3>$uri</html>";
        exit;
    }

}