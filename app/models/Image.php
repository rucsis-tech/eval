<?php

class Image extends Model {

    protected static string $tableName = 'image';
    const CATEGORY_BANNER = 'banner';
    const CATEGORY_WP_POSTS = 'wp_posts';
    const CATEGORY_WP_EYECATCH = 'wp_eyecatch';
    const CATEGORY_AUTHOR = 'author';
    const CATEGORY_AUTHOR_HEADER = 'author_header';
    const CATEGORY_WEEKLY_SERIES = 'wk_series';
    const CATEGORY_WEEKLY_THUMBNAIL = 'wk_thumbnail';
    const CATEGORY_RECOMMEND = 'recommend';
    const CATEGORY_ICON = 'icon';
    const CLOUDFRONT = 'https://d1yfn3shv1fajd.cloudfront.net/';

    /**
     * 画像をS3へ保存しimage レコードを得る
     * @param $category
     * @param $rid
     * @param $path
     * @return Image|null
     * @throws Exception
     */

    static
    public function transcript($category, $rid, $path, $origin = null) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $type = $ext;
        if ($ext == 'jpg') $type = 'jpeg';
        if ($ext == 'jpeg') $ext = 'jpg';

        $origin = $origin ?: $path;
        $data = ['category' => $category, 'rid' => $rid, 'origin' => $origin, 'updated_at' => self::date()];
        $ExistsQuery = self::where('category', $category);
        if ($rid) {
            $ExistsQuery->where('rid', $rid);
        } else {
            $ExistsQuery->where('origin', $origin);
        }
        $Exists = $ExistsQuery->first();
        if ($Exists) {
            // 既存上書き
            $id = $Exists->id;
            list($s3_path, $hash) = explode('?', $Exists->src);
        } else {
            // 新規挿入
            $id = self::insert($data);
            $filename = substr(md5($id), 0, 3) . uniqid() . '.' . $ext;
            $s3_path = $category . '/' . sprintf("%03d", intval($id / 1000)) . '/' . $filename;
        }
        // HASH更新
        self::where('id', $id)->update(['src' => self::getUpdateSrc($s3_path)]);

        //S3書き込み
        $picture = file_get_contents($path);
        $ContentType = "image/{$type}";
        AWSS3::putS3($s3_path, $picture, $ContentType);

        return self::first($id);
    }

    static private function getUpdateSrc($src) {
        if (str_contains($src, '?')) {
            $src = explode('?', $src)[0];
        }
        return $src . '?' . uniqid();
    }

    static
    public function getImage($array): static {

        $Query = self::query();
        foreach ($array as $key => $value) {
            $Query->where($key, $value);
        }

        return $Query->first();

    }

    static
    public function setImage($category, $rid, $origin) {
        Image::transcript($category, $rid, $origin);
    }


}