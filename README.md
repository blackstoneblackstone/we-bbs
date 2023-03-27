#  WeCenter 问答系统简介

---
WeCenter 问答系统是一套开源的社交化问答软件系统。作为国内首个推出基于 PHP 的社交化问答系统，WeCenter 期望能够给更多的站长或者企业提供一套完整的社交问答系统，帮助社区或者企业搭建相关的知识库建设。

### WeCenter 问答系统的下载

您可以随时从我们的官方下载站下载到最新版本，以及各种补丁

https://wenda.wecenter.com/timeline/

### WeCenter 问答系统的环境需求

1. 可用的 www 服务器，如 Apache、IIS、nginx, 推荐使用性能高效的 Apache 或 nginx.
2. PHP 7.4 及以上
3. MySQL 5.7 及以上, 服务器需要支持 MySQLi 或 PDO_MySQL
4. GD 图形库支持或 ImageMagick 支持, 推荐使用 ImageMagick, 在处理大文件的时候表现良好

### WeCenter 问答系统的安装

### 全新安装

①、访问下载中心，下载最新版的WeCenter程序。

②、把程序上传到网站根目录，并且设置运行目录为 public

网站根目录，就是存放网站代码的地方，例如用虚拟主机的，那么网站根目录就是你的虚拟主机的主目录了。

运行目录，简单来说，就是你网站根目录下的一个文件夹，以往的程序都是直接执行网站根目录的index.php 等就运行了，所以不需要设置运行目录，但是现在都是为了提高安全性，所以，都有一个运行目录。

运行目录去哪设置呢？

用宝塔面板举个例子，就是下面这个，其他的管理面板，虚拟主机都类似，仔细找一找，肯定有运行目录这个东西了。

③、设置伪静态规则，宝塔面板可以直接在伪静态选择 thinkphp 伪静态规则

④、访问我们的网站域名，这时候会跳转到 你的域名/install.php，不出意外，你就会看到这个界面

这时候，我们根据提示，一步步操作，然后你就会来到填写数据库的界面。

这里有个需要特别注意的，就是下面的这个后台地址，默认是admin，但是为了提高安全性，建议你设置一个复杂的名字，随便你写，只要自己容易记住，别人记不住的就尽管写！比如我写的就是 junxiaochenadmin，那么我访问后台的时候，就不是

https://www.wecenter.com/admin.php 了，而是 https://www.wecenter.com/backend.php

然后执行下一步，如果顺利的话，就会安装成功了！

URL重写规则
可以通过URL重写隐藏应用的入口文件index.php（也可以是其它的入口文件，但URL重写通常只能设置一个入口文件）,下面是相关服务器的配置参考：

[ Apache ]
httpd.conf配置文件中加载了mod_rewrite.so模块
AllowOverride None将None改为All
把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>

[ IIS ]
如果你的服务器环境支持ISAPI_Rewrite的话，可以配置httpd.ini文件，添加下面的内容：

RewriteRule (.*)$ /index\.php\?s=$1 [I]

在IIS的高版本下面可以配置web.Config，在中间添加rewrite节点：

<rewrite>
 <rules>
 <rule name="OrgPage" stopProcessing="true">
 <match url="^(.*)$" />
 <conditions logicalGrouping="MatchAll">
 <add input="{HTTP_HOST}" pattern="^(.*)$" />
 <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
 <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
 </conditions>
 <action type="Rewrite" url="index.php/{R:1}" />
 </rule>
 </rules>
 </rewrite>

[ Nginx ]
在Nginx低版本中，是不支持PATHINFO的，但是可以通过在Nginx.conf中配置转发规则实现：

location / { // …..省略部分代码
if (!-e $request_filename) {
rewrite  ^(.*)$  /index.php?s=/$1  last;
}
}

