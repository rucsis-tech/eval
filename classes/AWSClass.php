<?php

namespace classes;

use Aws\Ec2\Ec2Client;
use Aws\S3\S3Client;
use Exception;

class AWSClass {

    /**
     * EC2情報取得用インスタンス
     * @return Ec2Client
     * @throws Exception
     */
    public static function getEC2Instance() {
        return new Ec2Client([
            'region' => 'ap-northeast-1',
            'version' => '2016-11-15',
            'credentials' => [
                'key' => config('AWS_KEY'),
                'secret' => config('AWS_SECRET'),
            ],
        ]);
    }

    public static function getS3Instance() {
        return new S3Client([
            'region' => 'ap-northeast-1',
            'version' => 'latest',
            'credentials' => [
                'key' => config('AWS_KEY'),
                'secret' => config('AWS_SECRET'),
            ],
        ]);
    }

    /**
     * 指定名のEC２インスタンスの情報取得
     * @return array
     * @throws Exception
     */
    public static function getEc2Clients($filters): array {
        $list = [];
        $ec2Client = AWSClass::getEC2Instance();

        // フィルタ整理
        if (is_string($filters)) $filters = [$filters];
        foreach ($filters as $key => $filter) {
            if (!str_contains($filter, '/')) $filters[$key] = "/^{$filter}$/"; // 正規表現に
        }

        $result = $ec2Client->describeInstances();
        foreach ($result['Reservations'] as $Reservation) {
            $Instance = $Reservation['Instances'][0];
            // if ($Instance['State']['Name'] !== 'running') continue; // 動いてないのは除外

            // Nameタグ取得
            $tagName = '';
            foreach ($Instance['Tags'] as $tag) {
                if ($tag['Key'] == 'Name') {
                    $tagName = $tag['Value'];
                    break;
                };
            }
            if (empty($tagName)) continue;

            // フィルタ選別
            $InstanceId = '';
            foreach ($filters as $filter) {
                if (preg_match($filter, $tagName)) {
                    $InstanceId = $Instance['InstanceId'] ?? null;
                    break;
                }
            }
            if (empty($InstanceId)) continue;

            // 対象保存
            $list[$InstanceId] = [
                'Name' => $tagName,
                'Instance' => $Instance
            ];
        }

        return $list;
    }




    /**
     * 指定名のEC２インスタンスのPrivateIP取得
     * @return array
     * @throws Exception
     */

    public static function getAWSPrivateIPS($filters): array {
        $ec2s = self::getEc2Clients($filters);
        $privateIp = [];
        foreach ($ec2s as $ec2) {
            $Instance = $ec2['Instance'];
            if ($Instance['State']['Name'] !== 'running') continue; // 動いてないのは除外

            // 対象保存
            $ip = $Instance['PrivateIpAddress'] ?? null;
            if ($ip) $privateIp[$ec2['Name'] . "." . $ip] = $ip;
        }

        return $privateIp;
    }

    /**
     * セキュリティグループからIPアドレス情報を抜き出す
     * @param $group_name_prefix
     * @return array
     * @throws Exception
     */

    public static function getSecurityIpRanges($group_name_prefix): array {
        $ec2Client = self::getEC2Instance();
        $list = [];
        $result = $ec2Client->describeSecurityGroups();
        foreach ($result['SecurityGroups'] as $group) {
            if (str_contains($group['GroupName'], $group_name_prefix)) {
                foreach ($group['IpPermissions'] as $IpPermissions) {
                    foreach ($IpPermissions['IpRanges'] as $ipRange) {
                        $list[] = [$ipRange['CidrIp'], $ipRange['Description']];
                    }
                }
            }
        }
        return $list;
    }


}