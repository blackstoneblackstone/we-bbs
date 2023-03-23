ALTER TABLE `[#DB_PREFIX#]integral_log`
    MODIFY COLUMN `action` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `[#DB_PREFIX#]approval`
    MODIFY COLUMN `data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES ('weixin_xcx_app_id', 's:0:"";'),('weixin_xcx_app_secret', 's:0:"";');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES ('upload_cat_type', 's:38:"answer,question,article,project,column";'),('exception_handle_enable', 's:1:"Y";'),('log_remark_type', 'a:1:{i:0;s:5:"error";}');
INSERT INTO `[#DB_PREFIX#]system_setting`(`varname`, `value`) VALUES ('enable_search_answer', 's:1:"N";'),('set_top_num','s:1:"3";');

ALTER TABLE `[#DB_PREFIX#]article` ADD COLUMN `data_type` varchar(32) NOT NULL DEFAULT 'normal' COMMENT 'normal-标准';
ALTER TABLE `[#DB_PREFIX#]question` ADD COLUMN `data_type` varchar(32) NOT NULL DEFAULT 'normal' COMMENT 'normal-标准';

ALTER TABLE `[#DB_PREFIX#]attach` ADD COLUMN `outer_link` varchar(500) NOT NULL DEFAULT '' COMMENT '附件外链',
 ADD COLUMN `file_size` varchar(32) NOT NULL DEFAULT '' COMMENT '附件大小',
 ADD COLUMN `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户uid',
 ADD COLUMN `download_count` int(10) NOT NULL DEFAULT 0 COMMENT '下载次数';

