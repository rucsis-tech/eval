<?php

/**
 * @property int $id
 * @property int $uid 認証ID
 * @property string $file_name ファイル名
 * @property string $created_at 作成日時
 */

class MemberIcon extends Model {

    protected static string $tableName = 'image';


    /**
     * ICON画像のURLパスを取得
     * @param $uid
     * @return string
     * @throws Exception
     */
    public static function getImagePath($uid): string {

        $MemberIcon = self::cast(self::where('uid', $uid)
            ->orderBy('id', 'desc')
            ->first());

        if ($MemberIcon) {
            return AWSS3::getS3URL('icon/' . $MemberIcon->file_name);
        } else {
            return BASE_URL . 'images/my_icon.png';
        }

    }

    static function resizeUploadImage($tmpName, $dir, $filename, $destWidth, $destHeight) {

        //　----------------------------------------
        // 対応ファイル形式で読み込み

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);

        if ($mime == 'image/jpeg' || $mime == 'image/pjpeg') {
            $ext = '.jpg';
            $image1 = imagecreatefromjpeg($tmpName);
        } elseif
        ($mime == 'image/png' || $mime == 'image/x-png') {
            $ext = '.png';
            $image1 = imagecreatefrompng($tmpName);
        } elseif
        ($mime == 'image/gif') {
            $ext = '.gif';
            $image1 = imagecreatefromgif($tmpName);
        } else {
            return false;
        }

        list($srcWidth, $srcHeight) = getimagesize($tmpName);
        //$srcLength = ($srcWidth > $srcHeight) ? $srcHeight : $srcWidth;

        $image2 = imagecreatetruecolor($destWidth, $destHeight);

        if ($ext == '.gif') {
            $transparent1 = imagecolortransparent($image1);
            if ($transparent1 >= 0) {
                $index = imagecolorsforindex($image1, $transparent1);
                $transparent2 = imagecolorallocate($image2, $index['red'], $index['green'], $index['blue']);
                imagefill($image2, 0, 0, $transparent2);
                imagecolortransparent($image2, $transparent2);
            }
        } elseif ($ext == '.png') {
            imagealphablending($image2, false);
            $transparent = imagecolorallocatealpha($image2, 0, 0, 0, 127);
            imagefill($image2, 0, 0, $transparent);
            imagesavealpha($image2, true);
        }

        //$srcX = ($srcWidth - $srcLength) / 2;
        //$srcY = ($srcHeight - $srcLength) / 2;

        imagecopyresampled($image2, $image1, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

        //　----------------------------------------
        // 保存
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $saveTo = rtrim($dir, '/\\') . '/' . $filename . ".png";
        imagepng($image2, $saveTo);


        //　----------------------------------------
        // 破棄
        imagedestroy($image1);
        imagedestroy($image2);

        return $saveTo;
    }


}