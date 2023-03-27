SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
/*SET GLOBAL innodb_large_prefix = ON;
SET GLOBAL innodb_file_format=Barracuda;
SET GLOBAL innodb_file_per_table=ON;*/
-- ----------------------------
-- Table structure for aws_admin_auth
-- ----------------------------
DROP TABLE IF EXISTS `aws_admin_auth`;
CREATE TABLE `aws_admin_auth`  (
       `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
       `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
       `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '控制器/方法',
       `title` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
       `type` tinyint(1) NOT NULL DEFAULT 1,
       `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单状态',
       `condition` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
       `sort` mediumint(8) NOT NULL DEFAULT 0 COMMENT '排序',
       `auth_open` tinyint(2) NULL DEFAULT 1,
       `icon` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单图标',
       `create_time` int(11) NULL DEFAULT 0 COMMENT '添加时间',
       `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
       `param` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '参数',
       `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限分组',
       PRIMARY KEY (`id`) USING BTREE,
       UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC COMMENT='后台权限';

INSERT INTO `aws_admin_auth` VALUES (1, 0, 'index', '控制台', 1, 1, '', 0, 1, 'si si-speedometer', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (2, 0, 'system', '系统管理', 1, 1, '', 0, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (3, 0, 'member', '用户管理', 1, 1, '', 2, 1, 'fa fa-users', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (4, 0, 'extend', '系统拓展', 1, 1, '', 3, 1, 'fa fa-business-time', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (5, 0, 'plugin', '插件模板', 1, 1, '', 4, 1, 'fa fa-anchor', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (6, 0, 'content', '内容管理', 1, 1, '', 1, 1, 'fa fa-box', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (7, 1, 'Index/logout', '退出登录', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (8, 1, 'Index/index', '后台首页', 1, 1, '', 0, 1, 'fa fa-home', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (9, 1, 'Index/select2', 'select2下拉', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (10, 1, 'Index/icons', '选择图标', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (11, 1, 'Index/clear', '清空缓存', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (12, 2, 'admin.Config/index', '配置管理', 1, 1, '', 1, 1, 'fas fa-sun', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (13, 2, 'admin.Config/config', '系统配置', 1, 1, '', 0, 1, 'fa fa-edit', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (14, 12, 'admin.Config/group', '配置分组', 1, 0, '', 0, 1, 'fa fa-edit', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (15, 12, 'admin.Config/group_add', '添加分组', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (16, 12, 'admin.Config/group_edit', '编辑分组', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (17, 12, 'admin.Config/group_delete', '删除分组', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (19, 12, 'admin.Config/choose', '操作-选择', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (20, 12, 'admin.Config/export', '操作-导出', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (21, 2, 'admin.Auth/index', '后台菜单', 1, 1, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (22, 20, 'admin.Auth/add', '操作-添加', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (23, 20, 'admin.Auth/edit', '操作-编辑', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (24, 20, 'admin.Auth/delete', '操作-删除', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (25, 20, 'admin.Auth/sort', '操作-排序', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (26, 20, 'admin.Auth/state', '操作-状态', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (27, 20, 'admin.Auth/choose', '操作-选择', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (28, 20, 'admin.Auth/export', '操作-导出', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (29, 321, 'admin.Group/index', '系统组', 1, 1, '', 2, 1, 'fa fa-people-carry', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (30, 29, 'admin.Group/add', '操作-添加', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (31, 29, 'admin.Group/edit', '操作-编辑', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (32, 29, 'admin.Group/delete', '操作-删除', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (33, 29, 'admin.Group/permission', '操作-权限', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (34, 29, 'admin.Group/state', '操作-状态', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (35, 29, 'admin.Group/export', '操作-导出', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (36, 29, 'admin.Group/choose', '操作-选择', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (37, 12, 'admin.Config/add', '添加配置', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (38, 12, 'admin.Config/edit', '编辑配置', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (39, 12, 'admin.Config/delete', '删除配置', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (40, 12, 'admin.Config/sort', '排序配置', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (41, 12, 'admin.Config/state', '配置状态', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (42, 2, 'admin.Menu/index', '导航管理', 1, 1, '', 3, 1, 'fa fa-align-center', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (43, 42, 'admin.Menu/add', '添加导航', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (44, 42, 'admin.Menu/edit', '编辑导航', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (45, 42, 'admin.Menu/delete', '删除导航', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (46, 42, 'admin.Menu/sort', '排序导航', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (47, 42, 'admin.Menu/state', '导航状态', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (48, 42, 'admin.Menu/export', '操作-导出', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (49, 42, 'admin.Menu/choose', '操作-选择', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (50, 3, 'member.Users/index', '用户管理', 1, 1, '', 0, 1, 'fa fa-users', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (51, 50, 'member.Users/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (52, 50, 'member.Users/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (53, 50, 'member.Users/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (54, 50, 'member.Users/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (55, 50, 'member.Users/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (56, 50, 'member.Users/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (58, 50, 'member.Users/choose', '操作-选择', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (59, 321, 'member.ReputationGroup/index', '威望组', 1, 1, '', 2, 1, 'fa fa-key', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (60, 59, 'member.ReputationGroup/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (61, 59, 'member.ReputationGroup/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (62, 59, 'member.ReputationGroup/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (63, 59, 'member.ReputationGroup/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (64, 59, 'member.ReputationGroup/permission', '用户权限', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (65, 59, 'member.ReputationGroup/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (66, 59, 'member.ReputationGroup/export', '操作-导出', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (67, 59, 'member.ReputationGroup/choose', '操作-选择', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (68, 321, 'member.Permission/index', '用户权限', 1, 1, '', 2, 1, 'fa fa-user-check', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (69, 68, 'member.Permission/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (70, 68, 'member.Permission/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (71, 68, 'member.Permission/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (72, 68, 'member.Permission/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (73, 68, 'member.Permission/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (74, 68, 'member.Permission/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (76, 68, 'member.Permission/choose', '操作-选择', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (77, 4, 'extend.Database/database', '数据库备份', 1, 1, '', 31, 1, 'fa fa-server', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (78, 77, 'extend.Database/backup', '操作-备份', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (79, 77, 'extend.Database/repair', '操作-修复', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (80, 77, 'extend.Database/optimize', '操作-优化', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (81, 4, 'extend.Database/restore', '数据库还原', 1, 1, '', 32, 1, 'fa fa-recycle', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (82, 77, 'extend.Database/import', '操作-还原', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (83, 77, 'extend.Database/downFile', '操作-下载', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (84, 77, 'extend.Database/delete', '操作-删除', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (85, 4, 'extend.Links/index', '友情链接', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (86, 85, 'extend.Links/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (87, 85, 'extend.Links/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (88, 85, 'extend.Links/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (89, 85, 'extend.Links/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (90, 85, 'extend.Links/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (91, 85, 'extend.Links/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (92, 85, 'extend.Links/choose', '操作-选择', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (101, 5, 'plugin.Plugins/index', '插件模块', 1, 1, '', 0, 1, 'fas fa-cloud-upload-alt', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (102, 101, 'plugin.Plugins/config', '插件配置', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (103, 101, 'plugin.Plugins/install', '安装插件', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (104, 101, 'plugin.Plugins/uninstall', '卸载插件', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (105, 101, 'plugin.Plugins/design', '设计插件', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (106, 101, 'plugin.Plugins/state', '操作-状态', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (107, 101, 'plugin.Plugins/import', '操作-导入', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (108, 101, 'plugin.Plugins/delete', '操作-删除', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (109, 101, 'plugin.Plugins/upgrade', '操作-升级', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (110, 101, 'plugin.Plugins/export', '操作-导出', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (117, 101, 'plugin.Plugins/choose', '操作-选择', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (118, 5, 'plugin.Upgrade/index', '在线升级', 1, 1, '', 0, 1, 'fa fa-download', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (119, 118, 'plugin.Upgrade/download', '下载升级包', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (120, 118, 'plugin.Upgrade/check', '升级检测', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (124, 118, 'plugin.Upgrade/bind', '账号绑定', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (125, 118, 'plugin.Upgrade/unbind', '解绑账号', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (126, 6, 'content.Page/index', '页面管理', 1, 1, '', 2, 1, 'fa fa-file', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (127, 126, 'content.Page/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (128, 126, 'content.Page/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (129, 126, 'content.Page/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (130, 126, 'content.Page/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (131, 126, 'content.Page/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (132, 126, 'content.Page/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (133, 126, 'content.Page/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (134, 6, 'content.Category/index', '分类管理', 1, 1, '', 2, 1, 'fa fa-atlas', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (135, 134, 'content.Category/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (136, 134, 'content.Category/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (137, 134, 'content.Category/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (138, 134, 'content.Category/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (139, 134, 'content.Category/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (140, 134, 'content.Category/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (141, 134, 'content.Category/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (142, 321, 'member.IntegralGroup/index', '积分组', 1, 1, '', 2, 1, 'fa fa-database', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (143, 142, 'member.IntegralGroup/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (144, 142, 'member.IntegralGroup/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (145, 142, 'member.IntegralGroup/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (146, 142, 'member.IntegralGroup/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (147, 142, 'member.IntegralGroup/permission', '用户权限', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (148, 142, 'member.IntegralGroup/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (149, 142, 'member.IntegralGroup/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (150, 142, 'member.IntegralGroup/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (151, 322, 'member.IntegralRule/index', '积分规则', 1, 1, '', 61, 1, 'fa fa-database', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (152, 151, 'member.IntegralRule/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (153, 151, 'member.IntegralRule/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (154, 151, 'member.IntegralRule/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (155, 151, 'member.IntegralRule/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (156, 151, 'member.IntegralRule/deleteLog', '操作-删除记录', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (157, 151, 'member.IntegralRule/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (158, 151, 'member.IntegralRule/detail', '记录详情', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (159, 322, 'member.IntegralRule/log', '积分记录', 1, 1, '', 61, 1, 'far fa-bookmark', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (160, 151, 'member.IntegralRule/choose', '操作-选择', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (161, 323, 'member.Action/index', '行为规则', 1, 1, '', 61, 1, 'fa fa-cog', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (162, 161, 'member.Action/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (163, 161, 'member.Action/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (164, 161, 'member.Action/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (165, 161, 'member.Action/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (166, 161, 'member.Action/deleteLog', '操作-删除记录', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (167, 161, 'member.Action/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (168, 161, 'member.Action/detail', '记录详情', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (169, 323, 'member.Action/log', '行为记录', 1, 1, '', 61, 1, 'far fa-clipboard', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (170, 161, 'member.Action/choose', '操作-选择', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (171, 3, 'userVerify', '认证管理', 1, 1, '', 9, 1, 'fa fa-user-check', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (172, 171, 'member.Verify/index', '认证记录', 1, 1, '', 9, 1, 'fa fa-user-check', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (173, 171, 'member.Verify/field', '认证字段', 1, 1, '', 10, 1, 'far fa-check-square', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (174, 173, 'member.Verify/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (175, 172, 'member.Verify/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (176, 173, 'member.Verify/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (177, 173, 'member.Verify/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (178, 173, 'member.Verify/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (179, 172, 'member.Verify/preview', '操作-预览', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (180, 172, 'member.Verify/manager', '操作-管理', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (181, 173, 'member.Verify/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (183, 6, 'content.Question/index', '问题管理', 1, 1, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (184, 183, 'content.Question/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (185, 183, 'content.Question/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (186, 183, 'content.Question/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (187, 183, 'content.Question/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (188, 183, 'content.Question/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (189, 183, 'content.Question/seo', '操作-SEO设置', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (190, 183, 'content.Question/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (191, 183, 'content.Question/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (192, 6, 'content.Article/index', '文章管理', 1, 1, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (193, 192, 'content.Article/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (194, 192, 'content.Article/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (195, 192, 'content.Article/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (196, 192, 'content.Article/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (197, 192, 'content.Article/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (198, 192, 'content.Article/seo', '操作-SEO设置', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (199, 192, 'content.Article/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (200, 192, 'content.Article/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (201, 6, 'content.Column/index', '专栏管理', 1, 1, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (202, 201, 'content.Column/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (203, 201, 'content.Column/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (204, 201, 'content.Column/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (205, 201, 'content.Column/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (206, 201, 'content.Column/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (207, 201, 'content.Column/seo', '操作-SEO设置', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (208, 201, 'content.Column/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (209, 201, 'content.Column/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (210, 6, 'content.Topic/index', '话题管理', 1, 1, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (211, 210, 'content.Topic/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (212, 210, 'content.Topic/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (213, 210, 'content.Topic/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (214, 210, 'content.Topic/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (215, 210, 'content.Topic/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (216, 210, 'content.Topic/seo', '操作-SEO设置', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (217, 210, 'content.Topic/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (218, 210, 'content.Topic/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (219, 6, 'content.Approval/index', '审核管理', 1, 1, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (220, 219, 'content.Approval/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (221, 219, 'content.Approval/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (222, 219, 'content.Approval/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (223, 219, 'content.Approval/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (224, 219, 'content.Approval/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (225, 219, 'content.Approval/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (226, 219, 'content.Approval/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (227, 4, 'extend.Log/index', '操作记录', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (228, 161, 'extend.Log/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (229, 161, 'extend.Log/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (230, 161, 'extend.Log/detail', '记录详情', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (236, 4, 'extend.Task/index', '定时任务', 1, 1, '', 31, 1, 'fa fa-server', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (237, 236, 'extend.Task/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (238, 236, 'extend.Task/start', '操作-开始', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (239, 236, 'extend.Task/stop', '操作-停止', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (240, 236, 'extend.Task/delete', '操作-删除', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (241, 236, 'extend.Task/test', '操作-测试', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (242, 236, 'extend.Task/edit', '操作-编辑', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (243, 1, 'Index/statistic', '后台统计', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (244, 6, 'content.Report/index', '举报管理', 1, 1, '', 0, 1, 'fa fa-info-circle', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (245, 244, 'content.Report/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (246, 244, 'content.Report/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (247, 244, 'content.Report/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (248, 244, 'content.Report/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (249, 244, 'content.Report/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (250, 244, 'content.Report/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (251, 6, 'content.Answer/index', '回答管理', 1, 1, '', 2, 1, 'fa fa-file', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (252, 251, 'content.Answer/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (253, 251, 'content.Answer/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (254, 251, 'content.Answer/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (255, 251, 'content.Answer/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (256, 251, 'content.Answer/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (257, 251, 'content.Answer/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (258, 251, 'content.Answer/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (259, 236, 'extend.Task/status', '操作-状态', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (260, 236, 'extend.Task/progress', '操作-进度', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (261, 236, 'extend.Task/state', '操作-进度', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (262, 236, 'extend.Task/register', '操作-进度', 1, 0, '', 2, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (284, 50, 'member.Users/forbidden', '操作-封禁', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (285, 50, 'member.Users/un_forbidden', '操作-解除封禁', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (286, 50, 'member.Users/approval', '操作-审核', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (287, 50, 'member.Users/decline', '操作-拒绝审核', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (288, 50, 'member.Users/manager', '操作-管理', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (289, 50, 'member.Users/integral', '操作-积分', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (290, 6, 'content.Announce/index', '公告管理', 1, 1, '', 2, 1, 'fa fa-file', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (291, 290, 'content.Announce/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (292, 290, 'content.Announce/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (293, 290, 'content.Announce/delete', '操作-删除', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (294, 290, 'content.Announce/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (295, 290, 'content.Announce/export', '操作-导出', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (296, 290, 'content.Announce/choose', '操作-选择', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (297, 290, 'content.Announce/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (298, 5, 'admin.Theme/index', '模板管理', 1, 1, '', 0, 1, 'fa fa-box', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (299, 298, 'admin.Theme/config', '模板配置', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (300, 298, 'admin.Theme/install', '安装模板', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (301, 298, 'admin.Theme/uninstall', '卸载模板', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (302, 298, 'admin.Theme/state', '操作-状态', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (303, 201, 'content.Column/approval', '操作-审核通过', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (304, 201, 'content.Column/decline', '操作-拒绝审核', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (305, 192, 'content.Article/manager', '操作-更多', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (306, 183, 'content.Question/manager', '操作-更多', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (307, 3, 'member.NotifySetting/index', '通知配置', 1, 1, '', 0, 1, 'fa fa-bell', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (308, 307, 'member.NotifySetting/add', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (309, 307, 'member.NotifySetting/edit', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (310, 307, 'member.NotifySetting/group_add', '操作-添加分组', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (311, 307, 'member.NotifySetting/state', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (312, 307, 'member.NotifySetting/group', '通知分组', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (313, 307, 'member.NotifySetting/sort', '操作-排序', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (314, 307, 'member.NotifySetting/group_edit', '操作-编辑分组', 1, 0, '', 3, 1, 'fas fa-cogs', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (315, 171, 'member.Verify/type', '认证类型', 1, 1, '', 10, 1, 'far fa-check-square', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (316, 173, 'member.Verify/delete_field', '操作-删除字段', 1, 0, '', 10, 1, 'far fa-check-square', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (317, 315, 'member.Verify/add_type', '操作-添加', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (318, 315, 'member.Verify/edit_type', '操作-编辑', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (319, 315, 'member.Verify/delete_type', '操作-删除类型', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (320, 315, 'member.Verify/state_type', '操作-状态', 1, 0, '', 0, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (321, 3, 'user_group', '用户组管理', 1, 1, '', 50, 1, 'fa fa-user-friends', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (322, 3, 'integral', '积分管理', 1, 1, '', 50, 1, 'fa fa-database', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (323, 3, 'user_action', '行为管理', 1, 1, '', 50, 1, 'fa fa-bolt', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (324, 3, 'forbidden', '封禁管理', 1, 1, '', 0, 1, 'fa fa-ban', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (325, 324, 'member.Forbidden/ips', '封禁IP', 1, 1, '', 0, 1, 'fa fa-ban', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (326, 4, 'wechat', '微信管理', 1, 1, '', 0, 1, 'fab fa-weixin', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (327, 326, 'wechat.Account/index', '账号管理', 1, 1, '', 0, 1, 'fas fa-users', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (328, 327, 'wechat.Account/add', '操作-添加', 1, 0, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (329, 327, 'wechat.Account/edit', '操作-编辑', 1, 0, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (330, 327, 'wechat.Account/delete', '操作-删除', 1, 0, '', 0, 1, 'fa fa-folder', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (353, 4, 'extend.Token/index', '接口请求', 1, 1, '', 61, 1, 'fa fa-anchor', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (354, 353, 'extend.Token/delete', '操作-删除', 0, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (355, 353, 'extend.Token/add', '操作-添加', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (356, 353, 'extend.Token/edit', '操作-删除', 1, 0, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');

INSERT INTO `aws_admin_auth` VALUES (373, 6, 'content.Help/index', '帮助章节', 1, 1, '', 61, 1, 'icon-help', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (374, 373, 'content.Help/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (375, 373, 'content.Help/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (376, 373, 'content.Help/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (377, 373, 'content.Help/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (378, 373, 'content.Help/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (379, 373, 'content.Help/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (380, 373, 'content.Help/choose', '操作-选择', 1, 0, '', 9, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_admin_auth` VALUES (381, 6, 'content.Feature/index', '专题管理', 1, 1, '', 61, 1, 'icon-help', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (382, 381, 'content.Feature/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (383, 381, 'content.Feature/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (384, 381, 'content.Feature/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (385, 381, 'content.Feature/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (386, 381, 'content.Feature/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (387, 381, 'content.Feature/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (388, 381, 'content.Feature/choose', '操作-选择', 1, 0, '', 9, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_admin_auth` VALUES (389, 2, 'admin.DictType/index', '字典类型', 1, 1, '', 61, 1, 'fa fa-database', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (390, 389, 'admin.DictType/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (391, 389, 'admin.DictType/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (392, 389, 'admin.DictType/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_admin_auth` VALUES (393, 2, 'admin.Dict/index', '字典数据', 1, 1, '', 61, 1, 'fa fa-bezier-curve', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (394, 393, 'admin.Dict/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (395, 393, 'admin.Dict/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` VALUES (396, 393, 'admin.Dict/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');

INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (2, 'extend.RouteRule/index', '路由规则', 1, 1, '', 61, 1, 'fa fa-link', 0, 0, '', 'system');
SET @pid=LAST_INSERT_ID();
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/add', '操作-添加', 1, 0, '', 1, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/edit', '操作-修改', 1, 0, '', 3, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/delete', '操作-删除', 1, 0, '', 5, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/export', '操作-导出', 1, 0, '', 7, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/sort', '操作-排序', 1, 0, '', 8, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/state', '操作-状态', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (@pid, 'extend.RouteRule/choose', '操作-选择', 1, 0, '', 9, 1, '', 0, 0, '', 'system');
INSERT INTO `aws_admin_auth` (`pid`, `name`, `title`, `type`, `status`, `condition`, `sort`, `auth_open`, `icon`, `create_time`, `update_time`, `param`, `group`) VALUES (298, 'admin.Theme/upgrade', '操作-更新配置', 1, 0, '', 50, 1, '', 0, 0, '', 'system');
DROP TABLE IF EXISTS `aws_invitation`;
CREATE TABLE `aws_invitation` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '激活ID',
      `uid` int(11) DEFAULT '0' COMMENT '用户ID',
      `invitation_code` varchar(32) DEFAULT NULL COMMENT '激活码',
      `invitation_email` varchar(255) DEFAULT NULL COMMENT '激活email',
      `active_type` varchar(32) NOT NULL DEFAULT 'link' COMMENT '激活方式',
      `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
      `add_ip` bigint(12) DEFAULT NULL COMMENT '添加IP',
      `active_expire` int(10) NOT NULL DEFAULT 0 COMMENT '激活过期',
      `active_time` int(10) DEFAULT NULL COMMENT '激活时间',
      `active_ip` bigint(12) DEFAULT NULL COMMENT '激活IP',
      `active_status` tinyint(4) DEFAULT '0' COMMENT '1-未使用2-已使用',
      `active_uid` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`) USING BTREE,
      KEY `uid` (`uid`) USING BTREE,
      KEY `active_type` (`active_type`) USING BTREE,
      KEY `invitation_code` (`invitation_code`) USING BTREE,
      KEY `invitation_email` (`invitation_email`) USING BTREE,
      KEY `active_time` (`active_time`) USING BTREE,
      KEY `active_ip` (`active_ip`) USING BTREE,
      KEY `active_status` (`active_status`) USING BTREE
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='邀请表';

-- ----------------------------
-- Table structure for aws_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `aws_admin_group`;
CREATE TABLE `aws_admin_group`  (
    `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `title` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组名称',
    `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
    `rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '后台用户组拥有的规则id， 多个规则\",\"隔开',
    `permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '分组权限',
    `system` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '1系统内置',
     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC COMMENT='权限分组表';

-- ----------------------------
-- Records of aws_admin_group
-- ----------------------------
INSERT INTO `aws_admin_group` (`id`, `title`, `status`, `rules`, `permission`, `system`) VALUES (1, '超级管理员', 1, '*', '{"visit_website":"Y","publish_question_enable":"Y","publish_question_approval":"Y","publish_article_enable":"Y","publish_article_approval":"N","publish_answer_enable":"Y","publish_answer_approval":"N","modify_answer_approval":"N","modify_article_approval":"N","modify_question_approval":"N","available_invite_count":"5","create_topic_enable":"Y","publish_approval_time_start":"","publish_approval_time_end":"","publish_url":"Y","publish_question_num":"100","publish_article_num":"100","topic_manager":"Y","modify_article":"Y","remove_article":"Y","modify_question":"Y","remove_question":"Y","set_best_answer":"Y","edit_content_topic":"Y","recommend_post":"Y","set_top_post":"Y","modify_answer":"Y","remove_answer":"Y","lock_topic":"Y","remove_topic":"Y"}', 1);
INSERT INTO `aws_admin_group` (`id`, `title`, `status`, `rules`, `permission`, `system`) VALUES (2, '前台管理员', 1, '1,8,9,10,11,243,7,42,43,44,45,46,47,48,49,6,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,224,223,225,226,227,228,230,229,231,232,234,233,235,244,245,246,247,248,249,250,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,50,52,59,61,64,68,70,142,144,147,171,173,178,158,159,169,168,77,78,82,79,83,80,84,5,101,102,103,104,106,107,109,118,119,120,124,125,121,122,123', '{"visit_website":"Y","publish_question_enable":"Y","publish_question_approval":"N","publish_article_enable":"Y","publish_article_approval":"N","publish_answer_enable":"Y","publish_answer_approval":"N","modify_answer_approval":"N","modify_article_approval":"N","modify_question_approval":"N","available_invite_count":"5","create_topic_enable":"Y","publish_approval_time_start":"","publish_approval_time_end":"","publish_url":"Y","publish_question_num":"100","publish_article_num":"100","topic_manager":"Y","modify_article":"Y","remove_article":"Y","modify_question":"Y","remove_question":"Y","set_best_answer":"Y","edit_content_topic":"Y","recommend_post":"Y","set_top_post":"Y","modify_answer":"Y","remove_answer":"Y","lock_topic":"Y","remove_topic":"Y"}', 1);
INSERT INTO `aws_admin_group` (`id`, `title`, `status`, `rules`, `permission`, `system`) VALUES (3, '未验证用户', 1, '0', '{"visit_website":"Y","publish_question_enable":"N","publish_question_approval":"Y","publish_article_enable":"N","publish_article_approval":"Y","publish_answer_enable":"N","publish_answer_approval":"Y","modify_answer_approval":"Y","modify_article_approval":"Y","modify_question_approval":"Y","available_invite_count":"5","create_topic_enable":"N","publish_approval_time_start":"","publish_approval_time_end":"","publish_url":"N","publish_question_num":"3","publish_article_num":"3","topic_manager":"N","modify_article":"N","remove_article":"N","modify_question":"N","remove_question":"N","set_best_answer":"N","edit_content_topic":"N","recommend_post":"N","set_top_post":"N","modify_answer":"N","remove_answer":"N","lock_topic":"N","remove_topic":"N"}', 1);
INSERT INTO `aws_admin_group` (`id`, `title`, `status`, `rules`, `permission`, `system`) VALUES (4, '普通用户', 1, '0', '{\"visit_website\":\"Y\",\"publish_question_enable\":\"Y\",\"publish_question_approval\":\"N\",\"publish_article_enable\":\"Y\",\"publish_article_approval\":\"N\",\"publish_answer_enable\":\"N\",\"publish_answer_approval\":\"N\",\"modify_answer_approval\":\"N\",\"modify_article_approval\":\"N\",\"modify_question_approval\":\"N\",\"available_invite_count\":\"5\",\"create_topic_enable\":\"N\",\"publish_approval_time_start\":\"\",\"publish_approval_time_end\":\"\",\"publish_url\":\"Y\",\"publish_question_num\":\"3\",\"publish_article_num\":\"3\",\"topic_manager\":\"N\",\"modify_article\":\"Y\",\"remove_article\":\"Y\",\"modify_question\":\"\",\"remove_question\":\"Y\",\"set_best_answer\":\"Y\",\"edit_content_topic\":\"Y\",\"recommend_content\":\"Y\",\"recommend_post\":\"Y\",\"set_top_post\":\"Y\",\"modify_answer\":\"Y\",\"remove_answer\":\"Y\",\"lock_topic\":\"Y\",\"remove_topic\":\"Y\"}', 1);
INSERT INTO `aws_admin_group` (`id`, `title`, `status`, `rules`, `permission`, `system`) VALUES (5, '游客', 1, '0', '{"visit_website":"Y"}', 1);

-- ----------------------------
-- Table structure for aws_attach
-- ----------------------------
DROP TABLE IF EXISTS `aws_attach`;
CREATE TABLE `aws_attach` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户表id',
      `name` varchar(255) DEFAULT NULL COMMENT '文件名',
      `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
      `path` varchar(255) DEFAULT NULL COMMENT '路径',
      `url` varchar(255) DEFAULT NULL COMMENT '完整地址',
      `ext` varchar(5) DEFAULT NULL COMMENT '后缀',
      `size` int(11) DEFAULT '0' COMMENT '大小',
      `width` varchar(30) DEFAULT '0' COMMENT '宽度',
      `height` varchar(30) DEFAULT '0' COMMENT '高度',
      `md5` char(32) DEFAULT NULL,
      `sha1` varchar(64) DEFAULT NULL,
      `mime` varchar(80) DEFAULT NULL,
      `driver` varchar(20) DEFAULT 'local',
      `status` tinyint(1) NOT NULL DEFAULT '1',
      `sort` int(5) NOT NULL DEFAULT '50',
      `item_type` varchar(255) DEFAULT NULL COMMENT '内容类型',
      `item_id` int(10) unsigned DEFAULT '0' COMMENT '内容ID',
      `access_key` varchar(255) DEFAULT NULL COMMENT '批次ID',
      `extends` text COMMENT '附属字段',
      `create_time` int(11) DEFAULT NULL,
      `update_time` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='附件表';

-- ----------------------------
-- Table structure for aws_config
-- ----------------------------
DROP TABLE IF EXISTS `aws_config`;
CREATE TABLE `aws_config` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量名',
      `group` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分组',
      `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量标题',
      `tips` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量描述',
      `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
      `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量值',
      `option` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量字典数据',
      `sort` int(10) unsigned NOT NULL DEFAULT '50' COMMENT '排序值,数字越小越靠前',
      `settings` text COLLATE utf8mb4_unicode_ci COMMENT '附属信息',
      `system` tinyint(1) unsigned DEFAULT '0' COMMENT '系统配置',
      `source` tinyint(1) UNSIGNED NULL DEFAULT '0' COMMENT '配置来源',
      `dict_code` int(10) UNSIGNED NULL DEFAULT '0' COMMENT '字典数据',
      PRIMARY KEY (`id`) USING BTREE,
      UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC COMMENT='系统配置';

-- ----------------------------
-- Records of aws_config
-- ----------------------------
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (1, 'site_name', '1', '站点名称', '请填写站点名称', 'text', 'WeCenter', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (2, 'site_logo', '1', '网站logo', '请上传网站LOGO', 'image', '/static/common/image/logo.png', 'null', 2, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (3, 'site_close', '1', '关闭站点', '开启或关闭站点', 'radio', 'N', '{\"Y\":\"关闭\",\"N\":\"开启\"}', 2, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (4, 'icp', '1', '备案号', '输入网站备案号', 'text', '', 'null', 3, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (5, 'email_enable', '3', '邮箱功能', '', 'radio', 'N', '{\"Y\":\"开启\",\"N\":\"关闭\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (6, 'email_host', '3', 'SMTP地址', '设置SMTP服务地址,如smtp.mail.com', 'text', '', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (7, 'email_username', '3', '邮箱用户名', '邮箱用户名,如admin@admin.com', 'text', '', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (8, 'email_password', '3', '邮箱密码', '邮箱密码', 'password', '', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (9, 'email_secure', '3', '安全链接(SSL)', '验证模式', 'radio', 'tls', '{\"tls\":\"否\",\"ssl\":\"是\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (10, 'upload_file_size', '4', '文件限制', '单位：KB，0表示不限制上传大小,直接填写数字,不需要带单位', 'text', '0', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (11, 'upload_file_ext', '4', '文件格式', '多个格式请用英文逗号（,）隔开 ', 'text', 'rar,zip,avi,rmvb,3gp,flv,mp3,mp4,txt,doc,xls,ppt,pdf,xls,docx,xlsx,doc', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (12, 'upload_image_size', '4', '图片限制', '单位：KB，0表示不限制上传大小,直接填写数字,不需要带单位', 'text', '0', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (13, 'upload_image_ext', '4', '图片格式', '多个格式请用英文逗号（,）隔开 ', 'text', 'jpg,png,gif,jpeg,ico', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (14, 'admin_menu_type', '1', '菜单类型', '后台菜单类型，侧边或顶部显示', 'radio', 'top', '{\"top\":\"顶部\",\"left\":\"侧边\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (15, 'seo_title', '1', '站点标题', '站点标题', 'text', 'WeCenter 社交化知识问答社区程序', 'null', 1, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (16, 'seo_keywords', '1', '站点关键词', '站点关键词', 'textarea', 'WeCenter,知识社区,企业社区,社交问答,问答社区,企业社交,开源社区程序,社交社区程序,开源问答程序,问答网站,social question,问答系统,微信公众平台,企业知识社区,微信开发社区,企业知识库', 'null', 1, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (17, 'seo_description', '1', '站点描述', '站点描述', 'textarea', 'WeCenter 是一款知识型的社交化问答社区程序，专注于社区内容的整理、归类和检索，并通过连接微信公众平台，移动APP进行内容分发。', 'null', 1, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (18, 'authorize_code', '1', '授权码', '授权码', 'password', '', 'null', 1, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (19, 'register_valid_type', '5', '验证类型', '新用户注册验证类型', 'radio', 'email', '{\"email\":\"邮箱验证\",\"admin\":\"后台审核\",\"N\":\"不验证\"}', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (20, 'register_type', '5', '注册类型', '注册类型', 'radio', 'email', '{\"email\":\"邮箱注册\",\"mobile\":\"手机注册\",\"all\":\"手机+邮箱注册\",\"close\":\"关闭注册\"}', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (21, 'password_min_length', '5', '密码最小长度', '密码最小长度', 'number', '6', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (22, 'password_max_length', '5', '密码最大长度', '密码最大长度', 'number', '18', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (23, 'password_type', '5', '密码类型', '密码类型，密码必须是选择的类型才可使用，不选则不限制', 'checkbox', '', '{\"number\":\"数字\",\"special\":\"特殊字符\",\"letter\":\"字母\"}', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (24, 'username_min_length', '5', '用户名最小长度', '用户名最小长度', 'number', '4', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (25, 'username_max_length', '5', '用户名最大长度', '用户名最大长度', 'number', '10', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (26, 'errors_exceeds_limit_password', '5', '密码最大重试次数', '密码最大重试次数', 'number', '3', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (27, 'password_error_limit_time', '5', '密码错误限制时长', '密码错误限制时长', 'number', '10', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (28, 'register_agreement', '5', '注册协议', '', 'textarea', '当您申请用户时，表示您已经同意遵守本规章。\r\n欢迎您加入本站点参与交流和讨论，本站点为社区，为维护网上公共秩序和社会稳定，请您自觉遵守以下条款：\r\n\r\n一、不得利用本站危害国家安全、泄露国家秘密，不得侵犯国家社会集体的和公民的合法权益，不得利用本站制作、复制和传播下列信息：\r\n　（一）煽动抗拒、破坏宪法和法律、行政法规实施的；\r\n　（二）煽动颠覆国家政权，推翻社会主义制度的；\r\n　（三）煽动分裂国家、破坏国家统一的；\r\n　（四）煽动民族仇恨、民族歧视，破坏民族团结的；\r\n　（五）捏造或者歪曲事实，散布谣言，扰乱社会秩序的；\r\n　（六）宣扬封建迷信、淫秽、色情、赌博、暴力、凶杀、恐怖、教唆犯罪的；\r\n　（七）公然侮辱他人或者捏造事实诽谤他人的，或者进行其他恶意攻击的；\r\n　（八）损害国家机关信誉的；\r\n　（九）其他违反宪法和法律行政法规的；\r\n　（十）进行商业广告行为的。\r\n\r\n二、互相尊重，对自己的言论和行为负责。\r\n三、禁止在申请用户时使用相关本站的词汇，或是带有侮辱、毁谤、造谣类的或是有其含义的各种语言进行注册用户，否则我们会将其删除。\r\n四、禁止以任何方式对本站进行各种破坏行为。\r\n五、如果您有违反国家相关法律法规的行为，本站概不负责，您的登录信息均被记录无疑，必要时，我们会向相关的国家管理部门提供此类信息。', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (30, 'enable_category', '7', '启用分类', '', 'radio', 'Y', '{\"Y\":\"启用\",\"N\":\"禁用\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (31, 'power_agree_factor', '6', '赞同系数', '赞同系数', 'number', '3', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (32, 'power_against_factor', '6', '反对系数', '赞同系数', 'number', '2', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (33, 'power_best_answer_factor', '6', '最佳回复系数', '', 'number', '5', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (34, 'popular_gravity', '6', '热门-刷新速度', '内容变得不再热门的速度，热门刷新速度越大，一个内容刷新的就越快', 'number', '1.5', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (35, 'popular_agree_ratio', '6', '热门-赞同比例', '计算热门时点赞数所占比例', 'number', '2', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (36, 'popular_against_ratio', '6', '热门-反对比例', '计算热门时反对数所占比例', 'number', '1', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (37, 'popular_view_ratio', '6', '热门-浏览比例', '计算热门时浏览数所占比例', 'number', '2', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (38, 'popular_comment_ratio', '6', '热门-评论比例', '计算热门时评论/回复数所占比例', 'number', '2', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (39, 'popular_quality_init_value', '6', '热门-初始质量', '内容初始质量', 'number', '1', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (40, 'uninterested_power_factor', '6', '不感兴趣威望系数', '不感兴趣威望系数', 'number', '3', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (41, 'thanks_power_factor', '6', '感谢威望系数', '感谢威望系数', 'number', '5', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (42, 'verify_user_power_factor', '6', '认证会员赞踩系数', '认证会员赞踩系数', 'number', '3', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (43, 'publish_user_power_factor', '6', '提问者赞踩系数', '提问者赞踩系数', 'number', '2', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (44, 'power_log_factor', '6', '威望对底系数', '威望对底系数', 'number', '2', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (45, 'mobile_enable', '1', '开启手机端', '开启手机端', 'radio', 'N', '{\"N\":\"关闭\",\"Y\":\"开启\"}', 5, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (46, 'cdn_url', '4', '资源URL', '默认为空，代表当前服务器，其他服务器填写域名或IP,不带/', 'text', '', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (47, 'upload_type', '4', '上传方式', '默认为TP上传', 'radio', 'tp', '{\"tp\":\"默认方式\",\"big\":\"分片上传\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (48, 'footer_html', '1', '公共底部代码', '', 'textarea', '', 'null', 53, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (49, 'header_html', '1', '公共头部代码', '', 'textarea', '', 'null', 52, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (50, 'online_check', '5', '在线检测', '在线检测用户在线状态', 'radio', 'N', '{\"N\":\"关闭\",\"Y\":\"开启\"}', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (51, 'online_check_time', '5', '检测间隔', '在线检测用户在线状态时间间隔，单位（分钟）', 'number', '5', 'null', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (55, 'sensitive_words', '7', '敏感词列表', '内容中如出现敏感词将进入审核，支持普通字符串和正则表达式，每行一个。正则表达式请以 <code>{</code> 开始、以 <code>}</code> 结束，且需符合 <a href=\"http://cn2.php.net/manual/zh/pcre.pattern.php\" target=\"_blank\">PCRE 模式</a>，如 <code>{/敏s*感s*词/i}</code>。', 'textarea', '江泽民\r\n胡锦涛\r\n温家宝\r\n周永康\r\n习近平\r\n李克强\r\n假证\r\n假證\r\n办理\r\n辦理\r\n8759\r\n地铁购\r\n发票\r\n{/(?:办|辦).*(?:证|證)/}\r\n赌博\r\n网赌\r\n金花\r\n牛牛\r\n提款\r\n央视新闻\r\n手机搜狐网\r\n倍投\r\n金牌团队\r\n万人推荐\r\n导师稳带\r\n快三\r\n彩票\r\n回本', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (56, 'url_rewrite_enable', '1', '启用URL重写', '是否启用URL重写', 'radio', 'N', '{\"N\":\"关闭\",\"Y\":\"开启\"}', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (57, 'email_port', '3', '邮箱端口', '留空时默认服务器端口为 25，使用 SSL 协议默认端口为 465，详细参数请询问邮箱服务商', 'text', '25', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (58, 'email_from', '3', '显示来源', '请保持和邮箱用户名同一主域下', 'text', 'user@example.com', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (59, 'email_show_name', '3', '来源名称', '显示来源名称', 'text', 'WeCenter官方团队', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (60, 'score_unit', '6', '积分单位', '前台积分单位', 'text', '积分', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (61, 'power_unit', '6', '威望单位', '前台威望单位', 'text', '威望', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (62, 'search_handle', '10', '搜索引擎', '搜索引擎选择,未安装其他搜索引擎时默认为本地数据库搜索', 'radio', 'regexp', '{\"regexp\":\"系统默认\",\"ElasticSearch\":\"ElasticSearch\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (63, 'remove_xss', '7', '启用XSS过滤', '启用XSS过滤可能会导致某些内容被过滤', 'radio', 'N', '{\"Y\":\"启用\",\"N\":\"禁用\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (64, 'date_friendly_enable', '7', '时间格式', '', 'radio', 'friendly', '{\"friendly\":\"友好格式\",\"normal\":\"标准格式\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (66, 'topic_enable', '9', '发布内容必选话题', '发布内容(问题、文章)时是否必选话题', 'radio', 'Y', '{\"Y\":\"是\",\"N\":\"否\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (67, 'download_local_enable', '4', '本地化图片', '远程图片本地化开关', 'radio', 'N', '{\"Y\":\"开启\",\"N\":\"关闭\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (68, 'record_admin_log', '7', '记录管理员日志', '记录管理员后台操作日志', 'radio', 'N', '{\"Y\":\"开启\",\"N\":\"关闭\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (69, 'open_content_enable', '9', '默认展开内容', '默认是否显示全部内容', 'radio', 'Y', '{\"Y\":\"开启\",\"N\":\"关闭\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (70, 'cache_explore_time', '8', '首页缓存周期', '首页数据缓存周期0代表不缓存，单位：分钟', 'number', '0', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (71, 'cache_list_time', '8', '列表缓存周期', '问题/文章列表数据缓存周期0代表不缓存，单位：分钟', 'number', '0', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (72, 'contents_per_page', '9', '内容显示条数', '通用内容列表显示条数', 'number', '15', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (73, 'publish_content_verify_time', '9', '发文验证周期', 'xx分钟内连续发文(问题/文章/回答)开启填写验证码，0代表不验证；单位：分钟', 'number', '0', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (74, 'publish_content_verify_num', '9', '发文验证', '连续发文多少次开启填写验证码', 'number', '5', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (75, 'notify_type', '10', '通知类型', '系统通知类型', 'checkbox', 'site,email', '{\"site\":\"站内通知\",\"email\":\"邮件通知\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (76, 'enable_frontend_captcha', '7', '启用前台验证码', '是否启用前台登录验证码', 'radio', 'N', '{\"Y\":\"启用\",\"N\":\"禁用\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (77, 'enable_backend_captcha', '7', '启用后台验证码', '是否启用后台登录验证码', 'radio', 'N', '{\"Y\":\"启用\",\"N\":\"禁用\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (79, 'cache_relation_list_time', '8', '相关数据缓存', '问题/文章详情页相关数据/推荐内容缓存周期0代表不缓存，单位：分钟', 'number', '0', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (80, 'unique_login', '5', '单一登录', '只允许同一个用户相同ip相同浏览器登录', 'radio', 'N', '{\"N\":\"关闭\",\"Y\":\"开启\"}', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (81, 'auto_save_draft', '7', '自动保存草稿', '是否开启自动保存草稿', 'radio', 'N', '{\"N\":\"关闭\",\"Y\":\"开启\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (82, 'auto_save_draft_time', '7', '自动保存时间', '草稿自动保存间隔时间,单位：秒', 'number', '30', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (83, 'search_engine_user', '10', '搜索引擎用户名', '搜索引擎用户名,没有请留空', 'text', '', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (84, 'search_engine_password', '10', '搜索引擎密码', '搜索引擎密码,没有请留空', 'text', '', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (85, 'search_engine_host', '10', '搜索引擎地址', '搜索引擎服务地址,请填写完整的服务器地址，包括端口等,如es搜索引擎需要配置', 'text', '', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (86, 'search_engine_app', '10', '搜索引擎项目名称', '搜索引擎项目名称,如es搜索引擎需要配置', 'text', '', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (87, 'answer_unique', '9', '用户对每个问题的回复限制', '用户对每个问题的回复限制', 'radio', 'Y', '{\"Y\":\"只允许回复一次\",\"N\":\"不限制\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (88, 'frontend_group_type', '5', '前台用户组', '选择前台默认使用的用户组', 'radio', 'reputation', '{\"reputation\":\"威望组\",\"integral\":\"积分组\"}', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (89, 'invitation_expire_time', '5', '注册邀请码有效期', '注册邀请时邀请码过期时间,单位:小时', 'number', '72', '[]', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (90, 'reputation_calc_time', '6', '威望计算周期', '威望计算周期,单位:天', 'number', '3', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (91, 'show_answer_user_ip', '9', '显示回答用户IP地址', '是否显示回答用户IP地址', 'radio', 'Y', '{\"Y\":\"显示\",\"N\":\"不显示\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (92, 'invite_register_enable', '5', '开启邀请注册', '是否开启邀请注册', 'radio', 'N', '{\"Y\":\"开启\",\"N\":\"关闭\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (93, 'enable_anonymous', '7', '是否允许匿名', '是否允许匿名提问/回答问题', 'radio', 'N', '{\"Y\":\"允许\",\"N\":\"不允许\"}', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (94, 'wechat_enable', '1', '开启微信端', '是否开启微信端,开启后在微信端会访问独立的微信端业务逻辑,若没有独立业务逻辑，请勿开启', 'radio', 'N', '{\"N\":\"关闭\",\"Y\":\"开启\"}', 5, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (95, 'remember_login_time', '5', '记住登录有效期', '记住登录有效期，单位：天', 'number', '7', 'null', 50, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (96, 'email_test', '3', '发送测试邮件', '', 'html', '&lt;a class=&quot;btn btn-primary aw-ajax-open&quot; data-url=&quot;index/send_test_email&quot; data-title=&quot;发送测试邮件&quot;&gt;发送测试邮件&lt;/a&gt;', '', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (97, 'report_bug_email', '1', 'BUG反馈接收提醒邮箱', 'BUG反馈接收提醒邮箱', 'text', '', '', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (98, 'max_topic_select', '9', '话题最多选择数量', '话题最多选择数量', 'number', '5', '', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (99, 'cache_type', '8', '缓存方式', '选择缓存方式,当选择非文件缓存时，请点击下方“检查并配置缓存链接”', 'radio', 'file', '{"file":"文件缓存","redis":"Redis缓存","memcached":"Memcached缓存","memcache":"Memcache缓存"}', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (100, 'cache_type_test', '8', '检查并配置缓存链接', '', 'html', '&lt;a class=&quot;btn btn-primary aw-ajax-open&quot; data-url=&quot;index/cache_type_check&quot; data-title=&quot;检查缓存状态&quot;&gt;检查缓存状态&lt;/a&gt;', '', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (101, 'cache_host', '8', '链接地址', '链接地址默认127.0.0.1', 'hidden', '127.0.0.1', '', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (102, 'cache_port', '8', '链接端口', '链接端口', 'hidden', '', '', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (103, 'cache_password', '8', '链接密码', '链接密码', 'hidden', '', '', 51, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (104, 'pc_host', '1', 'PC端域名', '若开启手机端,PC端域名和手机端域名必须同时填写手机域名才生效,格式为www.xxx.com;不带http://或https://', 'text', '', '', 6, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (105, 'mobile_host', '1', '手机端域名', '若开启手机端,PC端域名和手机端域名必须同时填写手机域名才生效,格式为m.xxx.com;不带http://或https://', 'text', '', '', 7, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (106, 'pjax_enable', '1', '是否启用pjax', '网站是否启用pjax请求', 'radio', 'Y', '{"Y":"启用","N":"不启用"}', 50, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (107, 'cron_enable', '1', '是否启用网页定时任务', '是否启用网页定时任务,若服务器不允许exec函数或则未启用拓展->定时任务,可开启网页定时任务,建议使用命令行定时任务效率更高', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 50, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (108, 'answer_sort_type', '9', '默认回答排序', '选择默认回答排序方式', 'radio', 'new', '{"new":"最新排序","hot":"热门排序","publish":"只看楼主","focus":"关注的人"}', 50, '', 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (109, 'reputation_calc_limit', '6', '威望每次计算条数', '威望每次计算用户条数', 'number', '200', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (110, 'auto_question_lock_day', '9', '自动锁定问题天数', '自动锁定问题天数 0 代表不自动锁定，单位：天', 'number', '60', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (111, 'auto_set_best_answer_day', '9', '自动设定最佳回答天数', '自动设定最佳回答天数 0 代表不自动设定，单位：天', 'number', '7', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`id`, `name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES (112, 'best_answer_min_count', '9', '自动设定最佳回答时该问题最小回答数', '自动设定最佳回答时该问题最小回答数', 'number', '1', '[]', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('enable_multilingual', '7', '启用多语言', '启用前台多语言', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('visitor_view_answer_count', '5', '游客可浏览回答数量', '游客可浏览回答数量，0代表不限制', 'number', '0', '', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('uninterested_fold', '9', '“不感兴趣”数量达到多少个时自动折叠回复', '“不感兴趣”数量达到多少个时自动折叠回复', 'number', '10', '', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('remember_login_enable', '5', '是否启用记住登录状态', '是否启用记住登录状态,为了防止用户通过保存cookie实现自动登录，建议不启用', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 50, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`, `source`, `dict_code`) VALUES ('default_language', '7', '默认站点语言', '站点默认使用语言', 'radio', 'zh-cn', '[]', 50, '', 0, 1, 5);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('best_agree_min_count', '9', '自动设定最佳回答时该回答至少获赞数', '自动设定最佳回答时该回答至少获赞数', 'number', '3', '', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('content_popular_value_show', '9', '热度值大于多少参与热度排序', '热度值达到多少参与热度排序,默认大于0即参与热门排序', 'number', '0', '', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('upload_image_thumb_enable', '4', '是否启用图片压缩', '是否启用图片压缩，PNG图片压缩会失去透明效果', 'radio', 'N', '{"Y":"启用","N":"不启用"}', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`) VALUES ('upload_image_thumb_percent', '4', '上传图片默认压缩比例', '默认为0.7', 'text', '0.7', 'null', 0, NULL, 1);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`, `source`, `dict_code`) VALUES ('db_version', '1', '数据库版本', '', 'hidden', '410', 'null', 0, NULL, 1, 0, 0);
INSERT INTO `aws_config` (`name`, `group`, `title`, `tips`, `type`, `value`, `option`, `sort`, `settings`, `system`, `source`, `dict_code`) VALUES ('local_upgrade_enable', '1', '启用本地升级', '升级完成后建议关闭', 'radio', 'Y', '{"Y":"启用","N":"不启用"}', 0, NULL, 1, 0, 0);

-- ----------------------------
-- Table structure for aws_config_group
-- ----------------------------
DROP TABLE IF EXISTS `aws_config_group`;
CREATE TABLE `aws_config_group`  (
     `id` int(8) NOT NULL AUTO_INCREMENT,
     `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分组名称',
     `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
     `sort` mediumint(8) NULL DEFAULT 50 COMMENT '排序',
     `status` int(1) NULL DEFAULT 0 COMMENT '状态（1 正常，0 锁定）',
     `create_time` int(11) NULL DEFAULT 0 COMMENT '添加时间',
     `update_time` int(11) NULL DEFAULT 0 COMMENT '修改时间',
     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统配置分组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of aws_config_group
-- ----------------------------
INSERT INTO `aws_config_group` VALUES (1, '站点配置', '站点配置', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (3, '邮箱配置', '邮箱配置', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (4, '上传配置', '上传配置', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (5, '注册访问', '注册访问', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (6, '威望规则', '威望规则', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (7, '功能配置', '功能配置', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (8, '优化配置', '优化配置', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (9, '内容配置', '内容配置', 50, 0, 0, 0);
INSERT INTO `aws_config_group` VALUES (10, '其他配置', '其他配置', 50, 0, 0, 0);

-- ----------------------------
-- Table structure for aws_menu_rule
-- ----------------------------
DROP TABLE IF EXISTS `aws_menu_rule`;
CREATE TABLE `aws_menu_rule`  (
      `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
      `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
      `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '控制器/方法',
      `title` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
      `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1站内链接2站外链接',
      `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单状态',
      `sort` mediumint(8) NOT NULL DEFAULT 0 COMMENT '排序',
      `auth_open` tinyint(2) NULL DEFAULT 1,
      `icon` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单图标',
      `param` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '参数',
      `is_home` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为默认首页',
      `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限分组',
      PRIMARY KEY (`id`) USING BTREE,
      UNIQUE INDEX `name`(`name`,`param`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC  COMMENT = '菜单表';

-- ----------------------------
-- Records of aws_menu_rule
-- ----------------------------
INSERT INTO `aws_menu_rule` VALUES (1, 0, 'index/index', '首页', 1, 1,  50, 0, '', '', 1, 'nav');
INSERT INTO `aws_menu_rule` VALUES (2, 0, 'question/index', '问答', 1, 1, 50, 0, '', '', 0, 'nav');
INSERT INTO `aws_menu_rule` VALUES (3, 0, 'article/index', '文章', 1, 1, 50, 0, '', '', 0, 'nav');
INSERT INTO `aws_menu_rule` VALUES (4, 0, 'column/index', '专栏', 1, 1, 50, 0, '', '', 0, 'nav');
INSERT INTO `aws_menu_rule` VALUES (5, 0,  'people/lists', '大咖', 1, 1, 50, 0, '','', 0, 'nav');
INSERT INTO `aws_menu_rule` VALUES (6, 0,  'topic/index', '话题', 1, 1, 50, 0, '','', 0, 'nav');
INSERT INTO `aws_menu_rule` VALUES (7, 0, 'page/index', '关于我们', 1, 1, 50, 0, '', 'url_name=about', 0, 'footer');
INSERT INTO `aws_menu_rule` VALUES (8, 0, 'page/index', '社区规范', 1, 1, 50, 0, '', 'url_name=rule', 0, 'footer');
INSERT INTO `aws_menu_rule` VALUES (9, 0,  'help/index', '帮助', 1, 1, 50, 0, '','', 0, 'nav');
INSERT INTO `aws_menu_rule` VALUES (10, 0,  'feature/index', '专题', 1, 1, 50, 0, '','', 0, 'nav');

DROP TABLE IF EXISTS `aws_users`;
CREATE TABLE `aws_users` (
     `uid` int(11) NOT NULL AUTO_INCREMENT,
     `nick_name` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '昵称',
     `user_name` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '用户名',
     `password` varchar(64) DEFAULT NULL COMMENT '用户密码',
     `salt` varchar(16) DEFAULT NULL COMMENT '用户附加混淆码',
     `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
     `mobile` char(16) DEFAULT NULL COMMENT '手机号',
     `sex` tinyint(1) unsigned DEFAULT '0' COMMENT '0保密1男2女',
     `is_first_login` tinyint(1) unsigned DEFAULT '0' COMMENT '是否首次登陆',
     `inbox_unread` int(10) unsigned DEFAULT '0' COMMENT '未读私信',
     `notify_unread` int(10) unsigned DEFAULT '0' COMMENT '未读通知',
     `fans_count` int(10) unsigned DEFAULT '0' COMMENT '粉丝数量',
     `friend_count` int(10) unsigned DEFAULT '0' COMMENT '关注数量',
     `available_invite_count` int(10) unsigned DEFAULT '0' COMMENT '可用邀请数量',
     `is_valid_email` tinyint(1) unsigned DEFAULT '0' COMMENT '是否验证邮箱',
     `is_valid_mobile` tinyint(1) unsigned DEFAULT '0' COMMENT '是否验证手机号',
     `integral` int(10) DEFAULT '0' COMMENT '积分数量',
     `reputation` int(10) DEFAULT '0' COMMENT '威望值',
     `reputation_update_time` int(10) DEFAULT '0' COMMENT '威望更新时间',
     `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
     `signature` varchar(255) DEFAULT NULL COMMENT '签名',
     `verified` varchar(255) DEFAULT NULL COMMENT '认证类型',
     `group_id` int(10) unsigned DEFAULT '0' COMMENT '系统分组ID',
     `integral_group_id` int(10) unsigned DEFAULT '0' COMMENT '积分分组ID',
     `reputation_group_id` int(10) unsigned DEFAULT '0' COMMENT '威望分组ID',
     `last_login_time` int(10) unsigned DEFAULT '0' COMMENT '最后登录时间',
     `last_login_ip` varchar(50) DEFAULT '' COMMENT '最后登录IP',
     `reg_ip` varchar(50) DEFAULT NULL COMMENT '注册IP',
     `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '用户余额',
     `frozen_money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '冻结金额',
     `deal_password` varchar(64) DEFAULT NULL COMMENT '交易密码',
     `birthday` int(10) DEFAULT '0' COMMENT '生日',
     `url_token` varchar(255) DEFAULT NULL COMMENT '自定义URL',
     `views_count` int(255) unsigned DEFAULT '0' COMMENT '个人主页浏览量',
     `agree_count` int(10) unsigned DEFAULT '0' COMMENT '赞同数量',
     `question_count` int(255) unsigned DEFAULT '0' COMMENT '问题数量',
     `answer_count` int(255) unsigned DEFAULT '0' COMMENT '回答数量',
     `article_count` int(10) unsigned DEFAULT '0' COMMENT '发布文章数量',
     `draft_count` int(10) DEFAULT NULL COMMENT '草稿数量',
     `topic_focus_count` int(10) NOT NULL DEFAULT '0' COMMENT '关注话题数量',
     `column_count` int(10) NOT NULL DEFAULT '0' COMMENT '专栏数量',
     `extend` text DEFAULT NULL COMMENT '拓展信息',
     `forbidden_ip` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1-已封禁ip',
     `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态0已删除1正常2待审核3已封禁',
     `theme` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT '主题',
     `client` varchar(32) NOT NULL DEFAULT 'pc',
     `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
     `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
     PRIMARY KEY (`uid`) USING BTREE,
     UNIQUE KEY `user_name` (`user_name`) USING BTREE,
     UNIQUE KEY `url_token` (`url_token`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户表';
-- ----------------------------
-- Table structure for aws_users_extends
-- ----------------------------
DROP TABLE IF EXISTS `aws_users_extends`;
CREATE TABLE `aws_users_extends`  (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `uid` int(8) UNSIGNED NOT NULL,
      `notify_setting` text COMMENT '通知设置',
      `inbox_setting` varchar(255) DEFAULT NULL COMMENT '私信设置',
      PRIMARY KEY (`id`) USING BTREE,
      UNIQUE INDEX `uid`(`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '用户信息拓展表' ROW_FORMAT = DYNAMIC;

DROP TABLE IF EXISTS `aws_users_reputation_group`;
CREATE TABLE `aws_users_reputation_group`  (
     `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
     `title` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组名称',
     `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
     `permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '分组权限',
     `group_icon` varchar(255) DEFAULT NULL COMMENT '用户组图标',
     `min_reputation` int unsigned DEFAULT '0' COMMENT '最小条件',
     `max_reputation` int unsigned DEFAULT '0' COMMENT '最大条件',
     `reputation_factor` int unsigned DEFAULT '0' COMMENT '威望系数',
     `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
     `system` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '1系统内置',
     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC COMMENT = '用户威望组' ;

INSERT INTO `aws_users_reputation_group` (`id`, `title`, `status`, `permission`, `group_icon`, `min_reputation`, `max_reputation`, `reputation_factor`, `remark`, `system`) VALUES (1, '注册会员', 1, '{\"visit_website\":\"Y\",\"publish_question_enable\":\"Y\",\"publish_question_approval\":\"Y\",\"publish_article_enable\":\"Y\",\"publish_article_approval\":\"Y\",\"publish_answer_enable\":\"Y\",\"publish_answer_approval\":\"Y\",\"modify_answer_approval\":\"Y\",\"modify_article_approval\":\"Y\",\"modify_question_approval\":\"Y\",\"available_invite_count\":\"5\",\"create_topic_enable\":\"N\",\"publish_approval_time_start\":\"23\",\"publish_approval_time_end\":\"5\",\"publish_url\":\"Y\",\"publish_question_num\":\"3\",\"publish_article_num\":\"3\",\"topic_manager\":\"N\"}', NULL, 0, 1, 1, '', 1);
INSERT INTO `aws_users_reputation_group` (`id`, `title`, `status`, `permission`, `group_icon`, `min_reputation`, `max_reputation`, `reputation_factor`, `remark`, `system`) VALUES (2, '初级会员', 1, '{\"visit_website\":\"Y\",\"publish_question_enable\":\"Y\",\"publish_question_approval\":\"N\",\"publish_article_enable\":\"Y\",\"publish_article_approval\":\"N\",\"publish_answer_enable\":\"Y\",\"publish_answer_approval\":\"N\",\"modify_answer_approval\":\"N\",\"modify_article_approval\":\"N\",\"modify_question_approval\":\"N\",\"available_invite_count\":\"5\",\"create_topic_enable\":\"N\",\"publish_approval_time_start\":\"23\",\"publish_approval_time_end\":\"5\",\"publish_url\":\"Y\",\"publish_question_num\":\"3\",\"publish_article_num\":\"3\",\"topic_manager\":\"N\"}', NULL, 1, 10, 1, '', 0);
INSERT INTO `aws_users_reputation_group` (`id`, `title`, `status`, `permission`, `group_icon`, `min_reputation`, `max_reputation`, `reputation_factor`, `remark`, `system`) VALUES (3, '中级会员', 1, '{\"visit_website\":\"Y\",\"publish_question_enable\":\"Y\",\"publish_question_approval\":\"N\",\"publish_article_enable\":\"Y\",\"publish_article_approval\":\"N\",\"publish_answer_enable\":\"Y\",\"publish_answer_approval\":\"N\",\"modify_answer_approval\":\"N\",\"modify_article_approval\":\"N\",\"modify_question_approval\":\"N\",\"available_invite_count\":\"5\",\"create_topic_enable\":\"Y\",\"publish_approval_time_start\":\"23\",\"publish_approval_time_end\":\"5\",\"publish_url\":\"Y\",\"publish_question_num\":\"3\",\"publish_article_num\":\"3\",\"topic_manager\":\"N\"}', NULL, 10, 50, 1, '', 0);
INSERT INTO `aws_users_reputation_group` (`id`, `title`, `status`, `permission`, `group_icon`, `min_reputation`, `max_reputation`, `reputation_factor`, `remark`, `system`) VALUES (4, '高级会员', 1, '{\"visit_website\":\"Y\",\"publish_question_enable\":\"Y\",\"publish_question_approval\":\"N\",\"publish_article_enable\":\"Y\",\"publish_article_approval\":\"N\",\"publish_answer_enable\":\"Y\",\"publish_answer_approval\":\"N\",\"modify_answer_approval\":\"N\",\"modify_article_approval\":\"N\",\"modify_question_approval\":\"N\",\"available_invite_count\":\"5\",\"create_topic_enable\":\"N\",\"publish_approval_time_start\":\"23\",\"publish_approval_time_end\":\"5\",\"publish_url\":\"Y\",\"publish_question_num\":\"3\",\"publish_article_num\":\"3\",\"topic_manager\":\"Y\"}', NULL, 50, 100, 1, '', 0);
INSERT INTO `aws_users_reputation_group` (`id`, `title`, `status`, `permission`, `group_icon`, `min_reputation`, `max_reputation`, `reputation_factor`, `remark`, `system`) VALUES (5, '核心会员', 1, '{\"visit_website\":\"Y\",\"publish_question_enable\":\"Y\",\"publish_question_approval\":\"N\",\"publish_article_enable\":\"Y\",\"publish_article_approval\":\"N\",\"publish_answer_enable\":\"Y\",\"publish_answer_approval\":\"N\",\"modify_answer_approval\":\"N\",\"modify_article_approval\":\"N\",\"modify_question_approval\":\"N\",\"available_invite_count\":\"5\",\"create_topic_enable\":\"Y\",\"publish_approval_time_start\":\"23\",\"publish_approval_time_end\":\"5\",\"publish_url\":\"Y\",\"publish_question_num\":\"3\",\"publish_article_num\":\"3\",\"topic_manager\":\"Y\"}', NULL, 100, 300, 1, '', 0);

DROP TABLE IF EXISTS `aws_users_integral_group`;
CREATE TABLE `aws_users_integral_group`  (
        `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
        `title` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组名称',
        `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
        `permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '分组权限',
        `group_icon` varchar(255) DEFAULT NULL COMMENT '用户组图标',
        `min_integral` int unsigned DEFAULT '0' COMMENT '最小条件',
        `max_integral` int unsigned DEFAULT '0' COMMENT '最大条件',
        `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
        `system` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '修改时间',
        PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC COMMENT = '用户积分组' ;

INSERT INTO `aws_users_integral_group` (`id`, `title`, `status`, `permission`, `group_icon`, `min_integral`, `max_integral`, `remark`, `system`) VALUES (1, '普通用户', 1, '{\"visit_website\":\"Y\",\"publish_question_enable\":\"Y\",\"publish_question_approval\":\"Y\",\"publish_article_enable\":\"N\",\"publish_article_approval\":\"N\",\"publish_answer_enable\":\"N\",\"publish_answer_approval\":\"N\",\"modify_answer_approval\":\"N\",\"modify_article_approval\":\"N\",\"modify_question_approval\":\"N\",\"available_invite_count\":\"5\",\"create_topic_enable\":\"N\",\"publish_approval_time_start\":\"\",\"publish_approval_time_end\":\"\",\"publish_url\":\"Y\",\"publish_question_num\":\"3\",\"publish_article_num\":\"3\",\"topic_manager\":\"N\"}', NULL, 0, 100, '', 1);

DROP TABLE IF EXISTS `aws_users_permission`;
CREATE TABLE `aws_users_permission`  (
     `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
     `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '权限字段',
     `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '权限标题',
     `tips` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '权限描述',
     `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
     `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限值',
     `option` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限字典数据',
     `sort` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
     `extend` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '自定义规则配置',
     `group` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '权限分组',
     PRIMARY KEY (`id`) USING BTREE,
     UNIQUE INDEX `name`(`name`,`group`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户权限配置' ROW_FORMAT = DYNAMIC;

INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('visit_website', '允许浏览网站', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('publish_question_enable', '发布问题', '是否允许发起问题', 'radio', 'Y', '{"N":"否","Y":"是"}', 0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('publish_question_approval', '提问审核', '发起问题时是否需要审核', 'radio', 'Y', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('publish_article_enable', '发起文章', '是否允许发起文章', 'radio', 'N', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('publish_article_approval', '发文审核', '发起文章时是否需要审核', 'radio', 'N', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('publish_answer_enable', '允许回答问题', '是否允许发起回答', 'radio', 'N', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('publish_answer_approval', '回答审核', '发起回答时是否需要审核', 'radio', 'N', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('modify_answer_approval', '修改回答审核', '修改回答时是否需要审核', 'radio', 'N', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('modify_article_approval', '修改文章审核', '修改文章时是否需要审核', 'radio', 'N', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('modify_question_approval', '修改提问审核', '修改提问时是否需要审核', 'radio', 'N', '{"N":"否","Y":"是"}',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('available_invite_count', '可邀请用户数量', '可邀请用户数量', 'number', '5', '',  0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`,`group`) VALUES ('create_topic_enable', '创建话题', '', 'radio', 'N', '{"N":"否","Y":"是"}', 0,'','common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('publish_approval_time_start', '审核开始时间', '审核开始时间(请填写24小时制的标准时间格式,如希望5:30-凌晨23:00点，则开始时间为5:30),0代表全时间,只有开启审核此配置才生效', 'time', 0, '[]', 0, NULL, 'common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('publish_approval_time_end', '审核结束时间', '审核结束时间(请填写24小时制的标准时间格式,如希望5:00-凌晨23:00点，则结束时间为23:00),0代表全时间,只有开启审核此配置才生效', 'time', 0, '[]', 0, NULL, 'common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('publish_url', '发布站外链接', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('publish_question_num', '当天发布问题数量', '当天可发布问题数量', 'text', '3', '[]', 0, NULL, 'common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('publish_article_num', '当天发布文章数量', '当天可发布文章数量', 'text', '3', '[]', 0, NULL, 'common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('topic_manager', '允许编辑话题', '是否允许用户编辑话题', 'radio', 'N', '{"N":"否","Y":"是"}', 0, NULL, 'common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('modify_article', '编辑文章', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('remove_article', '删除文章', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('modify_question', '修改问题', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('remove_question', '删除问题', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('set_best_answer', '设置最佳回复', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('edit_content_topic', '编辑内容话题', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'common');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('recommend_post', '推荐内容', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('set_top_post', '设置置顶内容', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('modify_answer', '编辑回答', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('remove_answer', '删除回答', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('lock_topic', '锁定话题', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('remove_topic', '删除话题', '', 'radio', 'Y', '{"N":"否","Y":"是"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('redirect_question', '允许重定向问题', '', 'radio', 'N', '{\"N\":\"否\",\"Y\":\"是\"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('lock_question', '允许锁定问题', '', 'radio', 'N', '{\"N\":\"否\",\"Y\":\"是\"}', 0, NULL, 'system');
INSERT INTO `aws_users_permission` (`name`, `title`, `tips`, `type`, `value`, `option`, `sort`, `extend`, `group`) VALUES ('merge_topic', '允许合并话题', '', 'radio', 'N', '{\"N\":\"否\",\"Y\":\"是\"}', 0, NULL, 'system');

DROP TABLE IF EXISTS `aws_links`;
CREATE TABLE `aws_links` (
     `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
     `name` varchar(255) NOT NULL DEFAULT '' COMMENT '网站名称',
     `url` varchar(255) NOT NULL DEFAULT '' COMMENT '网站地址',
     `logo` varchar(80) NOT NULL DEFAULT '' COMMENT '网站logo',
     `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
     `sort` int(10) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
     `status` tinyint(10) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
     `create_time` int(11) NOT NULL DEFAULT 0,
     `update_time` int(11) NOT NULL DEFAULT 0,
     PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='友情链接';

INSERT INTO `aws_links` VALUES (1, 'WeCenter社交化问答系统', 'https://wenda.wecenter.com', '/static/common/image/logo.png', 'WeCenter官方社区', 0, 1, 1651830461, 1651888133);

DROP TABLE IF EXISTS `aws_action`;
CREATE TABLE `aws_action` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
      `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
      `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
      `remark` varchar (255) NOT NULL DEFAULT '' COMMENT '行为描述',
      `log_rule` text COMMENT '日志规则',
      `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
      PRIMARY KEY (`id`) USING BTREE,
      UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户行为类型';

INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (1, 'user_login', '用户登录', '用户登录系统','[user] 在 [time] 登录了系统', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (2, 'publish_question', '发起提问', '发起提问','[user] 在 [time] 发起了提问', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (3, 'publish_article', '发表文章', '发表文章','[user] 在 [time] 发表了文章', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (4, 'publish_answer', '发表回答', '发表回答','[user] 在 [time] 回答了问题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (5, 'publish_article_comment', '发表文章评论', '发表文章评论','[user] 在 [time] 评论了文章', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (6, 'agree_question', '点赞问题', '点赞问题','[user] 在 [time] 点赞了问题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (7, 'agree_article', '点赞文章', '点赞文章','[user] 在 [time] 点赞了文章', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (8, 'agree_answer', '点赞回答', '点赞回答','[user] 在 [time] 点赞了回答', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (9, 'focus_user', '关注用户', '关注用户','[user] 在 [time] 关注了用户', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (10, 'focus_question', '关注问题', '关注问题','[user] 在 [time] 关注了问题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (11, 'modify_answer', '修改回答', '修改回答','[user] 在 [time] 修改了回答', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (12, 'modify_question_title', '修改问题标题', '修改问题标题','[user] 在 [time] 修改了问题标题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (13, 'modify_article_title', '修改文章标题', '修改文章标题','[user] 在 [time] 修改了文章标题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (14, 'modify_question_detail', '修改问题描述', '修改问题描述','[user] 在 [time] 修改了问题描述', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (15, 'modify_article_detail', '修改文章详情', '修改文章详情','[user] 在 [time] 修改了文章详情', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (16, 'modify_question_topic', '向话题添加问题', '向话题添加问题','[user] 在 [time] 向话题添加了问题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (17, 'modify_article_topic', '向话题添加文章', '向话题添加文章','[user] 在 [time] 向话题添加了文章', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (18, 'create_topic', '创建话题', '新建话题','[user] 在 [time] 创建了话题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (19, 'focus_column', '关注专栏', '关注专栏','[user] 在 [time] 关注了专栏', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (20, 'focus_topic', '关注话题', '关注话题','[user] 在 [time] 关注了话题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (21, 'focus_favorite', '关注收藏夹', '关注收藏夹','[user] 在 [time] 关注了收藏夹', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (22, 'agree_article_comment', '点赞文章评论', '点赞文章评论','[user] 在 [time] 点赞了文章评论', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (23, 'agree_answer_comment', '点赞回答评论', '点赞回答评论','[user] 在 [time] 点赞了回答评论', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (24, 'create_column_article', '发表专栏文章', '发表专栏文章','[user] [time] 发表了文章,在专栏 [name]', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (25, 'modify_column_article', '修改专栏文章', '修改专栏文章','[user] [time] 修改了文章,在专栏 [name]', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (26, 'publish_column', '新建专栏', '新建专栏','[user] [time] 创建了专栏 [name]', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (27, 'update_topic', '编辑话题', '编辑话题','[user] 在 [time] 编辑了话题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (28, 'lock_topic', '锁定话题', '锁定话题','[user] 在 [time] 锁定了话题', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (29, 'unlock_topic', '取消锁定话题', '取消锁定话题','[user] 在 [time] 取消了话题的锁定', 1);
INSERT INTO `aws_action` (`id`, `name`, `title`, `remark`, `log_rule`, `status`) VALUES (30, 'modify_log', '内容修改记录', '内容修改记录', '[user] 在 [time] 修改了内容', 1);
DROP TABLE IF EXISTS `aws_action_log`;
CREATE TABLE `aws_action_log` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
      `action_id` int(10) NOT NULL DEFAULT '0' COMMENT '行为id',
      `uid` int(10) NOT NULL DEFAULT '0' COMMENT '执行用户id',
      `action_ip` varchar(20) NOT NULL COMMENT '执行行为者ip',
      `record_type` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表类型',
      `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
      `relation_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联数据id',
      `relation_type` varchar(50) NOT NULL DEFAULT '' COMMENT '关联数据类型',
      `anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名',
      `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
      `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `action_ip` (`action_ip`) USING BTREE,
      KEY `action_id` (`action_id`) USING BTREE,
      KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='行为日志表';

DROP TABLE IF EXISTS `aws_action_log_all`;
CREATE TABLE `aws_action_log_all` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) NOT NULL DEFAULT '0' COMMENT '行为id',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` varchar(20) NOT NULL COMMENT '执行行为者ip',
  `record_type` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表类型',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `relation_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联数据id',
  `relation_type` varchar(50) NOT NULL DEFAULT '' COMMENT '关联数据类型',
  `anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名',
  `data` text COMMENT '附加数据',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `action_ip` (`action_ip`) USING BTREE,
  KEY `action_id` (`action_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='行为日志表';

DROP TABLE IF EXISTS `aws_action_log_data`;
CREATE TABLE `aws_action_log_data` (
    `log_id` int(11) unsigned NOT NULL,
    `data` text COMMENT '附加数据',
    `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='行为日志数据';

DROP TABLE IF EXISTS `aws_integral_rule`;
CREATE TABLE `aws_integral_rule` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
      `name` char(100) NOT NULL DEFAULT '' COMMENT '唯一标识',
      `title` varchar(255) NOT NULL DEFAULT '' COMMENT '规则说明',
      `cycle` int(11) NOT NULL DEFAULT '0' COMMENT '执行次数',
      `cycle_type` char(10) NOT NULL DEFAULT '' COMMENT '执行单位;month月,week周,day天,hour小时,minute分钟,second秒数',
      `max` int(10) DEFAULT '0' COMMENT '最大执行次数',
      `integral` int(11) DEFAULT '0' COMMENT '操作积分',
      `log` text COMMENT '日志规则',
      `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
      PRIMARY KEY (`id`) USING BTREE,
      UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='积分规则表';

BEGIN;
INSERT INTO `aws_integral_rule` VALUES (1, 'REGISTER', '用户注册', 1, 'day', 0, 200, '用户注册奖励积分', 1);
INSERT INTO `aws_integral_rule` VALUES (2, 'LOGIN', '每日登录', 1, 'day', 1, 10, '登录系统,获得积分奖励', 1);
INSERT INTO `aws_integral_rule` VALUES (3, 'NEW_QUESTION', '发起提问', 1, 'day', 0, -5, '发表提问,扣除积分 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (4, 'NEW_ARTICLE', '发表文章', 1, 'day', 3, 10, '发表文章,奖励积分奖励 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (5, 'ANSWER_QUESTION', '回答问题', 0, 'day', 0, 5, '回答问题,获得积分奖励 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (6, 'BEST_ANSWER', '被设为最佳回答', 1, 'day', 0, 20, '回答被设为最佳,获得积分奖励 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (7, 'INVITE_ANSWER', '邀请用户回答问题', 1, 'day', 0, -10, '邀请用户回答问题,扣除积分 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (8, 'ANSWER_INVITE', '被邀请用户回答问题', 1, 'day', 0, 10, '被邀请回答问题并回答,获得积分奖励 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (9, 'AWARD', '系统操作', 1, 'day', 0, 0, '系统操作积分 #[time]', 1);
INSERT INTO `aws_integral_rule` VALUES (10, 'QUESTION_THANKS', '感谢问题', 1, 'day', 0, -10, '感谢了问题 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (11, 'THANKS_QUESTION', '问题被感谢', 1, 'day', 0, 10, '问题被感谢 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (12, 'ANSWER_THANKS', '感谢回复', 1, 'day', 0, -10, '感谢了回复 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (13, 'THANKS_ANSWER', '回复被感谢', 1, 'day', 0, 10, '回复被感谢 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (14, 'QUESTION_ANSWER', '问题被回答', 1, 'day', 0, -5, '问题被回答 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (15, 'UPDATE_SIGNATURE', '完善一句话介绍', 1, 'day', 1, 20, '完善一句话介绍', 1);
INSERT INTO `aws_integral_rule` VALUES (16, 'UPLOAD_AVATAR', '上传头像', 1, 'day', 1, 20, '上传头像', 1);
INSERT INTO `aws_integral_rule` VALUES (17, 'UPDATE_CONTACT', '完善联系资料', 1, 'day', 1, 20, '完善联系资料', 1);
INSERT INTO `aws_integral_rule` VALUES (18, 'ANSWER_FOLD', '回复被折叠', 0, 'day', 1, -10, '回复被折叠 #[record]', 1);
INSERT INTO `aws_integral_rule` VALUES (19, 'UPDATE_EDU', '完善教育经历', 1, 'day', 1, 20, '完善教育经历', 1);
INSERT INTO `aws_integral_rule` VALUES (20, 'BIND_OPENID', '绑定第三方账号', 0, 'day', 1, 20, '绑定第三方账号', 1);
INSERT INTO `aws_integral_rule` VALUES (21, 'INVITE_REGISTER', '邀请注册', 0, 'day', 0, 200, '邀请注册成功', 1);
COMMIT;

DROP TABLE IF EXISTS `aws_integral_log`;
CREATE TABLE `aws_integral_log` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     `uid` int(11) DEFAULT '0' COMMENT '触发行为用户id',
     `record_id` char(16) DEFAULT NULL COMMENT '触发行为的数据id',
     `action_type` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '触发行为的类型',
     `integral` int(11) DEFAULT NULL COMMENT '操作积分',
     `remark` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '积分说明',
     `balance` int(11) DEFAULT '0' COMMENT '积分余额',
     `create_time` int(11) DEFAULT '0',
     `record_db` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '记录数据表',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `uid` (`uid`) USING BTREE,
     KEY `action_type` (`action_type`) USING BTREE,
     KEY `create_time` (`create_time`) USING BTREE,
     KEY `integral` (`integral`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='积分记录表';

DROP TABLE IF EXISTS `aws_users_favorite`;
CREATE TABLE `aws_users_favorite` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `uid` int(11) DEFAULT '0',
      `item_id` int(11) unsigned NOT NULL DEFAULT '0',
      `item_type` varchar(16) NOT NULL DEFAULT '',
      `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
      `create_time` int(10) unsigned DEFAULT '0',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `uid` (`uid`) USING BTREE,
      KEY `create_time` (`create_time`) USING BTREE,
      KEY `item_id` (`item_id`) USING BTREE,
      KEY `item_type` (`item_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户收藏表';

DROP TABLE IF EXISTS `aws_users_favorite_tag`;
CREATE TABLE `aws_users_favorite_tag` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户',
      `title` varchar(128) DEFAULT NULL COMMENT '收藏标签',
      `description` varchar(255) NULL COMMENT '收藏夹描述',
      `post_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '内容数',
      `focus_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关注数',
      `comment_count` int(11) NOT NULL DEFAULT '0' COMMENT '评论数',
      `is_public` tinyint(1) unsigned DEFAULT '0' COMMENT '1公开，0私密',
      `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
      `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `uid` (`uid`) USING BTREE,
      KEY `title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户收藏标签表';

DROP TABLE IF EXISTS `aws_users_inbox`;
CREATE TABLE `aws_users_inbox` (
       `id` int(11) NOT NULL AUTO_INCREMENT,
       `uid` int(11) DEFAULT NULL COMMENT '发送者 ID',
       `dialog_id` int(11) DEFAULT NULL COMMENT '对话id',
       `message` text COMMENT '私信内容',
       `sender_remove` tinyint(1) DEFAULT '0' COMMENT '发送者删除消息',
       `recipient_remove` tinyint(1) DEFAULT '0' COMMENT '接受者删除消息',
       `send_time` int(10) DEFAULT '0' COMMENT '发送时间',
       `read_time` int(10) DEFAULT NULL COMMENT '读取时间',
       `status` tinyint(1) UNSIGNED NULL DEFAULT 1,
       PRIMARY KEY (`id`) USING BTREE,
       KEY `dialog_id` (`dialog_id`) USING BTREE,
       KEY `uid` (`uid`) USING BTREE,
       KEY `sender_remove` (`sender_remove`) USING BTREE,
       KEY `recipient_remove` (`recipient_remove`) USING BTREE,
       KEY `send_time` (`send_time`) USING BTREE,
       KEY `read_time` (`read_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户私信表';

DROP TABLE IF EXISTS `aws_users_inbox_dialog`;
CREATE TABLE `aws_users_inbox_dialog` (
      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '对话ID',
      `sender_uid` int(11) DEFAULT NULL COMMENT '发送者UID',
      `sender_unread` int(11) DEFAULT NULL COMMENT '发送者未读',
      `recipient_uid` int(11) DEFAULT NULL COMMENT '接收者UID',
      `recipient_unread` int(11) DEFAULT NULL COMMENT '接收者未读',
      `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
      `update_time` int(11) DEFAULT NULL COMMENT '最后更新时间',
      `sender_count` int(11) DEFAULT NULL COMMENT '发送者显示对话条数',
      `recipient_count` int(11) DEFAULT NULL COMMENT '接收者显示对话条数',
      `status` tinyint(1) UNSIGNED NULL DEFAULT 1,
      PRIMARY KEY (`id`) USING BTREE,
      KEY `recipient_uid` (`recipient_uid`) USING BTREE,
      KEY `sender_uid` (`sender_uid`) USING BTREE,
      KEY `update_time` (`update_time`) USING BTREE,
      KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户私信对话表';

DROP TABLE IF EXISTS `aws_users_notify`;
CREATE TABLE `aws_users_notify` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `sender_uid` int(11) DEFAULT NULL COMMENT '发送者ID',
    `recipient_uid` int(11) DEFAULT '0' COMMENT '接收者ID',
    `action_type` varchar(100) DEFAULT NULL COMMENT '操作类型',
    `model_type` varchar(100) DEFAULT NULL COMMENT '分组类型',
    `item_id` varchar(16) NOT NULL DEFAULT '0' COMMENT '关联ID',
    `item_type` varchar(255) DEFAULT NULL COMMENT '关联类型',
    `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '通知标题',
    `content` text COMMENT '通知内容',
    `anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名',
    `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
    `read_flag` tinyint(1) unsigned DEFAULT '0' COMMENT '阅读状态',
    `status` tinyint(1) unsigned DEFAULT '1' COMMENT '删除状态',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `recipient_read_flag` (`recipient_uid`,`read_flag`) USING BTREE,
    KEY `sender_uid` (`sender_uid`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE,
    KEY `action_type` (`action_type`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统通知';

DROP TABLE IF EXISTS `aws_users_follow`;
CREATE TABLE `aws_users_follow` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `fans_uid` int(11) DEFAULT NULL COMMENT '关注人的UID',
    `friend_uid` int(11) DEFAULT NULL COMMENT '被关注人的uid',
    `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除1正常0删除',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `fans_uid` (`fans_uid`) USING BTREE,
    KEY `friend_uid` (`friend_uid`) USING BTREE,
    KEY `user_follow` (`fans_uid`,`friend_uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户关注表';

DROP TABLE IF EXISTS `aws_users_forbidden`;
CREATE TABLE `aws_users_forbidden` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uid` int unsigned NOT NULL DEFAULT '0',
  `forbidden_time` int unsigned DEFAULT '0' COMMENT '封禁时长',
  `forbidden_reason` varchar(255) DEFAULT NULL COMMENT '封禁原因',
  `status` tinyint unsigned DEFAULT '0' COMMENT '是否删除',
  `create_time` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户封禁记录表';

DROP TABLE IF EXISTS `aws_users_online`;
CREATE TABLE `aws_users_online` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `uid` int(11) NOT NULL COMMENT '用户 ID',
    `last_login_time` int(11) DEFAULT '0' COMMENT '上次活动时间',
    `last_login_ip` varchar(20) DEFAULT '' COMMENT '客户端ip',
    `last_url` varchar(255) DEFAULT NULL COMMENT '停留页面',
    `user_agent` text DEFAULT NULL COMMENT '用户客户端信息',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `last_login_time` (`last_login_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='在线用户列表';

DROP TABLE IF EXISTS `aws_users_token`;
CREATE TABLE `aws_users_token` (
       `token` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token',
       `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
       `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
       `expire_time` int(10) DEFAULT NULL COMMENT '过期时间',
       PRIMARY KEY (`token`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC COMMENT='会员Token表';

DROP TABLE IF EXISTS `aws_approval`;
CREATE TABLE `aws_approval` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `type` varchar(16) DEFAULT NULL COMMENT '审核类型',
    `item_id` int(10) unsigned DEFAULT '0' COMMENT '审核内容ID',
    `data` mediumtext NOT NULL COMMENT '审核数据',
    `uid` int(11) NOT NULL DEFAULT '0',
    `reason` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
    `status` int(11) NOT NULL DEFAULT '0' COMMENT '0待审核,已审核,2已拒绝',
    `access_key` varchar(255) DEFAULT NULL COMMENT '批次ID',
    `create_time` int(10) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `type` (`type`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='审核表';

DROP TABLE IF EXISTS `aws_answer`;
CREATE TABLE IF NOT EXISTS `aws_answer` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '回答id',
    `question_id` int(11) unsigned NOT NULL COMMENT '问题id',
    `content` longtext COMMENT '回答内容',
    `against_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '反对人数',
    `agree_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支持人数',
    `uid` int(11) DEFAULT '0' COMMENT '发布问题用户ID',
    `comment_count` int(11) unsigned DEFAULT '0' COMMENT '评论总数',
    `uninterested_count` int(11) unsigned DEFAULT '0' COMMENT '不感兴趣',
    `thanks_count` int(11) unsigned DEFAULT '0' COMMENT '感谢数量',
    `answer_user_ip` varchar(20) DEFAULT NULL COMMENT '回答用户的来源IP',
    `answer_user_local` varchar(255) DEFAULT NULL COMMENT '回答用户的来源地址',
    `is_anonymous` tinyint(1) unsigned DEFAULT '0' COMMENT '是否匿名回答',
    `publish_source` varchar(16) CHARACTER SET utf8 DEFAULT NULL COMMENT '回答内容来源',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '内容状态',
    `is_best` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为最佳回复1是',
    `best_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定人id',
    `best_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定时间',
    `force_fold` tinyint(1) DEFAULT '0' COMMENT '强制折叠',
    `search_text` text COMMENT '搜索文本',
    `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
    `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
    `create_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
    `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `question_id` (`question_id`) USING BTREE,
    KEY `agree_count` (`agree_count`) USING BTREE,
    KEY `against_count` (`against_count`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `uninterested_count` (`uninterested_count`) USING BTREE,
    KEY `is_anonymous` (`is_anonymous`) USING BTREE,
    KEY `publish_source` (`publish_source`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC COMMENT='问题回答表';

DROP TABLE IF EXISTS `aws_answer_comment`;
CREATE TABLE IF NOT EXISTS `aws_answer_comment` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `pid` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '回复评论的id',
    `answer_id` int(11) DEFAULT '0' COMMENT '问题id',
    `uid` int(11) DEFAULT '0' COMMENT '评论人',
    `message` text COMMENT '评论内容',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除1正常0删除',
    `at_uid` varchar(255) DEFAULT  null COMMENT '被@用户id',
    `create_time` int(10) DEFAULT '0' COMMENT '评论时间',
    `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `answer_id` (`answer_id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='回答评论表';

DROP TABLE IF EXISTS `aws_article`;
CREATE TABLE IF NOT EXISTS `aws_article` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL,
    `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
    `message` longtext CHARACTER SET utf8mb4,
    `comment_count` int(10) DEFAULT '0',
    `view_count` int(10) DEFAULT '0',
    `agree_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞同数总和',
    `against_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对数总和',
    `category_id` int(10) DEFAULT '0',
    `is_recommend` tinyint(1) DEFAULT '0',
    `sort` tinyint(2) unsigned NOT NULL DEFAULT '0',
    `column_id` int(11) DEFAULT NULL COMMENT '所属专栏id',
    `cover` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '文章封面',
    `status` tinyint(1) unsigned DEFAULT '0' COMMENT '文章状态1正常0删除2待审核3待发布',
    `set_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 0不置顶1置顶',
    `set_top_time` int(11) unsigned DEFAULT '0' COMMENT '置顶时间',
    `wait_time` int(10) DEFAULT '0' COMMENT '定时时间',
    `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
    `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
    `user_ip` varchar(20) DEFAULT NULL COMMENT '用户的来源IP',
    `article_type` varchar(50) NOT NULL DEFAULT 'normal' COMMENT 'normal普通文章',
    `extends` text COMMENT '附加信息,用于存储附属信息',
    `search_text` text COMMENT '搜索文本',
    `seo_title` varchar(100) DEFAULT NULL COMMENT 'SEO标题',
    `seo_keywords` varchar(255) DEFAULT NULL COMMENT 'SEO关键词',
    `seo_description` varchar(255) DEFAULT NULL COMMENT 'SEO描述',
    `url_token` varchar(255) DEFAULT NULL COMMENT '自定义URL',
    `create_time` int(10) DEFAULT '0' COMMENT '发布时间',
    `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `comment_count` (`comment_count`) USING BTREE,
    KEY `view_count` (`view_count`) USING BTREE,
    KEY `category_id` (`category_id`) USING BTREE,
    KEY `is_recommend` (`is_recommend`) USING BTREE,
    KEY `sort` (`sort`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='文章表';

DROP TABLE IF EXISTS `aws_article_comment`;
CREATE TABLE IF NOT EXISTS `aws_article_comment` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL,
    `article_id` int(10) NOT NULL,
    `message` text NOT NULL,
    `create_time` int(10) NOT NULL,
    `at_uid` varchar(255) DEFAULT  null COMMENT '被@用户id',
    `pid` int UNSIGNED NULL DEFAULT 0 COMMENT '父级评论',
    `agree_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞同数总和',
    `against_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对数总和',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '内容状态',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `article_id` (`article_id`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='文章评论表';

DROP TABLE IF EXISTS `aws_article_vote`;
CREATE TABLE IF NOT EXISTS `aws_article_vote` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL COMMENT '投票用户',
    `item_type` varchar(16) DEFAULT NULL COMMENT '内容类型',
    `item_id` int(10) NOT NULL COMMENT '内容ID',
    `vote_value` tinyint(1) DEFAULT '0' COMMENT '1赞同,-1反对',
    `create_time` int(10) NOT NULL COMMENT '操作时间',
    `weigh_factor` int(10) DEFAULT '0' COMMENT '赞同反对系数',
    `item_uid` int(10) DEFAULT '0' COMMENT '被投票用户',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `item_type` (`item_type`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE,
    KEY `item_uid` (`item_uid`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='文章赞踩表';

DROP TABLE IF EXISTS `aws_category`;
CREATE TABLE IF NOT EXISTS `aws_category` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(128) DEFAULT NULL COMMENT '分类名称',
    `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
    `type` varchar(16) DEFAULT NULL COMMENT '分类类型',
    `icon` varchar(255) DEFAULT NULL COMMENT '分类图标',
    `pid` int(11) DEFAULT '0' COMMENT '分类父级',
    `sort` smallint(6) DEFAULT '0' COMMENT '分类排序',
    `url_token` varchar(32) DEFAULT NULL COMMENT '分类别名',
    `status` tinyint(1) unsigned DEFAULT '0' COMMENT '分类状态',
    `create_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
    `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `pid` (`pid`) USING BTREE,
    KEY `url_token` (`url_token`) USING BTREE,
    KEY `title` (`title`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='分类表';

INSERT INTO `aws_category` VALUES (1, '问题分类', '','question', NULL, 0, 0, NULL, 1, 0, 0);
INSERT INTO `aws_category` VALUES (2, '文章分类', '','article', NULL, 0, 0, NULL, 1, 0, 0);

DROP TABLE IF EXISTS `aws_column`;
CREATE TABLE IF NOT EXISTS `aws_column` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '专栏id',
    `name` varchar(64) DEFAULT NULL COMMENT '专栏标题',
    `verify` tinyint(1) DEFAULT '0' COMMENT '是否审核通过 （1通过0审核中-1通过）',
    `focus_count` int(11) DEFAULT '0' COMMENT '关注计数',
    `description` text COMMENT '专栏描述',
    `cover` varchar(255) DEFAULT NULL COMMENT '专栏图片',
    `uid` int(11) DEFAULT NULL COMMENT '用户UID',
    `sort` int(10) DEFAULT '0' COMMENT '排序',
    `reason` varchar(100) DEFAULT NULL COMMENT '拒绝原因',
    `recommend` tinyint(1) unsigned DEFAULT '0' COMMENT '是否推荐',
    `view_count` int(10) unsigned DEFAULT '0' COMMENT '专栏浏览数',
    `post_count` int(10) unsigned DEFAULT '0' COMMENT '专栏文章数',
    `join_count` int(10) unsigned DEFAULT '0' COMMENT '专栏签约用户数',
    `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
    `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
    `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
    `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='专栏表';

DROP TABLE IF EXISTS `aws_column_focus`;
CREATE TABLE IF NOT EXISTS `aws_column_focus` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `column_id` int(11) DEFAULT NULL COMMENT '问题ID',
    `uid` int(11) DEFAULT NULL COMMENT '用户UID',
    `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `column_id` (`column_id`) USING BTREE,
    KEY `column_uid` (`column_id`,`uid`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专栏关注表';

DROP TABLE IF EXISTS `aws_draft`;
CREATE TABLE IF NOT EXISTS `aws_draft` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) DEFAULT '0',
    `item_type` varchar(16) DEFAULT NULL,
    `item_id` char(16) NOT NULL,
    `data` longtext,
    `create_time` int(10) DEFAULT '0',
    `update_time` int(10) unsigned DEFAULT '0',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='草稿表';

DROP TABLE IF EXISTS `aws_post_relation`;
CREATE TABLE IF NOT EXISTS `aws_post_relation` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `item_id` int(10) NOT NULL COMMENT '类型ID',
    `item_type` varchar(16) NOT NULL DEFAULT '' COMMENT '内容类型',
    `category_id` int(10) DEFAULT '0' COMMENT '分类ID',
    `is_recommend` tinyint(1) DEFAULT '0' COMMENT '是否推荐',
    `view_count` int(10) DEFAULT '0' COMMENT '浏览量',
    `is_anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名',
    `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
    `uid` int(10) NOT NULL COMMENT '内容uid',
    `agree_count` int(10) DEFAULT '0' COMMENT '赞同数',
    `answer_count` int(10) DEFAULT '0' COMMENT '回答数/评论数',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除1正常0删除',
    `set_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 0不置顶1置顶',
    `set_top_time` int(11) unsigned DEFAULT '0' COMMENT '置顶时间',
    `relation_type` varchar(100) DEFAULT NULL COMMENT '关联类型',
    `relation_id` int(10) UNSIGNED NULL DEFAULT '0' COMMENT '关联类型ID',
    `create_time` int(10) NOT NULL COMMENT '创建时间',
    `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE,
    KEY `item_type` (`item_type`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE,
    KEY `update_time` (`update_time`) USING BTREE,
    KEY `category_id` (`category_id`) USING BTREE,
    KEY `recommend` (`is_recommend`) USING BTREE,
    KEY `anonymous` (`is_anonymous`) USING BTREE,
    KEY `popular_value` (`popular_value`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `agree_count` (`agree_count`) USING BTREE,
    KEY `answer_count` (`answer_count`) USING BTREE,
    KEY `view_count` (`view_count`) USING BTREE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='内容聚合表';

DROP TABLE IF EXISTS `aws_question`;
CREATE TABLE `aws_question` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT '' COMMENT '问题内容',
    `detail` text CHARACTER SET utf8mb4 COMMENT '问题说明',
    `uid` int(11) DEFAULT NULL COMMENT '发布用户UID',
    `answer_count` int(11) NOT NULL DEFAULT '0' COMMENT '回答计数',
    `answer_users` int(11) NOT NULL DEFAULT '0' COMMENT '回答人数',
    `view_count` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
    `focus_count` int(11) NOT NULL DEFAULT '0' COMMENT '关注数',
    `comment_count` int(11) NOT NULL DEFAULT '0' COMMENT '评论数',
    `thanks_count` int(10) DEFAULT '0' COMMENT '感谢数量',
    `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类 ID',
    `user_ip` varchar(20) DEFAULT NULL COMMENT '用户的来源IP',
    `agree_count` int(11) NOT NULL DEFAULT '0' COMMENT '回复赞同数总和',
    `against_count` int(11) NOT NULL DEFAULT '0' COMMENT '回复反对数总和',
    `best_answer` int(11) NOT NULL DEFAULT '0' COMMENT '最佳回复ID',
    `modify_count` int(10) NOT NULL DEFAULT '0' COMMENT '修改次数',
    `last_answer` int(11) NOT NULL DEFAULT '0' COMMENT '最后回答 ID',
    `popular_value` double NOT NULL DEFAULT '0' COMMENT '热度值',
    `popular_value_update` int(10) NOT NULL DEFAULT '0' COMMENT '热度值更新时间',
    `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定',
    `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名提问',
    `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐问题',
    `sort` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '问题状态1正常0删除2待审核',
    `set_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 0不置顶1置顶',
    `set_top_time` int(11) unsigned DEFAULT '0' COMMENT '置顶时间',
    `question_type` varchar(50) NOT NULL DEFAULT 'normal' COMMENT 'normal普通问题',
    `best_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定人id',
    `best_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案的设定时间',
    `seo_title` varchar(100) DEFAULT NULL COMMENT 'SEO标题',
    `seo_keywords` varchar(255) DEFAULT NULL COMMENT 'SEO关键词',
    `seo_description` varchar(255) DEFAULT NULL COMMENT 'SEO描述',
    `url_token` varchar(255) DEFAULT NULL COMMENT '自定义URL',
    `extends` text COMMENT '附加信息,用于存储附属信息',
    `search_text` text COMMENT '搜索文本',
    `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `category_id` (`category_id`) USING BTREE,
    KEY `update_time` (`update_time`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `answer_count` (`answer_count`) USING BTREE,
    KEY `agree_count` (`agree_count`) USING BTREE,
    KEY `title` (`title`) USING BTREE,
    KEY `is_lock` (`is_lock`) USING BTREE,
    KEY `is_anonymous` (`is_anonymous`) USING BTREE,
    KEY `popular_value` (`popular_value`) USING BTREE,
    KEY `best_answer` (`best_answer`) USING BTREE,
    KEY `popular_value_update` (`popular_value_update`) USING BTREE,
    KEY `against_count` (`against_count`) USING BTREE,
    KEY `is_recommend` (`is_recommend`) USING BTREE,
    KEY `modify_count` (`modify_count`) USING BTREE,
    KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC COMMENT='问题表';

DROP TABLE IF EXISTS `aws_question_comment`;
CREATE TABLE IF NOT EXISTS `aws_question_comment` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `pid` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '回复评论的id',
    `question_id` int(11) DEFAULT '0' COMMENT '问题id',
    `uid` int(11) DEFAULT '0' COMMENT '评论人',
    `message` text COMMENT '评论内容',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除1正常0删除',
    `at_uid` varchar(255) DEFAULT  null COMMENT '被@用户id',
    `create_time` int(10) DEFAULT '0' COMMENT '评论时间',
    `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `question_id` (`question_id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='问题评论表';

DROP TABLE IF EXISTS `aws_question_focus`;
CREATE TABLE IF NOT EXISTS `aws_question_focus` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `question_id` int(11) DEFAULT NULL COMMENT '问题ID',
    `uid` int(11) DEFAULT NULL COMMENT '用户UID',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除1正常0删除',
    `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `question_id` (`question_id`) USING BTREE,
    KEY `question_uid` (`question_id`,`uid`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='问题关注表';

DROP TABLE IF EXISTS `aws_question_invite`;
CREATE TABLE IF NOT EXISTS `aws_question_invite` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `question_id` int(11) NOT NULL COMMENT '问题ID',
    `sender_uid` int(11) NOT NULL,
    `recipient_uid` int(11) DEFAULT NULL,
    `create_time` int(10) DEFAULT '0' COMMENT '添加时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `question_id` (`question_id`) USING BTREE,
    KEY `sender_uid` (`sender_uid`) USING BTREE,
    KEY `recipient_uid` (`recipient_uid`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='邀请问答';

DROP TABLE IF EXISTS `aws_question_vote`;
CREATE TABLE IF NOT EXISTS `aws_question_vote` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL COMMENT '投票用户',
    `item_type` varchar(16) DEFAULT NULL COMMENT '内容类型',
    `item_id` int(10) NOT NULL COMMENT '内容ID',
    `vote_value` tinyint(1) DEFAULT '0' COMMENT '1赞同,-1反对',
    `create_time` int(10) NOT NULL COMMENT '操作时间',
    `weigh_factor` int(10) DEFAULT '0' COMMENT '赞同反对系数',
    `item_uid` int(10) DEFAULT '0' COMMENT '被投票用户',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `item_type` (`item_type`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE,
    KEY `item_uid` (`item_uid`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='赞踩表';

DROP TABLE IF EXISTS `aws_report`;
CREATE TABLE `aws_report` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `uid` int(11) DEFAULT '0' COMMENT '用户id',
      `item_type` varchar(50) DEFAULT NULL COMMENT '内容类型',
      `item_id` int(11) DEFAULT '0' COMMENT '举报内容id',
      `handle_type` tinyint(1) DEFAULT NULL COMMENT '处理方式，0拒绝处理，1删除内容，2修改内容',
      `handle_reason` varchar(255) DEFAULT NULL COMMENT '处理说明',
      `reason` text DEFAULT NULL COMMENT '举报理由',
      `url` varchar(255) DEFAULT NULL COMMENT '举报内容URL',
      `create_time` int(11) DEFAULT '0' COMMENT '举报时间',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否处理',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `create_time` (`create_time`) USING BTREE,
      KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='内容举报表';

DROP TABLE IF EXISTS `aws_topic`;
CREATE TABLE `aws_topic` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '话题id',
    `pid` int(10) DEFAULT '0' COMMENT '父级话题id',
    `is_parent` tinyint(1) unsigned DEFAULT '0' COMMENT '是否是根话题',
    `uid` int(10) DEFAULT '0' COMMENT '话题创建者',
    `title` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '话题标题',
    `discuss` int(11) DEFAULT '0' COMMENT '讨论计数',
    `description` longtext CHARACTER SET utf8mb4 COMMENT '话题描述',
    `pic` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '话题图片',
    `lock` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否锁定',
    `top` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否推荐',
    `focus` int(11) DEFAULT '0' COMMENT '关注计数',
    `related` tinyint(1) DEFAULT '0' COMMENT '是否被用户关联',
    `url_token` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
    `seo_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
    `seo_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `seo_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `discuss_week` int(10) DEFAULT '0' COMMENT '一周讨论数',
    `discuss_month` int(10) DEFAULT '0' COMMENT '一月讨论数',
    `discuss_update` int(10) DEFAULT '0' COMMENT '讨论更新时间',
    `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
    `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '审核状态',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `title` (`title`) USING BTREE,
    KEY `url_token` (`url_token`) USING BTREE,
    KEY `discuss` (`discuss`) USING BTREE,
    KEY `create_time` (`create_time`) USING BTREE,
    KEY `related` (`related`) USING BTREE,
    KEY `focus` (`focus`) USING BTREE,
    KEY `lock` (`lock`) USING BTREE,
    KEY `pid` (`pid`) USING BTREE,
    KEY `discuss_week` (`discuss_week`) USING BTREE,
    KEY `discuss_month` (`discuss_month`) USING BTREE,
    KEY `discuss_update` (`discuss_update`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='话题';

INSERT INTO `aws_topic` VALUES (1, 0, 0, 1, '默认话题', 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0,1);

DROP TABLE IF EXISTS `aws_topic_focus`;
CREATE TABLE `aws_topic_focus` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `topic_id` int(11) DEFAULT NULL COMMENT '话题ID',
    `uid` int(11) DEFAULT NULL COMMENT '用户UID',
    `create_time` int(10) DEFAULT NULL COMMENT '添加时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `topic_id` (`topic_id`) USING BTREE,
    KEY `topic_uid` (`topic_id`,`uid`) USING BTREE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='话题关注表';

DROP TABLE IF EXISTS `aws_topic_relation`;
CREATE TABLE `aws_topic_relation` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID',
    `topic_id` int(11) DEFAULT '0' COMMENT '话题id',
    `uid` int(11) DEFAULT '0' COMMENT '用户ID',
    `item_id` int(11) DEFAULT '0',
    `item_type` varchar(16) DEFAULT NULL,
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否删除',
    `create_time` int(10) DEFAULT '0' COMMENT '添加时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `topic_id` (`topic_id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `item_type` (`item_type`) USING BTREE,
    KEY `item_id` (`item_id`) USING BTREE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='内容聚合表';

DROP TABLE IF EXISTS `aws_answer_thanks`;
CREATE TABLE `aws_answer_thanks` (
     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     `uid` int(11) DEFAULT '0' COMMENT '用户ID',
     `answer_id` int(11) DEFAULT '0' COMMENT '回答ID',
     `create_time` int(11) DEFAULT '0',
     PRIMARY KEY (`id`) USING BTREE,
     KEY `answer_id` (`answer_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='回答感谢表';

DROP TABLE IF EXISTS `aws_uninterested`;
CREATE TABLE `aws_uninterested` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `uid` int(11) DEFAULT '0' COMMENT '用户ID',
        `item_id` int(11) DEFAULT '0' COMMENT '内容ID',
        `item_type` varchar(100) DEFAULT '' COMMENT '内容类型',
        `create_time` int(11) DEFAULT '0',
        PRIMARY KEY (`id`) USING BTREE,
        KEY `item_id` (`item_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='内容不感兴趣表';

DROP TABLE IF EXISTS `aws_users_verify`;
CREATE TABLE `aws_users_verify` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `uid` int(11) NOT NULL COMMENT '用户ID',
    `data` longtext COMMENT '审核数据',
    `status` tinyint(1) DEFAULT '0' COMMENT '审核状态0未提交,1待审核2已审核3拒绝审核',
    `type` varchar(32) DEFAULT '' COMMENT '审核类型',
    `reason` varchar(255) NOT NULL COMMENT '审核理由',
    `create_time` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `uid` (`uid`),
    KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户认证资料表';

DROP TABLE IF EXISTS `aws_users_verify_type`;
CREATE TABLE `aws_users_verify_type` (
 `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
 `name` char(100) NOT NULL DEFAULT '' COMMENT '认证标识',
 `title` char(100) NOT NULL DEFAULT '' COMMENT '认证名称',
 `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
 `icon` varchar(255) DEFAULT NULL COMMENT '认证图标',
 `remark` text NOT NULL COMMENT '备注',
 PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户认证类型';
INSERT INTO `aws_users_verify_type` (`id`, `name`, `title`, `status`, `icon`, `remark`) VALUES (1, 'personal', '个人认证', 1, '/static/common/image/people.svg', '个人认证');
INSERT INTO `aws_users_verify_type` (`id`, `name`, `title`, `status`, `icon`, `remark`) VALUES (2, 'enterprise', '公司认证', 1, '/static/common/image/company.svg', '公司认证');

DROP TABLE IF EXISTS `aws_verify_field`;
CREATE TABLE `aws_verify_field` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量名',
   `verify_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '认证类型',
   `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量标题',
   `tips` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '变量描述',
   `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
   `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量值',
   `option` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '变量字典数据',
   `sort` int(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'  COMMENT '排序字段',
   `validate` tinyint(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1'  COMMENT '是否必填',
   `verify_show` tinyint(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1'  COMMENT '是否认证显示',
   `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
   `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
   PRIMARY KEY (`id`) USING BTREE,
   UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC COMMENT='认证字段管理';

INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (1, 'real_name', 'personal', '真实姓名', '填写您的真实姓名', 'text', '', '[]', 0, 1621141649, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (2, 'card', 'personal', '身份证', '填写身份证号码', 'text', '', '[]', 0, 1621141718, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (3, 'mobile', 'personal', '联系方式', '', 'text', '', '[]', 0, 1621141759, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (4, 'remark', 'personal', '认证说明', '', 'textarea', '', '[]', 0, 1621141784, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (5, 'company_name', 'enterprise', '公司名称', '', 'text', '', '[]', 0, 1621143505, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (6, 'company_code', 'enterprise', '组织代码', '', 'text', '', '[]', 0, 1621143524, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (7, 'company_mobile', 'enterprise', '联系电话', '', 'text', '', '[]', 0, 1621143553, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (8, 'company_code_image', 'enterprise', '组织代码附件', '', 'image', '', '[]', 0, 1621143596, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (9, 'company_image', 'enterprise', '营业执照', '', 'image', '', '[]', 0, 1621143630, 0, 1);
INSERT INTO `aws_verify_field` (`id`, `name`, `verify_type`, `title`, `tips`, `type`, `value`, `option`, `sort`, `create_time`, `update_time`,`validate`) VALUES (10, 'company_remark', 'enterprise', '认证说明', '', 'textarea', '', '[]', 0, 1621143655, 0, 1);

DROP TABLE IF EXISTS `aws_page`;
CREATE TABLE `aws_page` (
   `id` int NOT NULL AUTO_INCREMENT,
   `title` varchar(255) DEFAULT NULL,
   `keywords` varchar(255) DEFAULT NULL,
   `description` varchar(255) DEFAULT NULL,
   `contents` longtext,
   `url_name` varchar(32) NOT NULL,
   `status` tinyint(1) NOT NULL DEFAULT '1',
   `create_time` int DEFAULT '0' COMMENT '发布时间',
   `update_time` int DEFAULT '0' COMMENT '更新时间',
   PRIMARY KEY (`id`),
   UNIQUE KEY `url_name` (`url_name`),
   KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='单页表';
INSERT INTO `aws_page` (`id`, `title`, `keywords`, `description`, `contents`, `url_name`, `status`, `create_time`, `update_time`) VALUES (1, '关于我们', '', '', '', 'about', 1, 1651813462, 1651900054);
INSERT INTO `aws_page` (`id`, `title`, `keywords`, `description`, `contents`, `url_name`, `status`, `create_time`, `update_time`) VALUES (2, '社区规范', '', '', '', 'rule', 1, 1651900067, 1651900067);

DROP TABLE IF EXISTS `aws_users_active`;
CREATE TABLE `aws_users_active` (
       `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
       `uid` int(11) DEFAULT '0',
       `expire_time` int(10) DEFAULT NULL,
       `active_code` varchar(32) DEFAULT NULL,
       `active_type` varchar(50) DEFAULT NULL,
       `create_time` int(10) DEFAULT NULL,
       `create_valid_ip` varchar(20) DEFAULT NULL,
       `active_time` int(10) DEFAULT NULL,
       `active_ip` varchar(20) DEFAULT NULL,
       PRIMARY KEY (`id`),
       KEY `active_code` (`active_code`),
       KEY `active_type` (`active_type`),
       KEY `uid` (`uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COMMENT='用户激活码';

DROP TABLE IF EXISTS `aws_task`;
CREATE TABLE `aws_task` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `code` varchar(20) NOT NULL DEFAULT '' COMMENT '任务编号',
    `title` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名称',
    `command` varchar(500) DEFAULT '' COMMENT '执行指令',
    `exec_pid` bigint(20) DEFAULT '0' COMMENT '执行进程',
    `exec_data` longtext COMMENT '执行参数',
    `exec_time` bigint(20) DEFAULT '0' COMMENT '执行时间',
    `exec_desc` varchar(500) DEFAULT '' COMMENT '执行描述',
    `enter_time` decimal(20,4) DEFAULT '0.0000' COMMENT '开始时间',
    `outer_time` decimal(20,4) DEFAULT '0.0000' COMMENT '结束时间',
    `loops_time` bigint(20) DEFAULT '0' COMMENT '循环时间',
    `attempts` bigint(20) DEFAULT '0' COMMENT '执行次数',
    `rscript` tinyint(1) DEFAULT '1' COMMENT '任务类型(0单例,1多例)',
    `status` tinyint(1) DEFAULT '1' COMMENT '任务状态(1新任务,2处理中,3成功,4失败)',
    `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `task_code` (`code`) USING BTREE,
    KEY `task_title` (`title`) USING BTREE,
    KEY `task_status` (`status`) USING BTREE,
    KEY `task_rscript` (`rscript`) USING BTREE,
    KEY `task_create_at` (`create_at`) USING BTREE,
    KEY `task_exec_time` (`exec_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务队列';

DROP TABLE IF EXISTS `aws_task_log`;
CREATE TABLE `aws_task_log` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL COMMENT '任务的ID',
    `remark` text COMMENT '备注',
    `create_time` int(10) unsigned NOT NULL COMMENT '执行时间',
    `status` tinyint(1) NOT NULL COMMENT '状态 0:失败 1:成功',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='定时任务日志表';

DROP TABLE IF EXISTS `aws_email_log`;
CREATE TABLE `aws_email_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `send_to` varchar(255) NOT NULL COMMENT '发送给',
    `subject` varchar(255) NOT NULL COMMENT '邮件主题',
    `message` text NOT NULL COMMENT '邮件内容',
    `error_message` text DEFAULT NULL COMMENT '错误信息',
    `create_time` int(10) DEFAULT '0',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0发送失败，1发送成功',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `send_to` (`send_to`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='邮箱记录表';

DROP TABLE IF EXISTS `aws_announce`;
CREATE TABLE `aws_announce` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL,
    `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
    `message` text CHARACTER SET utf8mb4,
    `view_count` int(10) DEFAULT '0',
    `sort` tinyint(2) unsigned NOT NULL DEFAULT '0',
    `status` tinyint(1) unsigned DEFAULT '0' COMMENT '公告状态1正常0删除',
    `set_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 0不置顶1置顶',
    `set_top_time` int(11) unsigned DEFAULT '0' COMMENT '置顶时间',
    `create_time` int(10) DEFAULT '0' COMMENT '发布时间',
    `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `view_count` (`view_count`) USING BTREE,
    KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  ROW_FORMAT=DYNAMIC COMMENT='公告表';

DROP TABLE IF EXISTS `aws_admin_log`;
CREATE TABLE `aws_admin_log` (
    `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `uid` text NOT NULL COMMENT '管理员',
    `url` varchar(255) NOT NULL DEFAULT '' COMMENT '操作页面	',
    `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
    `content` text NOT NULL COMMENT '日志内容',
    `ip` varchar(20) NOT NULL DEFAULT '' COMMENT '操作IP',
    `user_agent` text NOT NULL COMMENT 'User-Agent',
    `create_time` int(11) NOT NULL,
    `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员日志';

DROP TABLE IF EXISTS `aws_hook`;
CREATE TABLE `aws_hook` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '系统钩子',
    `name` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名称',
    `description` varchar(200) NOT NULL DEFAULT '' COMMENT '钩子简介',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0',
    `update_time` int(10) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='钩子表';

INSERT INTO `aws_hook` (`id`, `system`, `name`, `description`, `status`, `create_time`, `update_time`) VALUES (1, 0, 'editor', '', 1, 0, 0);

DROP TABLE IF EXISTS `aws_hook_plugins`;
CREATE TABLE `aws_hook_plugins` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `hook` varchar(32) NOT NULL COMMENT '钩子id',
    `plugins` varchar(32) NOT NULL COMMENT '插件标识',
    `sort` int(11) unsigned NOT NULL DEFAULT '0',
    `status` tinyint(2) unsigned NOT NULL DEFAULT '1',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0',
    `update_time` int(10) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='钩子-插件对应表';

INSERT INTO `aws_hook_plugins` (`id`, `hook`, `plugins`, `sort`, `status`, `create_time`, `update_time`) VALUES (1, 'editor', 'editor', 0, 1, 0, 0);

DROP TABLE IF EXISTS `aws_plugins`;
CREATE TABLE `aws_plugins` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(32) NOT NULL COMMENT '插件名称(英文)',
   `title` varchar(32) NOT NULL COMMENT '插件标题',
   `description` text NOT NULL COMMENT '插件简介',
   `author` varchar(32) NOT NULL COMMENT '作者',
   `author_url` varchar(255) NOT NULL COMMENT '作者主页',
   `plugin_url` varchar(255) DEFAULT NULL COMMENT '插件默认详情页',
   `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
   `identifier` varchar(64) NOT NULL DEFAULT '' COMMENT '插件唯一标识符',
   `config` text NOT NULL COMMENT '插件配置',
   `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
   `setting` text COMMENT '附加设置',
   `type` varchar(32) NOT NULL COMMENT '插件类型;plugins插件,module模块',
   `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '应用分类',
   `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
   `create_time` int(10) unsigned NOT NULL DEFAULT '0',
   `update_time` int(10) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='插件表';

INSERT INTO `aws_plugins` (`id`, `name`, `title`, `description`, `author`, `author_url`, `version`, `identifier`, `config`, `sort`, `setting`, `status`, `create_time`, `update_time`,`type`) VALUES (1, 'editor', 'WEditor编辑器', 'WEditor编辑器', 'WeCenter官方', 'https://www.wecenter.com', '1.0.0', '', '{\"timeout\":{\"title\":\"超时时间\",\"type\":\"text\",\"options\":[],\"value\":\"30000\",\"tips\":\"超时时间,单位(毫秒)\"}}', 0, '', 1, 0, 1646733721,'plugins');

DROP TABLE IF EXISTS `aws_search_engine`;
CREATE TABLE `aws_search_engine` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
     `name` varchar(16) DEFAULT NULL COMMENT '索引名称/数据库名称',
     `title` varchar(32) DEFAULT NULL COMMENT '搜索标题',
     `pk` varchar(255) DEFAULT NULL COMMENT '主键字段',
     `union_sql` text COMMENT '聚合返回sql，聚合查询时需要，确保多个表字段统一；如user_name as detail',
     `search_field` varchar(255) DEFAULT NULL COMMENT '查询字段，多个用|分割',
     `search_engine` varchar(32) DEFAULT NULL COMMENT '搜索引擎，默认本地数据库',
     `result_field` text COMMENT '查询返回字段',
     `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
     `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
     PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='搜索引擎配置';

INSERT INTO `aws_search_engine` (`id`, `name`, `title`, `pk`, `union_sql`, `search_field`, `search_engine`, `result_field`, `status`, `create_time`) VALUES (1, 'question', '问题', 'id', 'title,search_text as detail', 'title|search_text', 'regexp', 'id,uid', 1, NULL);
INSERT INTO `aws_search_engine` (`id`, `name`, `title`, `pk`, `union_sql`, `search_field`, `search_engine`, `result_field`, `status`, `create_time`) VALUES (2, 'article', '文章', 'id', 'title,search_text as detail', 'title|search_text', 'regexp', 'id,uid', 1, NULL);
INSERT INTO `aws_search_engine` (`id`, `name`, `title`, `pk`, `union_sql`, `search_field`, `search_engine`, `result_field`, `status`, `create_time`) VALUES (3, 'users', '用户', 'uid', 'nick_name as title,user_name as detail', 'nick_name|user_name', 'regexp', 'uid', 1, NULL);
INSERT INTO `aws_search_engine` (`id`, `name`, `title`, `pk`, `union_sql`, `search_field`, `search_engine`, `result_field`, `status`, `create_time`) VALUES (4, 'topic', '话题', 'id', 'title,description as detail', 'title|description', 'regexp', 'id', 1, NULL);

DROP TABLE IF EXISTS `aws_forbidden_ip`;
CREATE TABLE `aws_forbidden_ip` (
   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
   `uid` int(10) unsigned DEFAULT NULL COMMENT '用户ID',
   `ip` varchar(16) NOT NULL COMMENT 'IP',
   `time` int(10) unsigned DEFAULT NULL COMMENT '封禁时间',
   PRIMARY KEY (`id`),
   UNIQUE KEY `ip` (`ip`),
   KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='封禁列表';

DROP TABLE IF EXISTS `aws_users_notify_setting`;
CREATE TABLE `aws_users_notify_setting` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '通知标识',
    `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '通知标题',
    `group` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '通知分组ID',
    `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '通知主题',
    `message` text COLLATE utf8_unicode_ci COMMENT '通知详情',
    `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '通知类型:site站内通知，email邮件通知,dingTalk钉钉通知,mobile短信通知,wechat微信通知,template微信模板消息,min小程序通知',
    `user_setting` tinyint(1) unsigned DEFAULT '1' COMMENT '是否允许用户配置',
    `extends` text COLLATE utf8_unicode_ci COMMENT '其他通知相关附属设置',
    `status` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启用',
    `system` tinyint(1) unsigned DEFAULT 0 COMMENT '是否系统内置',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='消息通知配置表';

INSERT INTO `aws_users_notify_setting` VALUES (1, 'BEST_ANSWER', '回答被设为最佳', 'TYPE_ANSWER_COMMENT', '您的回答被设为了最佳', '您在问题 [#title#] 中的回答被设为了最佳回复', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (2, 'TYPE_PEOPLE_FOCUS_ME', '有用户关注了我', 'TYPE_PEOPLE_FOCUS_ME', '[#from_username#] 关注了你', NULL, 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (3, 'TYPE_SYSTEM_NOTIFY', '新用户注册欢迎', 'TYPE_SYSTEM_NOTIFY', '亲爱的用户您好,欢迎注册[#site_name#]', '尊敬的[#user_name#]，您已经注册成为[#site_name#]的会员，请您在发表言论时，遵守当地法律法规。如果您有什么疑问可以联系管理员。', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (4, 'QUESTION_ANSWER', '问题有新的回复', 'TYPE_ANSWER_COMMENT', '[#from_username#]回答了您的问题', '您发表的问题 [#title#] 有新的回答', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (5, 'QUESTION_COMMENT_AT_ME', '有人在问题评论中@我', 'TYPE_PEOPLE_AT_ME', '[#from_username#]在问题评论中@到了您', '[#from_username#]在问题 [#title#] 评论中@到了您', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (6, 'QUESTION_ANSWER_COMMENT_AT_ME', '有人在问题回答评论中@我', 'TYPE_PEOPLE_AT_ME', '[#from_username#]在问题回答评论中@到了您', '[#from_username#]在问题 [#title#] 回答评论中@到了您', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (7, 'NEW_ANSWER_COMMENT', '有人评论了您的回答', 'TYPE_ANSWER_COMMENT', '[#from_username#]评论了您的回答', '[#from_username#]在问题 [#title#] 中评论了您的回答', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (8, 'INVITE_ANSWER', '有人邀请您回答问题', 'TYPE_INVITE', '[#from_username#]邀请您回答问题', '[#from_username#]邀请您回答问题 [#title#] ', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (9, 'NEW_ARTICLE_COMMENT', '有人评论了您的文章', 'TYPE_ANSWER_COMMENT', '[#from_username#]评论了您的文章', '[#from_username#]在文章 [#title#] 中评论了您', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (10, 'ARTICLE_COMMENT_AT_ME', '有人在文章评论中@我', 'TYPE_PEOPLE_AT_ME', '[#from_username#]在文章评论中@到了您', '[#from_username#]在文章 [#title#] 评论中@到了您', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (11, 'NEW_QUESTION_COMMENT', '有人评论了您的问题', 'TYPE_ANSWER_COMMENT', '[#from_username#]评论了您的问题', '[#from_username#]在问题 [#title#] 中评论了您', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (12, 'REPORT_QUESTION_HANDLE_SUCCESS', '问题举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的问题已处理反馈', '亲爱的用户您好,您举报的问题 [#title#] 经审核确认存在违规行为,现已对该问题进行删除处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (13, 'QUESTION_REPORT_HANDLE_SUCCESS', '问题被举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您的问题被举报反馈', '亲爱的用户您好,您发表的问题 [#title#] 经审核确认存在违规行为,现已对该问题进行删除处理！[#site_name#] 希望能够遵守社区规则进行发问', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (14, 'REPORT_ANSWER_HANDLE_SUCCESS', '回答举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的回答已处理反馈', '亲爱的用户您好,您举报在问题 [#title#] 中的回答 经审核确认存在违规行为,现已对该问题回答进行删除处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (15, 'ANSWER_REPORT_HANDLE_SUCCESS', '回答被举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您的回答被举报反馈', '亲爱的用户您好,您在问题 [#title#] 中发表的回答 经审核确认存在违规行为,现已对该问题回答进行删除处理！[#site_name#] 希望能够遵守社区规则进行回答', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (16, 'REPORT_ARTICLE_HANDLE_SUCCESS', '文章举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的文章处理反馈', '亲爱的用户您好,您举报的文章 [#title#] 经审核确认存在违规行为,现已对该文章进行删除处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (17, 'ARTICLE_REPORT_HANDLE_SUCCESS', '文章被举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您的文章被举报删除反馈', '亲爱的用户您好,您发表的文章 [#title#] 经审核确认存在违规行为,现已对该文章进行删除处理！[#site_name#] 希望能够遵守社区规则进行回答', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (18, 'REPORT_ARTICLE_COMMENT_HANDLE_SUCCESS', '文章评论举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的文章评论处理反馈', '亲爱的用户您好,您举报在文章中 [#title#] 的评论 经审核确认存在违规行为,现已对该文章评论进行删除处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (19, 'ARTICLE_COMMENT_REPORT_HANDLE_SUCCESS', '文章评论被举报成功反馈', 'TYPE_SYSTEM_NOTIFY', '您的文章评论被举报反馈', '亲爱的用户您好,您在文章中 [#title#] 的评论 经审核确认存在违规行为,现已对该文章评论进行删除处理！[#site_name#] 希望能够遵守社区规则进行回答', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (20, 'REPORT_QUESTION_HANDLE_MODIFY', '问题举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的问题已处理反馈', '亲爱的用户您好,您举报的问题 [#title#] 经审核确认存在违规行为,现已对该问题进行修改处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (21, 'QUESTION_REPORT_HANDLE_MODIFY', '问题被举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您的问题被举报反馈', '亲爱的用户您好,您发表的问题 [#title#] 经审核确认存在违规行为,现已对该问题进行修改处理！[#site_name#] 希望能够遵守社区规则进行发问', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (22, 'REPORT_ANSWER_HANDLE_MODIFY', '回答举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的回答已处理反馈', '亲爱的用户您好,您举报在问题 [#title#] 中的回答 经审核确认存在违规行为,现已对该问题回答进行修改处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (23, 'ANSWER_REPORT_HANDLE_MODIFY', '回答被举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您的回答被举报反馈', '亲爱的用户您好,您在问题 [#title#] 中发表的回答 经审核确认存在违规行为,现已对该问题回答进行修改处理！[#site_name#] 希望能够遵守社区规则进行回答', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (24, 'REPORT_ARTICLE_HANDLE_MODIFY', '文章举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的文章已处理反馈', '亲爱的用户您好,您举报的文章 [#title#] 经审核确认存在违规行为,现已对该文章进行修改处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (25, 'ARTICLE_REPORT_HANDLE_MODIFY', '文章被举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您的文章被举报反馈', '亲爱的用户您好,您发表的文章 [#title#] 经审核确认存在违规行为,现已对该文章进行修改处理！[#site_name#] 希望能够遵守社区规则进行回答', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (26, 'REPORT_ARTICLE_COMMENT_HANDLE_MODIFY', '文章评论举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您举报的文章评论已处理反馈', '亲爱的用户您好,您举报在文章中 [#title#] 的评论 经审核确认存在违规行为,现已对该文章评论进行修改处理！ [#site_name#] 非常感谢您的反馈！', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (27, 'ARTICLE_COMMENT_REPORT_HANDLE_MODIFY', '文章评论被举报修改反馈', 'TYPE_SYSTEM_NOTIFY', '您的文章评论被举报反馈', '亲爱的用户您好,您在文章中 [#title#] 的评论 经审核确认存在违规行为,现已对该文章评论进行修改处理！[#site_name#] 希望能够遵守社区规则进行回答', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (28, 'REPORT_HANDLE_DECLINE', '举报失败提醒', 'TYPE_SYSTEM_NOTIFY', '您举报处理反馈', '', 'site,email', 0, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (29, 'AGREE_CONTENT', '有用户点赞了我', 'TYPE_AGREE', '有用户点赞了您的内容', '[#from_username#] 点赞了您的内容 [#title#] ', 'site,email', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (30, 'TYPE_QUESTION_APPROVAL', '问题审核通过', 'TYPE_SYSTEM_NOTIFY', '我发表的问题审核通过', '亲爱的用户您好,您发表的问题 [#title#] 已审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (31, 'TYPE_ARTICLE_APPROVAL', '文章审核通过', 'TYPE_SYSTEM_NOTIFY', '我发表的文章审核通过', '亲爱的用户您好,您发表的文章 [#title#] 已审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (32, 'TYPE_QUESTION_DECLINE', '问题审核拒绝', 'TYPE_SYSTEM_NOTIFY', '我发表的问题审核未通过', '亲爱的用户您好,您发表的问题 [#title#] 未审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (33, 'TYPE_ARTICLE_DECLINE', '文章审核拒绝', 'TYPE_SYSTEM_NOTIFY', '我发表的文章审核未通过', '亲爱的用户您好,您发表的文章 [#title#] 未审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (34, 'TYPE_QUESTION_MODIFY_APPROVAL', '问题修改审核通过', 'TYPE_SYSTEM_NOTIFY', '我修改的问题审核通过', '亲爱的用户您好,您修改的问题 [#title#] 已审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (35, 'TYPE_ARTICLE_MODIFY_APPROVAL', '文章修改审核通过', 'TYPE_SYSTEM_NOTIFY', '我修改的文章审核通过', '亲爱的用户您好,您修改的文章 [#title#] 已审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (36, 'TYPE_ANSWER_APPROVAL', '回答审核通过', 'TYPE_SYSTEM_NOTIFY', '我发表的回答审核通过', '亲爱的用户您好,您在问题 [#title#] 发表的回答已审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (37, 'TYPE_ANSWER_MODIFY_APPROVAL', '回答修改审核通过', 'TYPE_SYSTEM_NOTIFY', '我修改的回答审核通过', '亲爱的用户您好,您在问题 [#title#] 修改的回答已审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (38, 'TYPE_QUESTION_MODIFY_DECLINE', '问题修改审核未通过', 'TYPE_SYSTEM_NOTIFY', '我修改的问题审核未通过', '亲爱的用户您好,您修改的问题 [#title#] 未审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (39, 'TYPE_ARTICLE_MODIFY_DECLINE', '文章修改审核未通过', 'TYPE_SYSTEM_NOTIFY', '我修改的文章审核未通过', '亲爱的用户您好,您修改的文章 [#title#] 未审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (40, 'TYPE_ANSWER_DECLINE', '回答审核未通过', 'TYPE_SYSTEM_NOTIFY', '我发表的回答审核未通过', '亲爱的用户您好,您在问题 [#title#] 发表的回答未审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (41, 'TYPE_ANSWER_MODIFY_DECLINE', '回答修改审核未通过', 'TYPE_SYSTEM_NOTIFY', '我修改的回答审核未通过', '亲爱的用户您好,您在问题 [#title#] 修改的回答未审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (42, 'TYPE_COLUMN_APPROVAL', '申请专栏审核通过', 'TYPE_SYSTEM_NOTIFY', '我申请专栏审核通过', '亲爱的用户您好,您申请的专栏 [#title#] 已审核通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (43, 'TYPE_COLUMN_DECLINE', '申请专栏审核未通过', 'TYPE_SYSTEM_NOTIFY', '我申请专栏审核未通过', '亲爱的用户您好,您申请的专栏 [#title#] 审核未通过！', 'site', 1, NULL, 1, 1);
INSERT INTO `aws_users_notify_setting` VALUES (44, 'DIY_NOTIFY', '自定义通知', 'TYPE_SYSTEM_NOTIFY', '', '', 'site', 0, NULL, 1, 0);

DROP TABLE IF EXISTS `aws_theme`;
CREATE TABLE `aws_theme` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     `name` varchar(32) NOT NULL COMMENT '模板标识(英文)',
     `title` varchar(32) NOT NULL COMMENT '模板标题',
     `description` text NOT NULL COMMENT '模板简介',
     `author` varchar(32) NOT NULL COMMENT '模板作者',
     `author_url` varchar(255) NOT NULL COMMENT '作者主页',
     `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
     `config` text NOT NULL COMMENT '插件配置',
     `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
     `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='模板表';

INSERT INTO `aws_theme` VALUES (1, 'default', '默认模板', 'bootstrap4模板', 'WeCenter官方', 'https://www.wecenter.com', '1.0.0', '{"home":{"title":"发现页","config":{"search_enable":{"title":"首页搜索","type":"radio","value":"Y","options":{"N":"不开启","Y":"开启"},"tips":""},"search_bg":{"title":"头部背景图","type":"image","value":"","options":[],"tips":""},"search_title_text":{"title":"搜索标题文字","type":"text","value":"","options":[],"tips":""},"search_min_text":{"title":"搜索介绍文字","type":"textarea","value":"","options":[],"tips":""},"sidebar_show_items":{"title":"侧边栏项目","type":"checkbox","value":"write_nav,announce,focus_topic,column,hot_topic,hot_users","options":{"write_nav":"快速发起","announce":"网站公告","focus_topic":"关注话题","column":"热门专栏","hot_topic":"热门话题","hot_users":"热门用户","diy_content":"自定义内容"},"tips":"侧边栏显示的项目"},"sidebar_diy_content":{"title":"自定义内容","type":"editor","value":"&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;\/p&gt;","options":[],"tips":"侧边栏自定义内容,支持富文本"},"links_show_type":{"title":"友情链接展示方式","type":"radio","value":"text","options":{"text":"文字链接","image":"图片链接"},"tips":"侧边栏自定义内容,支持富文本"}}},"question":{"title":"问答页面","config":{"sidebar_show_items":{"title":"侧边栏项目","type":"checkbox","value":"write_nav,announce,focus_topic,column,hot_topic,hot_users","options":{"write_nav":"快速发起","announce":"网站公告","focus_topic":"关注话题","column":"热门专栏","hot_topic":"热门话题","hot_users":"热门用户","diy_content":"自定义内容"},"tips":"侧边栏显示的项目"},"sidebar_diy_content":{"title":"自定义内容","type":"editor","value":"&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;\/p&gt;","options":[],"tips":"侧边栏自定义内容,支持富文本"}}},"article":{"title":"文章页面","config":{"sidebar_show_items":{"title":"侧边栏项目","type":"checkbox","value":"write_nav,announce,focus_topic,column,hot_topic,hot_users","options":{"write_nav":"快速发起","announce":"网站公告","focus_topic":"关注话题","column":"热门专栏","hot_topic":"热门话题","hot_users":"热门用户","diy_content":"自定义内容"},"tips":"侧边栏显示的项目"},"sidebar_diy_content":{"title":"自定义内容","type":"editor","value":"&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;\/p&gt;","options":[],"tips":"侧边栏自定义内容,支持富文本"}}},"column":{"title":"专栏页","config":{"navbar_bg":{"title":"头部背景图","type":"image","value":"","options":[],"tips":""},"navbar_text":{"title":"标题文字","type":"text","value":"","options":[],"tips":""}}},"common":{"title":"通用设置","config":{"bg_logo":{"title":"背景图logo","type":"image","value":"","options":[],"tips":"导航带背景时的logo"},"list_show_image":{"title":"列表显示图片","type":"radio","value":"Y","options":{"N":"不显示","Y":"显示"},"tips":""},"fixed_navbar":{"title":"固定导航","type":"radio","value":"N","options":{"N":"不固定","Y":"固定"},"tips":""},"login_type":{"title":"登录方式","type":"radio","value":"page","options":{"page":"新页面","dialog":"弹窗"},"tips":""},"enable_mathjax":{"title":"启用Mathjax支持","type":"radio","value":"N","options":{"N":"不支持","Y":"支持"},"tips":""}}},"question_detail":{"title":"问题详情","config":{"sidebar_show_relation_question":{"title":"侧边栏显示相关问题","type":"radio","value":"Y","options":{"N":"不显示","Y":"显示"},"tips":"侧边栏显示相关问题"},"sidebar_show_recommend_post":{"title":"侧边栏显示推荐内容","type":"radio","value":"Y","options":{"N":"不显示","Y":"显示"},"tips":"侧边栏显示推荐内容"},"sidebar_show_items":{"title":"侧边栏项目","type":"checkbox","value":"","options":{"write_nav":"快速发起","announce":"网站公告","focus_topic":"关注话题","column":"热门专栏","hot_topic":"热门话题","hot_users":"热门用户","diy_content":"自定义内容"},"tips":"侧边栏显示的项目"},"sidebar_diy_content":{"title":"自定义内容","type":"editor","value":"&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;\/p&gt;","options":[],"tips":"侧边栏自定义内容,支持富文本"}}},"article_detail":{"title":"文章详情","config":{"sidebar_show_relation_article":{"title":"侧边栏显示相关文章","type":"radio","value":"Y","options":{"N":"不显示","Y":"显示"},"tips":"侧边栏显示相关文章"},"sidebar_show_recommend_post":{"title":"侧边栏显示推荐内容","type":"radio","value":"Y","options":{"N":"不显示","Y":"显示"},"tips":"侧边栏显示推荐内容"},"sidebar_show_items":{"title":"侧边栏项目","type":"checkbox","value":"","options":{"write_nav":"快速发起","announce":"网站公告","focus_topic":"关注话题","column":"热门专栏","hot_topic":"热门话题","hot_users":"热门用户","diy_content":"自定义内容"},"tips":"侧边栏显示的项目"},"sidebar_diy_content":{"title":"自定义内容","type":"editor","value":"&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;\/p&gt;","options":[],"tips":"侧边栏自定义内容,支持富文本"}}}}', 0, 1);

DROP TABLE IF EXISTS `aws_search_log`;
CREATE TABLE `aws_search_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '搜索用户id',
  `ip` varchar(255) DEFAULT NULL COMMENT '搜索IP地址',
  `keyword` varchar(255) DEFAULT NULL COMMENT '搜索关键词',
  `user_agent` varchar(500) DEFAULT NULL COMMENT '浏览器标识',
  `from` varchar(255) DEFAULT NULL COMMENT '来源页面',
  `create_time` int(11) DEFAULT NULL COMMENT '搜索时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='搜索记录表';

DROP TABLE IF EXISTS `aws_users_favorite_focus`;
CREATE TABLE `aws_users_favorite_focus` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `tag_id` int(11) DEFAULT NULL COMMENT '收藏夹ID',
    `uid` int(11) DEFAULT NULL COMMENT '用户UID',
    `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
    PRIMARY KEY (`id`) USING BTREE,
    KEY `uid` (`uid`) USING BTREE,
    KEY `tag_id` (`tag_id`) USING BTREE,
    KEY `tag_uid` (`tag_id`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='收藏关注表';

DROP TABLE IF EXISTS `aws_app_token`;
CREATE TABLE `aws_app_token` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `title` varchar(255) DEFAULT NULL COMMENT '客户端名称',
     `token` varchar(32) DEFAULT NULL COMMENT '客户端Token',
     `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='接口请求表';

# 2022-06-15
DROP TABLE IF EXISTS `aws_wechat_account`;
CREATE TABLE `aws_wechat_account` (
     `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
     `name` varchar(60) NOT NULL DEFAULT '' COMMENT '公众号名称',
     `app_id` varchar(50) NOT NULL DEFAULT '' COMMENT 'appID',
     `app_secret` varchar(50) NOT NULL DEFAULT '' COMMENT 'appSecret',
     `origin_id` varchar(64) NOT NULL DEFAULT '' COMMENT '公众号原始ID',
     `aes_key`  varchar(100) NOT NULL DEFAULT '' COMMENT 'EncodingAESKey，兼容与安全模式下请一定要填写！！！',
     `logo` char(255) NOT NULL COMMENT '头像地址',
     `token` char(255) NOT NULL COMMENT 'token',
     `related` varchar(200) NOT NULL DEFAULT '' COMMENT '微信对接地址',
     `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型 1 普通订阅号2认证订阅号 3普通服务号 4认证服务号/认证媒体/政府订阅号',
     `qrcode` varchar(200) NOT NULL DEFAULT '' COMMENT '公众号二维码',
     `status` tinyint(1) DEFAULT '1' COMMENT '微信接入状态,0待接入1已接入',
     `create_time` int(11) NOT NULL COMMENT '创建时间',
     `update_time` int(11) NOT NULL COMMENT '更新时间',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='微信公众帐号';

DROP TABLE IF EXISTS `aws_topic_related`;
CREATE TABLE `aws_topic_related` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `source_id` int(11) NOT NULL DEFAULT '0' COMMENT '原话题ID',
   `target_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联话题ID',
   PRIMARY KEY (`id`) USING BTREE,
   KEY `source_id` (`source_id`) USING BTREE,
   KEY `target_id` (`target_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='话题关联表';

DROP TABLE IF EXISTS `aws_topic_merge`;
CREATE TABLE `aws_topic_merge` (
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

DROP TABLE IF EXISTS `aws_browse_records`;
CREATE TABLE `aws_browse_records` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `uid` int(10) unsigned DEFAULT '0' COMMENT '用户UID',
      `item_id` int(10) unsigned DEFAULT '0' COMMENT '内容ID',
      `item_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '内容类型，question问题，article文章，topic话题,column专栏',
      `status` tinyint(10) unsigned DEFAULT '1' COMMENT '1正常0删除',
      `create_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='浏览记录表';

DROP TABLE IF EXISTS `aws_question_redirect`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专栏推荐文章表';


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

DROP TABLE IF EXISTS `aws_dict`;
CREATE TABLE `aws_dict` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL DEFAULT '' COMMENT '字典标签',
    `value` varchar(255) NOT NULL DEFAULT '' COMMENT '字典键值',
    `dict_id` int(10) NOT NULL DEFAULT '0' COMMENT '字典类型ID',
    `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典数据表';

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

DROP TABLE IF EXISTS `aws_dict_type`;
CREATE TABLE `aws_dict_type` (
     `id` int(10) NOT NULL AUTO_INCREMENT,
     `title` char(100) NOT NULL DEFAULT '' COMMENT '显示名称',
     `name` varchar(100) NOT NULL DEFAULT '' COMMENT '字典标识',
     `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典类型表';
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (1, '状态', '1',  '1 显示， 0 隐藏');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (2, '是否', '1', '1 是， 0 否');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (3, '性别', '1', '0 保密，1 男，2 女');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (4, '通知分组', 'notify_group','通知分组');
INSERT INTO `aws_dict_type` (`id`, `title`, `name`, `remark`) VALUES (5, '语言选择', 'language_select','语言选择');


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

SET FOREIGN_KEY_CHECKS = 1;
