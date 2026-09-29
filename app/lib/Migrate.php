<?php

// 簡易DBマイグレーション

use classes\trait\DBMigrate;
use classes\trait\DBTableTrait;

/*
// カラム削除
ALTER TABLE `member_condition` DROP COLUMN  `score`
//　カラム追加
ALTER TABLE `qube` ADD `generation` INT(10) NOT NULL default '0' COMMENT '譲渡世代' AFTER `registration`;
//　カラム変更
ALTER TABLE `admin_log` CHANGE `operate_id` `operate_id` VARCHAR(20) NOT NULL COMMENT '操作ID';

 * */

class Migrate extends Model {

    use DBTableTrait;
    use DBMigrate;

    // -------------------------------------------------------------------------------------------------------------
    // DBマイグレーション関数　この下に migration*** メソッドを追加していく

    function migration_windex_local_table(): array {
        return [
            'state' => $this->hasTable("windex_local_race"),
            'sql' => "
                CREATE TABLE `windex_local_day` (
                  `id` bigint(20) NOT NULL,
                  `day` date NOT NULL COMMENT 'レース日',
                  `start_at` datetime DEFAULT NULL COMMENT '開始日時',
                  `work` text NOT NULL COMMENT '情報等',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                
                CREATE TABLE `windex_local_race` (
                  `id` bigint(20) NOT NULL,
                  `status` varchar(10) NOT NULL COMMENT '状態', 
                  `race_code` varchar(20) NOT NULL COMMENT 'レースコード',
                  `start_at` datetime NOT NULL COMMENT '発走時刻',
                  `grade_code` varchar(10) NOT NULL COMMENT '重賞コード',
                  `conditions` varchar(30) NOT NULL COMMENT '競走条件',
                  `race_name` varchar(50) NOT NULL COMMENT 'レース名',
                  `course` varchar(30) NOT NULL COMMENT 'コース',
                  `entrants` int(11) DEFAULT NULL COMMENT '出走頭数',
                  `need_points` int(11) NOT NULL DEFAULT 100 COMMENT '閲覧ポイント',
                  `work` text NOT NULL COMMENT '情報',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                
                CREATE TABLE `windex_local_uma` (
                  `id` bigint(20) NOT NULL,
                  `race_code` varchar(20) DEFAULT NULL COMMENT 'レースコード',
                  `wakuban` int(11) DEFAULT NULL COMMENT '枠番',
                  `umaban` int(11) DEFAULT NULL COMMENT '馬番',
                  `status` int(11) NOT NULL DEFAULT 0 COMMENT '出走馬ステータス',
                  `chakujun` int(11) NOT NULL DEFAULT 0 COMMENT '着順',
                  `basic_ability` int(11) DEFAULT NULL COMMENT '基礎能力値 25-125',
                  `pedigree` int(11) DEFAULT NULL COMMENT '血統 -5/-3/0/+3/+5',
                  `running_style_and_development` int(11) DEFAULT NULL COMMENT '脚質・展開 -5/-3/0/+3/+5',
                  `final_time` int(11) DEFAULT NULL COMMENT '走破タイム -5/-3/0/+3/+5',
                  `jockeys_and_trainers` int(11) DEFAULT NULL COMMENT '騎手・調教師 -5/-3/0/+3/+5',
                  `work` text NOT NULL COMMENT '情報',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                
                ALTER TABLE `windex_local_day`                  ADD PRIMARY KEY (`id`),                  ADD UNIQUE KEY `race_code` (`day`);
                ALTER TABLE `windex_local_race`                  ADD PRIMARY KEY (`id`),                  ADD KEY `race_code` (`race_code`) USING BTREE;
                ALTER TABLE `windex_local_uma`                  ADD PRIMARY KEY (`id`),                  ADD KEY `race_code` (`race_code`) USING BTREE;

                ALTER TABLE `windex_local_day`                  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;                
                ALTER TABLE `windex_local_race`                  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
                ALTER TABLE `windex_local_uma`                  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
           "
        ];
    }

    function migration_add_content_author_rank(): array {
        return [
            'state' => $this->hasField('content_author', 'author_rank'),
            'sql' => " 
                ALTER TABLE `content_author` ADD `author_rank` varchar(5) NOT NULL DEFAULT 'A' COMMENT '著者ランク' AFTER `image_id`;
            ",
        ];
    }

    function migration_table_point_pack(): array {
        return [
            'state' => $this->hasTable("point_pack"),
            'sql' => "
                CREATE TABLE `point_pack` (
                  `id` bigint(20) NOT NULL,
                  `title` varchar(100) NOT NULL COMMENT 'パック商品名',
                  `start_at` datetime DEFAULT NULL COMMENT '早割開始日時',
                  `publish_at` datetime DEFAULT NULL COMMENT '記事確定日時',
                  `work` text DEFAULT NULL COMMENT 'パック内容',
                  `end_at` datetime DEFAULT NULL COMMENT '終了日時',
                  `closed` tinyint(1) NOT NULL DEFAULT 0 COMMENT '終了・無効',
                  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '作成日時',
                  `updated_at` datetime DEFAULT NULL COMMENT '更新日時'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE `point_pack` ADD PRIMARY KEY (`id`);
                ALTER TABLE `point_pack` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
                ALTER TABLE `point_pack` ADD INDEX(`start_at`);

           "
        ];
    }


    function migration_table_kpi_apache(): array {
        $log_db = App::logDB();
        return [
            'state' => $this->hasTable("{$log_db}.kpi"),
            'sql' => "
                CREATE TABLE {$log_db}.`apache` (
                  `id` bigint(20) NOT NULL,
                  `uniq` varchar(255) NOT NULL,
                  `ip` bigint(20) UNSIGNED NOT NULL,
                  `path` varchar(100) NOT NULL,
                  `status` int(11) NOT NULL,
                  `referer` varchar(100) NOT NULL,
                  `aaid` bigint(20) NOT NULL,
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE {$log_db}.`apache`  ADD PRIMARY KEY (`id`,`created_at`),  ADD KEY `uniq` (`uniq`);
                ALTER TABLE {$log_db}.`apache`  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

               CREATE TABLE {$log_db}.`apache_agent` (
                  `id` bigint(20) NOT NULL,
                  `type` varchar(10) NOT NULL COMMENT '属性',
                  `org` varchar(10) NOT NULL COMMENT '所属',
                  `user_agent` varchar(500) NOT NULL COMMENT 'USER＿AGENT'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE {$log_db}.`apache_agent`  ADD PRIMARY KEY (`id`),  ADD UNIQUE KEY `UserAgent` (`user_agent`);
                ALTER TABLE {$log_db}.`apache_agent`  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

                CREATE TABLE {$log_db}.`kpi` (
                  `id` bigint(20) NOT NULL,
                  `category` tinyint(2) NOT NULL COMMENT '0:DYA 1:WEEK 2:MONTH',
                  `name` varchar(10) NOT NULL COMMENT '項目名',
                  `value` bigint(20) DEFAULT NULL COMMENT '集計値',
                  `created_at` date NOT NULL COMMENT '対象日'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE {$log_db}.`kpi`  ADD PRIMARY KEY (`id`,`created_at`);
                ALTER TABLE {$log_db}.`kpi`  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
           "
        ];
    }

    function migration_table_windex(): array {
        return [
            'state' => $this->hasTable('windex_race'),
            'sql' => " 
                CREATE TABLE `windex_day` (
                  `id` bigint(20) NOT NULL,
                  `day` date NOT NULL COMMENT 'レース日',
                  `weekday_at` datetime DEFAULT NULL COMMENT '平日版開始日時',
                  `weekend_at` datetime DEFAULT NULL COMMENT '週末版開始日時',
                  `work` text NOT NULL COMMENT 'Win5情報等',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE `windex_day`
                  ADD PRIMARY KEY (`id`),
                  ADD UNIQUE KEY `race_code` (`day`);
                ALTER TABLE `windex_day`
                  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
                
                CREATE TABLE `windex_kaime` (
                  `id` bigint(20) NOT NULL,
                  `race_code` varchar(20) DEFAULT NULL COMMENT 'レースコード',
                  `kaime_type` tinyint(4) NOT NULL COMMENT '1:的中重視型 2:期待値重視型',
                  `work` text NOT NULL COMMENT '詳細',
                  `payoff` int(11) NOT NULL DEFAULT 0 COMMENT '獲得額',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE `windex_kaime`
                  ADD PRIMARY KEY (`id`),
                  ADD UNIQUE KEY `race_code_2` (`race_code`,`kaime_type`);
                ALTER TABLE `windex_kaime`
                  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
                
                CREATE TABLE `windex_race` (
                  `id` bigint(20) NOT NULL,
                  `status` varchar(10) NOT NULL,
                  `race_code` varchar(20) NOT NULL COMMENT 'レースコード',
                  `race_name` varchar(50) NOT NULL COMMENT 'レース名',
                  `review` varchar(10) NOT NULL COMMENT '短評',
                  `review_1` tinyint(4) NOT NULL DEFAULT 0 COMMENT '短評1',
                  `review_2` tinyint(4) NOT NULL DEFAULT 0 COMMENT '短評2',
                  `kid_1` int(11) DEFAULT NULL COMMENT '的中券種',
                  `kid_2` int(11) DEFAULT NULL COMMENT '配当券種',
                  `need_points` int(11) NOT NULL DEFAULT 100 COMMENT '閲覧ポイント',
                  `payoff` int(11) NOT NULL DEFAULT 0 COMMENT '的中額',
                  `work` text NOT NULL COMMENT '情報',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE `windex_race`
                  ADD PRIMARY KEY (`id`),
                  ADD KEY `race_code` (`race_code`) USING BTREE;
                ALTER TABLE `windex_race`
                  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
                
                CREATE TABLE `windex_uma` (
                  `id` bigint(20) NOT NULL,
                  `race_code` varchar(20) DEFAULT NULL COMMENT 'レースコード',
                  `wakuban` int(11) DEFAULT NULL COMMENT '枠番',
                  `umaban` int(11) DEFAULT NULL COMMENT '馬番',
                  `registration` varchar(10) DEFAULT NULL COMMENT '血統登録番号',
                  `pre_ability` int(11) DEFAULT NULL COMMENT '平日基礎能力',
                  `basic_ability` int(11) DEFAULT NULL COMMENT '基礎能力値 25-125',
                  `pedigree` int(11) DEFAULT NULL COMMENT '血統 -5/-3/0/+3/+5',
                  `running_style_and_development` int(11) DEFAULT NULL COMMENT '脚質・展開 -5/-3/0/+3/+5',
                  `final_time` int(11) DEFAULT NULL COMMENT '走破タイム -5/-3/0/+3/+5',
                  `jockeys_and_trainers` int(11) DEFAULT NULL COMMENT '騎手・調教師 -5/-3/0/+3/+5',
                  `work` text NOT NULL COMMENT '情報',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE `windex_uma`
                  ADD PRIMARY KEY (`id`),
                  ADD KEY `race_code` (`race_code`,`registration`) USING BTREE;
                ALTER TABLE `windex_uma`
                  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
            ",
        ];
    }


    function migration_add_subscription_kind(): array {
        return [
            'state' => $this->hasField('subscription', 'kind'),
            'sql' => " 
                ALTER TABLE `subscription` ADD `kind` varchar(10) NOT NULL DEFAULT 'yoso' COMMENT '種別' AFTER `uid`;
                ALTER TABLE `subscription` ADD UNIQUE key `uid_expire_at` (`uid`, `expire_at`);
            ",
        ];
    }
    function migration_table_race_movie(): array {
        $table = 'race_movie';
        return [
            'state' => $this->hasTable($table),
            'sql' => " 
                CREATE TABLE `race_movie` (
                  `id` bigint(20) NOT NULL,
                  `status` varchar(10) NOT NULL,
                  `race_code` varchar(20) NOT NULL COMMENT 'レースコード',
                  `url` varchar(200) NOT NULL COMMENT 'ムービーURL',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                
                ALTER TABLE `race_movie`
                  ADD PRIMARY KEY (`id`),
                  ADD KEY `race_code` (`race_code`);
            ",
        ];
    }

    function migration_content_condition_priority(): array {
        return [
            'state' => $this->hasField('content_condition', 'priority'),
            'sql' => " 
                ALTER TABLE `content_condition` ADD `priority` INT NOT NULL DEFAULT '10' COMMENT '表示優先順位' AFTER `work`;
            ",
        ];
    }

    function migration_TableEnquete(): array {
        $log_db = App::logDB();
        return [
            'state' => $this->hasTable('enquete_master'),
            'sql' => " 
                CREATE TABLE `enquete_master` (
                  `id` bigint(20) NOT NULL,
                  `status` varchar(10) DEFAULT NULL COMMENT '状態',
                  `title` varchar(50) DEFAULT NULL COMMENT 'タイトル名',
                  `start_at` datetime DEFAULT NULL COMMENT '開始日時',
                  `end_at` datetime DEFAULT '2100-01-01 00:00:00' COMMENT '終了日時',
                  `work` text NOT NULL COMMENT '設定',
                  `questions` text NOT NULL COMMENT '設問',
                  `result` text NOT NULL COMMENT '集計結果',
                  `closed` tinyint(4) DEFAULT 0 COMMENT '終了フラグ',
                  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE `enquete_master` ADD PRIMARY KEY (`id`);
                ALTER TABLE `enquete_master` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

                CREATE TABLE $log_db.`enquete` (
                  `id` bigint(20) NOT NULL,
                  `uid` varchar(10) NOT NULL COMMENT 'UID',
                  `enquete_id` bigint(20) NOT NULL COMMENT 'enquete_master.id',
                  `question_id` int(10) NOT NULL COMMENT '設問ID',
                  `result` int(10) NOT NULL COMMENT '回答番号',
                  `work` text NOT NULL COMMENT '複数回答',
                  `created_at` datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin
                PARTITION BY RANGE COLUMNS(`created_at`)
                (
                PARTITION p2025 VALUES LESS THAN ('2026-01-01') ENGINE=InnoDB,
                PARTITION p2026 VALUES LESS THAN ('2027-01-01') ENGINE=InnoDB
                );
                ALTER TABLE $log_db.`enquete` ADD PRIMARY KEY (`id`,`created_at`), ADD KEY `everyone_id` (`enquete_id`,`question_id`), ADD KEY `uid` (`uid`);
                ALTER TABLE $log_db.`enquete` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
            ",
        ];
    }

    function migration_PointLogBalance(): array {
        $table = App::logDB('point_log');
        return [
            'state' => $this->hasField($table, 'gpp'),
            'sql' => " 
                ALTER TABLE {$table} ADD `gpb` int(11) DEFAULT 0 COMMENT 'ギフトポイント残高' AFTER `gift_point`;
                ALTER TABLE {$table} ADD `ppb` int(11) DEFAULT 0 COMMENT '有償ポイント残高' AFTER `gift_point`;
            ",
        ];
    }

    function migration_TableRanking(): array {
        $table = App::logDB('ranking');
        return [
            'state' => $this->hasTable($table),
            'sql' => " 
                CREATE TABLE {$table} (
                  `subject` varchar(20) NOT NULL COMMENT '対象',
                  `identifier` varchar(20) NOT NULL COMMENT '識別子',
                  `value` bigint(11) NOT NULL COMMENT '値'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
                ALTER TABLE  {$table}  ADD UNIQUE KEY `subject` (`subject`,`identifier`);
            ",
        ];
    }

    function migration_MemberConditionAt(): array {
        return [
            'state' => $this->hasField('member_condition', 'score_at'),
            'sql' => " 
                ALTER TABLE `member_condition` ADD `scored_at` DATETIME DEFAULT NULL COMMENT '最終スコア獲得' AFTER `paymented_at`;
                ALTER TABLE `member_condition` ADD `pointed_at` DATETIME DEFAULT NULL COMMENT '最終ポイント獲得' AFTER `paymented_at`;
                ALTER TABLE `member_condition` ADD `point_used_at` DATETIME DEFAULT NULL COMMENT '最終ポイント利用' AFTER `paymented_at`;
                ALTER TABLE `member_condition` DROP `ad_code`;
            ",
        ];
    }

    function migration_MemberConditionStaffFlag(): array {
        return [
            'state' => $this->hasField('member_condition', 'staff_flag'),
            'sql' => " 
                ALTER TABLE `member_condition` ADD `staff_flag` TINYINT NOT NULL DEFAULT '0' COMMENT 'スタッフフラグ' AFTER `notes3`;
            ",
        ];
    }

}