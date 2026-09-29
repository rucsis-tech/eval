<?php

use classes\AWSClass;

class AWSS3 {

    private static $S3Instance;        // s3インスタンス
    private static $bucket = 'winsight';
    // S3
    public static $S3_URL = 'https://winsight.s3-ap-northeast-1.amazonaws.com/';
    public static $CLOUDFRONT_URL = 'https://d1yfn3shv1fajd.cloudfront.net/';

    static
    public function getEnvDir() {
        if (App::isLocal()) {
            return 'local25/';
        } else if (App::isDevelop()) {
            return 'develop25/';
        }
        return 'release/';
    }

    /**
     * S3Clientインスタンスを返す
     * @return \Aws\S3\S3Client
     */
    static private function S3() {
        if (empty(self::$S3Instance)) {
            self::$S3Instance = AWSClass::getS3Instance();
        }
        return self::$S3Instance;
    }

    /**
     * @param null $path
     */
    public static function getS3URL($path = ''): string {
        $dir = self::getEnvDir();
        $url = self::$S3_URL . $dir;
        return $url . $path;
    }
    public static function getCFURL($path = ''): string {
        $dir = self::getEnvDir();
        $url = self::$CLOUDFRONT_URL . $dir;
        return $url . $path;
    }

    /**
     * S3ファイル一覧を取得する
     * @param string $key
     * @return array
     */
    static
    public function getS3List($prefix = '') {
        $list = self::S3()->listObjects([
            'Bucket' => self::$bucket,
            'Prefix' => $prefix
        ]);

        $list = $list->get('Contents');
        if (empty($list)) {
            return [];
        }

        // amazon APIの結果を表示がしやすいようにフォーマット
        foreach ($list as $key => $val) {

            $data = [];
            $data['file_path'] = $val['Key'];
            $data['file'] = '';
            $data['updated'] = date('Y/m/d H:i', strtotime($val['LastModified']));
            $data['size'] = round($val['Size'] / 1000) . 'KB';
            $data['s3_path'] = self::$S3_URL . $val['Key'];

            preg_match('/([^\/]*)\.[^\.]*$/', $val['Key'], $matches);
            if (!empty($matches[1])) {
                $data['file'] = $matches[1];
            }
            $result[$key] = $data;
        }

        $result = array_values($result);

        return $result;
    }

    /**
     * S3オブジェクトを取得する
     *
     * @param string $s3path ファイル名
     * @return \Aws\Result|bool 取得したファイル、存在しない場合はfalse
     *
     **/

    static
    public function getS3($s3path = '') {
        if (empty($s3path)) {
            return false;
        }

        try {
            $item = self::S3()->getObject([
                'Bucket' => self::$bucket,
                'Key' => $s3path,
            ]);
        } catch (\Aws\S3\Exception\NoSuchKeyException $e) {
            // ファイルがない場合
            return false;
        }

        return $item;
    }


    /**
     * S3オブジェクトを削除する
     * @param string $s3path ファイル名
     * @return \Aws\Result|bool 削除後の情報、失敗した場合はfale
     */
    static
    public function deleteS3($s3path = '') {

        if (empty($s3path)) return false;

        try {

            $delete = self::S3()->deleteObject(array(
                'Bucket' => self::$bucket,
                'Key' => self::getEnvDir() . $s3path,
            ));

        } catch (\Aws\S3\Exception\S3Exception $e) {
            // ファイルがない場合
            return false;
        }

        return $delete;
    }

    /**
     * S3にデータをアップロードする
     */
    static
    public function putS3($s3path, $body, $ContentType = '', $acl = '') {
        $param = [
            'Bucket' => self::$bucket,
            'Key' => self::getEnvDir() . "$s3path",
            'Body' => $body,
        ];
        if ($ContentType) {
            $param['ContentType'] = $ContentType;
        }
        if ($acl) {
            $param['ACL'] = $acl;
        }
        return self::S3()->putObject($param);
    }

    /**
     * S3にローカルファイルをアップロードする
     * @param $s3path
     * @param $filepath
     * @param string $ContentType
     * @param string $acl
     * @return \Aws\Result|bool
     */

    static
    public function putFileToS3($s3path, $filepath, $ContentType = '', $acl = ''): \Aws\Result|bool {
        if (!strlen($s3path) || !strlen($filepath)) {
            return false;
        }
        // s3に保存する
        $param = [
            'Bucket' => self::$bucket,
            'Key' => self::getEnvDir() . $s3path,
            'SourceFile' => $filepath,
        ];
        if ($ContentType) {
            $param['ContentType'] = $ContentType;
        }
        if ($acl) {
            $param['ACL'] = $acl;
        }
        $item = self::S3()->putObject($param);

        return $item;
    }
}