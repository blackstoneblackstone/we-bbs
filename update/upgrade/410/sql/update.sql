ALTER TABLE `aws_users_verify`
    MODIFY COLUMN `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '审核数据';

ALTER TABLE `aws_verify_field` ADD COLUMN  `validate` tinyint(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1'  COMMENT '是否必填',
                               ADD COLUMN  `verify_show` tinyint(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1'  COMMENT '是否认证显示';

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('cache_type', '8', '缓存方式', '选择缓存方式,当选择非文件缓存时，请点击下方“检查并配置缓存链接”', 'radio', 'file', '{"file":"文件缓存","redis":"Redis缓存","memcached":"Memcached缓存","memcache":"Memcache缓存"}', 51, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('cache_type_test', '8', '检查并配置缓存链接', '', 'html', '&lt;a class=&quot;btn btn-primary aw-ajax-open&quot; data-url=&quot;index/cache_type_check&quot; data-title=&quot;检查缓存状态&quot;&gt;检查缓存状态&lt;/a&gt;', '', 51, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('cache_host', '8', '链接地址', '链接地址默认127.0.0.1', 'hidden', '127.0.0.1', '', 51, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('cache_port', '8', '链接端口', '链接端口', 'hidden', '', '', 51, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('cache_password', '8', '链接密码', '链接密码', 'hidden', '', '', 51, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('pc_host', '1', 'PC端域名', '若开启手机端,PC端域名和手机端域名必须同时填写手机域名才生效,格式为www.xxx.com;不带http://或https://', 'text', '', '', 6, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('mobile_host', '1', '手机端域名', '若开启手机端,PC端域名和手机端域名必须同时填写手机域名才生效,格式为m.xxx.com;不带http://或https://', 'text', '', '', 7, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('pjax_enable', '1', '是否启用pjax', '网站是否启用pjax请求', 'radio', 'Y', '{"Y":"启用","N":"不启用"}', 50, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('cron_enable', '1', '是否启用网页定时任务', '是否启用网页定时任务,若服务器不允许exec函数或则未启用拓展->定时任务,可开启网页定时任务', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 50, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('answer_sort_type', '9', '默认回答排序', '选择默认回答排序方式', 'radio', 'new', '{"new":"最新排序","hot":"热门排序","publish":"只看楼主","focus":"关注的人"}', 50, '', 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('reputation_calc_limit', '6', '威望每次计算条数', '威望每次计算用户条数', 'number', '200', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('auto_question_lock_day', '9', '自动锁定问题天数', '自动锁定问题天数 0 代表不自动锁定，单位：天', 'number', '60', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('auto_set_best_answer_day', '9', '自动设定最佳回答天数', '自动设定最佳回答天数 0 代表不自动设定，单位：天', 'number', '7', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('best_answer_min_count', '9', '自动设定最佳回答时该问题最小回答数', '自动设定最佳回答时该问题最小回答数', 'number', '1', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('enable_multilingual', '7', '启用多语言', '启用前台多语言', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 0, NULL, 1);

ALTER TABLE `aws_article` ADD COLUMN `user_ip` varchar(20) DEFAULT NULL COMMENT '用户的来源IP',
                          ADD COLUMN `article_type` varchar(50) NOT NULL DEFAULT 'normal' COMMENT 'normal普通文章',
                          ADD COLUMN `extends` text COMMENT '附加信息,用于存储附属信息';

ALTER TABLE `aws_menu_rule` ADD COLUMN  `is_home` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为默认首页';
UPDATE `aws_config` SET `type` = 'password' WHERE `name` = 'authorize_code';

CREATE TABLE IF NOT EXISTS `aws_topic_related` (
                                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                                   `source_id` int(11) NOT NULL DEFAULT '0' COMMENT '原话题ID',
                                                   `target_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联话题ID',
                                                   PRIMARY KEY (`id`) USING BTREE,
                                                   KEY `source_id` (`source_id`) USING BTREE,
                                                   KEY `target_id` (`target_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='相关话题表';

CREATE TABLE IF NOT EXISTS `aws_topic_merge` (
                                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                                 `source_id` int(11) NOT NULL DEFAULT '0' COMMENT '原话题ID',
                                                 `target_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标话题ID',
                                                 `uid` int(11) DEFAULT '0' COMMENT '合并用户',
                                                 `create_time` int(10) DEFAULT '0',
                                                 PRIMARY KEY (`id`) USING BTREE,
                                                 KEY `source_id` (`source_id`) USING BTREE,
                                                 KEY `target_id` (`target_id`) USING BTREE,
                                                 KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='话题合并表';

ALTER TABLE `aws_answer` ADD COLUMN `force_fold` tinyint(1) DEFAULT '0' COMMENT '强制折叠';

CREATE TABLE IF NOT EXISTS `aws_browse_records` (
                                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                                    `uid` int(10) unsigned DEFAULT '0' COMMENT '用户UID',
                                                    `item_id` int(10) unsigned DEFAULT '0' COMMENT '内容ID',
                                                    `item_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '内容类型，question问题，article文章，topic话题,column专栏',
                                                    `status` tinyint(10) unsigned DEFAULT '1' COMMENT '1正常0删除',
                                                    `create_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
                                                    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='浏览记录表';

ALTER TABLE `aws_approval` ADD COLUMN `item_id` int(10) unsigned DEFAULT '0' COMMENT '审核内容ID';

CREATE TABLE `aws_question_redirect` (
                                         `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                         `item_id` int(11) DEFAULT '0',
                                         `target_id` int(11) DEFAULT '0',
                                         `uid` int(11) DEFAULT NULL,
                                         `create_time` int(10) DEFAULT '0',
                                         PRIMARY KEY (`id`) USING BTREE,
                                         KEY `item_id` (`item_id`) USING BTREE,
                                         KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='重定向表';

INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('redirect_question', '允许重定向问题', '', 'radio', 'N', '{\"N\":\"否\",\"Y\":\"是\"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('lock_question', '允许锁定问题', '', 'radio', 'N', '{\"N\":\"否\",\"Y\":\"是\"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('merge_topic', '允许合并话题', '', 'radio', 'N', '{\"N\":\"否\",\"Y\":\"是\"}', 0, NULL, 'system');

INSERT INTO `aws_action` (`name`, `title`, `remark`, `log_rule`, `status`) VALUES ('modify_log', '内容修改记录', '内容修改记录', '[user] 在 [time] 修改了内容', 1);

ALTER TABLE `aws_users_inbox`
    ADD COLUMN `status` tinyint(1) UNSIGNED NULL DEFAULT 1 ;

ALTER TABLE `aws_users_inbox_dialog`
    ADD COLUMN `status` tinyint(1) UNSIGNED NULL DEFAULT 1 ;

DELETE FROM `aws_admin_auth` WHERE `name`='extend.Curd/change';

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('uninterested_fold', '9', '“不感兴趣”数量达到多少个时自动折叠回复', '“不感兴趣”数量达到多少个时自动折叠回复', 'number', '10', '', 0, NULL, 1);

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('visitor_view_answer_count', '5', '游客可浏览回答数量', '游客可浏览回答数量，0代表不限制', 'number', '0', '', 0, NULL, 1);

DROP TABLE IF EXISTS `aws_curd`;
CREATE TABLE `aws_curd` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `name` varchar(100) DEFAULT NULL COMMENT '表标识',
                            `title` varchar(255) DEFAULT NULL COMMENT '表名称',
                            `remark` varchar(255) DEFAULT NULL COMMENT '表说明',
                            `top_button` varchar(255) DEFAULT NULL COMMENT '顶部按钮',
                            `right_button` varchar(255) DEFAULT NULL COMMENT '右侧按钮',
                            `page` tinyint(1) unsigned DEFAULT '1' COMMENT '是否分页',
                            `is_sort` tinyint(1) unsigned DEFAULT '1' COMMENT '添加排序字段',
                            `is_status` tinyint(1) unsigned DEFAULT '1' COMMENT '添加状态字段',
                            `pid_field` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'pid' COMMENT '树形菜单pid字段',
                            `pk` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '主键',
                            `menu_pid` int(10) unsigned DEFAULT '0' COMMENT '父级菜单ID',
                            `extends` text COLLATE utf8mb4_unicode_ci COMMENT '拓展信息',
                            `status` tinyint(1) unsigned DEFAULT '1' COMMENT '是否允许编辑',
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CURD表';

DROP TABLE IF EXISTS `aws_curd_field`;
CREATE TABLE `aws_curd_field`  (
                                   `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
                                   `table` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所属表',
                                   `field` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段名',
                                   `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段别名',
                                   `tips` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '提示信息',
                                   `required` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否必填',
                                   `minlength` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最小长度',
                                   `maxlength` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最大长度',
                                   `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段类型',
                                   `data_source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数据源',
                                   `relation_db` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关联表',
                                   `relation_field` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '展示字段',
                                   `dict_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字典类型',
                                   `is_add` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否可插入',
                                   `is_edit` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否可编辑',
                                   `is_list` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否可列表展示',
                                   `is_search` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否可查询',
                                   `is_sort` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否可排序',
                                   `is_pk` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是主键',
                                   `search_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '查询类型',
                                   `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
                                   `sort` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                   `remark` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
                                   `settings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '其他设置',
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'CURD字段表';

ALTER TABLE `aws_column` DROP COLUMN `auth`;

DROP TABLE IF EXISTS `aws_column_recommend_article`;
CREATE TABLE `aws_column_recommend_article` (
                                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                                `uid` int(10) unsigned DEFAULT '0' COMMENT '推荐用户',
                                                `column_id` int(10) unsigned DEFAULT '0' COMMENT '专栏id',
                                                `article_id` int(10) unsigned DEFAULT '0' COMMENT '推荐文章id',
                                                `status` tinyint(255) unsigned DEFAULT '0' COMMENT '审核状态0待审核1已审核2已拒绝',
                                                `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '拒绝理由',
                                                `create_time` int(10) unsigned DEFAULT NULL,
                                                `update_time` int(10) unsigned DEFAULT '0',
                                                PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='专栏推荐文章表';

DROP TABLE IF EXISTS `aws_help_chapter`;
CREATE TABLE `aws_help_chapter` (
                                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                    `title` varchar(100) NOT NULL COMMENT '章节标题',
                                    `description` text COMMENT '章节描述',
                                    `url_token` varchar(32) DEFAULT NULL COMMENT '章节别名',
                                    `image` varchar(255) DEFAULT NULL COMMENT '章节图标',
                                    `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '章节排序',
                                    `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
                                    PRIMARY KEY (`id`) USING BTREE,
                                    KEY `title` (`title`) USING BTREE,
                                    KEY `url_token` (`url_token`) USING BTREE,
                                    KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='帮助章节';

DROP TABLE IF EXISTS `aws_help_chapter_relation`;
CREATE TABLE `aws_help_chapter_relation` (
                                             `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                             `chapter_id` int(10) unsigned DEFAULT '0' COMMENT '章节ID',
                                             `item_type` varchar(100) NOT NULL COMMENT '关联类型',
                                             `item_id` int(10) unsigned DEFAULT '0' COMMENT '关联ID',
                                             `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
                                             `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '章节排序',
                                             PRIMARY KEY (`id`) USING BTREE,
                                             KEY `chapter_id` (`chapter_id`) USING BTREE,
                                             KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='帮助内容关联表';

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (6, 'content.Help/index', '帮助章节', 1, 1, '', 61, 1, 'icon-help', 0, 0, '', 'system');
SET @pid=LAST_INSERT_ID();
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Help/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Help/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Help/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Help/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Help/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Help/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Help/choose', '操作-选择', 1, 0, '', 9, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_menu_rule` (`pid`, `name`, `title`, `type`, `status`, `sort`, `auth_open`, `icon`, `param`, `group`, `is_home`) VALUES (0,  'help/index', '帮助', 1, 1, 50, 0, '','','nav', 0);

DROP TABLE IF EXISTS `aws_feature`;
CREATE TABLE `aws_feature` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `title` varchar(200) DEFAULT NULL COMMENT '专题标题',
                               `description` varchar(255) DEFAULT NULL COMMENT '专题描述',
                               `image` varchar(255) DEFAULT NULL COMMENT '专题封面',
                               `topic_count` int(11) DEFAULT '0' COMMENT '话题数量',
                               `css` text COMMENT '自定义css样式文件',
                               `url_token` varchar(32) DEFAULT NULL COMMENT '专题别名',
                               `seo_title` varchar(255) DEFAULT NULL COMMENT '专题SEO标题',
                               `seo_keywords` varchar(255) DEFAULT NULL COMMENT '专题SEO关键词',
                               `seo_description` varchar(255) DEFAULT NULL COMMENT '专题SEO描述',
                               `status` tinyint(1) NOT NULL DEFAULT '0',
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题表';

DROP TABLE IF EXISTS `aws_feature_topic`;
CREATE TABLE `aws_feature_topic` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `feature_id` int(11) DEFAULT '0' COMMENT '专题ID',
                                     `topic_id` int(11) DEFAULT '0' COMMENT '话题ID',
                                     PRIMARY KEY (`id`) USING BTREE,
                                     KEY `feature_id` (`feature_id`) USING BTREE,
                                     KEY `topic_id` (`topic_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专题话题关联表';

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (6, 'content.Feature/index', '专题管理', 1, 1, '', 61, 1, 'icon-help', 0, 0, '', 'system');
SET @pid=LAST_INSERT_ID();
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Feature/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Feature/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Feature/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Feature/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Feature/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Feature/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'content.Feature/choose', '操作-选择', 1, 0, '', 9, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_menu_rule` (`pid`, `name`, `title`, `type`, `status`, `sort`, `auth_open`, `icon`, `param`, `group`, `is_home`) VALUES (0,  'feature/index', '专题', 1, 1, 50, 0, '','','nav', 0);

DROP TABLE IF EXISTS `aws_dict`;
CREATE TABLE `aws_dict` (
                            `id` int(10) NOT NULL AUTO_INCREMENT,
                            `name` varchar(100) NOT NULL DEFAULT '' COMMENT '字典标签',
                            `value` varchar(255) NOT NULL DEFAULT '' COMMENT '字典键值',
                            `dict_id` int(10) NOT NULL DEFAULT '0' COMMENT '字典类型ID',
                            `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典数据表';

DROP TABLE IF EXISTS `aws_dict_type`;
CREATE TABLE `aws_dict_type` (
                                 `id` int(10) NOT NULL AUTO_INCREMENT,
                                 `title` char(100) NOT NULL DEFAULT '' COMMENT '显示名称',
                                 `name` varchar(100) NOT NULL DEFAULT '' COMMENT '字典标识',
                                 `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典类型表';

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (2, 'admin.DictType/index', '字典类型', 1, 1, '', 61, 1, 'fa fa-database ', 0, 0, '', 'system');
SET @pid=LAST_INSERT_ID();
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'admin.DictType/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'admin.DictTypee/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'admin.DictType/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (2, 'admin.Dict/index', '字典数据', 1, 1, '', 61, 1, 'fa fa-bezier-curve', 0, 0, '', 'system');
SET @pid=LAST_INSERT_ID();
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'admin.Dict/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'admin.Dict/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'admin.Dict/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');

DELETE FROM `aws_config` WHERE `name` = 'notify_group';
DELETE FROM `aws_config_group` WHERE `name` = '字典配置';

INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (1, '状态', '1',  '1 显示， 0 隐藏');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (2, '是否', '1', '1 是， 0 否');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (3, '性别', '1', '0 保密，1 男，2 女');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (4, '通知分组', 'notify_group','通知分组');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (5, '语言选择', 'language_select','语言选择');

INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('显示', '1', '1', '显示');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('隐藏', '0', '1', '隐藏');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('是', '1', '2', '是');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('否', '0', '2', '否');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('保密', '0', '3', '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('男', '1', '3', '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('女', '2', '3', '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('关注我的', 'TYPE_PEOPLE_FOCUS_ME', 4, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('提到我的', 'TYPE_PEOPLE_AT_ME', 4, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('赞同喜欢', 'TYPE_AGREE', 4, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('评论回复', 'TYPE_ANSWER_COMMENT', 4, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('邀请我的', 'TYPE_INVITE', 4, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('站务通知', 'TYPE_APPROVAL',4, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('系统通知', 'TYPE_SYSTEM_NOTIFY', 4, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('中文', 'zh-cn', 5, '');
INSERT INTO `aws_dict` (`name`, `value`, `dict_id`, `remark`) VALUES ('英文', 'en-us', 5, '');

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('remember_login_enable', '5', '是否启用记住登录状态', '是否启用记住登录状态,为了防止用户通过保存cookie实现自动登录，建议不启用', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 50, NULL, 1);

DROP TABLE IF EXISTS `aws_route_rule`;
CREATE TABLE `aws_route_rule` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) DEFAULT NULL COMMENT '标题',
      `url` varchar(255) DEFAULT NULL COMMENT 'url',
      `rule` varchar(255) DEFAULT NULL COMMENT '规则',
      `method` varchar(100) DEFAULT '*' COMMENT '请求方法',
      `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1正常0删除',
      `entrance` varchar(50) DEFAULT 'frontend' COMMENT '入口',
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='路由配置';

INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (1, '发现页', 'index/index', 'explore/[:sort]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (2, '问题详情', 'question/detail', 'question/:id-[:answer]-[:sort]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (3, '问题列表', 'question/index', 'questions/[:sort]-[:category_id]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (4, '回答列表', 'question/answers', 'answers/[:sort]-[:question_id]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (5, '文章预览', 'article/preview', 'preview/article', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (6, '文章详情', 'article/detail', 'article/:id', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (7, '文章列表', 'article/index', 'articles/[:sort]-[:category_id]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (8, '发起问题', 'question/publish', 'publish/question/[:id]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (9, '发起文章', 'article/publish', 'publish/article/[:id]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (10, '专栏列表', 'column/index', 'columns/[:sort]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (11, '专栏详情', 'column/detail', 'column/detail/:id', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (12, '专栏收录', 'column/collect', 'c/collect/:id', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (13, '话题列表', 'topic/index', 'topics/[:type]-[:pid]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (14, '话题详情', 'topic/detail', 'topic/:id-[:sort]-[:type]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (16, '话题选择', 'topic/select', 'select/topic', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (18, '管理话题', 'topic/manager', 'manager/topic/[:id]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (19, '用户主页', 'people/index', 'people/:name/[:type]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (20, '大咖列表', 'people/lists', 'peoples/[:page]', '*', 1, 'all');
INSERT INTO `aws_route_rule` (`id`, `title`, `url`, `rule`, `method`, `status`, `entrance`) VALUES (21, '用户管理首页', 'creator/index', 'creator/', '*', 1, 'all');

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (2, 'extend.RouteRule/index', '路由规则', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
SET @pid=LAST_INSERT_ID();
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/choose', '操作-选择', 1, 0, '', 9, 1, '', 0, 0, '', 'system');

DELETE FROM `aws_config` WHERE `name` = 'url_rewrite';
ALTER TABLE `aws_config`
    ADD COLUMN `source` tinyint(1) UNSIGNED NULL DEFAULT '0' COMMENT '配置来源',
    ADD COLUMN `dict_code` int(10) UNSIGNED NULL DEFAULT '0' COMMENT '字典数据';

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`, `source`, `dict_code`) VALUES ('default_language', '7', '默认站点语言', '站点默认使用语言', 'radio', 'zh-cn', '[]', 50, '', 0, 1, 5);

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (298, 'admin.Theme/upgrade', '操作-更新配置', 1, 0, '', 50, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('best_agree_min_count', '9', '自动设定最佳回答时该回答至少获赞数', '自动设定最佳回答时该回答至少获赞数', 'number', '3', '', 0, NULL, 1);

ALTER TABLE `aws_category` ADD COLUMN `description` varchar(255) DEFAULT NULL COMMENT '分类描述';

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('upload_image_thumb_enable', '4', '是否启用图片压缩', '是否启用图片压缩，PNG图片压缩会失去透明效果', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('upload_image_thumb_percent', '4', '上传图片默认压缩比例', '默认为0.7', 'text', '0.7', 'null', 0, NULL, 1);

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('content_popular_value_show', '9', '热度值大于多少参与热度排序', '热度值达到多少参与热度排序,默认大于0即参与热门排序', 'number', '0', '', 0, NULL, 1);

ALTER TABLE `aws_topic`
    MODIFY COLUMN `url_token` varchar(255) DEFAULT NULL;
ALTER TABLE `aws_topic`
    MODIFY COLUMN `title` varchar(255) DEFAULT NULL;

# 2023-02-16
ALTER TABLE `aws_post_relation`
    ADD COLUMN `relation_type` varchar(100) DEFAULT NULL COMMENT '关联类型',
    ADD COLUMN `relation_id` int(10) UNSIGNED NULL DEFAULT '0' COMMENT '关联类型ID';

INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`, `source`, `dict_code`) VALUES ('db_version', '1', '数据库版本', '', 'hidden', '410', 'null', 0, NULL, 1, 0, 0);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`, `source`, `dict_code`) VALUES ('local_upgrade_enable', '1', '启用本地升级', '升级完成后建议关闭', 'radio', 'Y', '{"Y":"启用","N":"不启用"}', 0, NULL, 1, 0, 0);