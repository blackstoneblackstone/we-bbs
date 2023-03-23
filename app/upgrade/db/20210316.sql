INSERT INTO `[#DB_PREFIX#]hook`(`system`, `name`, `intro`, `source`, `status`, `add_time`, `update_time`) VALUES (1, 'action_explore_main', '发现页渲染', 'system', 1, 1583844198, 0);
INSERT INTO `[#DB_PREFIX#]hook`(`system`, `name`, `intro`, `source`, `status`, `add_time`, `update_time`) VALUES (1, 'action_explore_ajax', '发现页异步渲染', 'system', 1, 1583844198, 0);

INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_enable', 's:1:"N";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_type', 's:4:"text";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_image', 's:0:"";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_image_position', 's:9:"top-right";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_image_type', 's:6:"normal";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_image_opacity', 's:3:"0.8";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_text', 's:8:"WeCenter";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_text_font_size', 's:2:"36";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_text_x', 's:3:"100";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_text_y', 's:3:"100";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_text_color', 's:7:"#ff0000";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_text_font', 's:0:"";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES('watermark_text_angle', 's:1:"0";');
INSERT INTO `[#DB_PREFIX#]menu` (`title`, `cname`, `url`, `pid`, `unid`, `status`, `systerm`) VALUES ('图片设置', 'home', 'admin/settings/category-picture', '2', NULL, '1', '1');
ALTER TABLE `[#DB_PREFIX#]approval` ADD COLUMN `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态:0待审核,1审核通过,-1拒绝审核';
ALTER TABLE `[#DB_PREFIX#]article` MODIFY COLUMN `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL ;

ALTER TABLE `[#DB_PREFIX#]report`
    MODIFY COLUMN `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否处理0待处理，1已处理，2拒绝处理',
    ADD COLUMN `decline_reason` varchar(255) NULL COMMENT '拒绝举报理由';