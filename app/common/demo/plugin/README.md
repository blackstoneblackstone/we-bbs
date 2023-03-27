#  WeCenter 插件开发说明

- 插件标识(插件根目录名)必须唯一
- 插件目录app目录下对应前台和后台，注意命名空间和目录结构对应保持一致
- libs目录为插件所需的类库
- model目录为插件所需的模型库，命名空间为plugins\\插件标识\\model
- templates目录对应前台视图目录，务必保持目录结构一致
- validate目录为验证器库，注意命名空间
- view目录为wecenter事件所需的视图模板
- info.php是插件的说明、配置、菜单信息
- Plugin类的命名空间为 plugins\\插件标识; wecenter事件可在Plugin类里实现
- install.sql 安装插件时数据库操作脚本
- uninstall.sql 卸载插件时数据库操作脚本
