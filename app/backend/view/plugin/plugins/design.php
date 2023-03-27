{extend name="block" /}
{block name="main"}
<div class="p-3 bg-white">
    <div class="container-fluid">
        <div class="row">
            <form class="col-12" method="post" action="{:url('plugin.Plugins/design')}" id="plugin-form">
                <ul class="nav nav-tabs nav-tabs-block" id="builder-form-group-tab" role="tablist" data-toggle="tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#nav-tab-content-base" role="tab" data-toggle="tab">基本信息</a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link" href="#nav-tab-content-menu" role="tab" data-toggle="tab">后台菜单</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nav-tab-content-config" role="tab" data-toggle="tab">插件配置</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nav-tab-content-setting" role="tab" data-toggle="tab">其他配置</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nav-tab-content-build" role="tab" data-toggle="tab">生成配置</a>
                    </li>-->
                </ul>
                <div class="tab-content p-3">
                    <div class="tab-pane active" id="nav-tab-content-base">
                        <div class="form-group">
                            <label for="title">插件名称</label>
                            <input type="text" class="form-control" name="title" value="" autocomplete=false id="title" placeholder="请输入插件名称,一般为中文">
                        </div>

                        <div class="form-group">
                            <label for="name">插件标识</label>
                            <input type="text" class="form-control" name="name" value="" autocomplete=false id="name" placeholder="插件标识/目录名(英文)">
                        </div>

                        <div class="form-group">
                            <label for="author">插件作者</label>
                            <input type="text" class="form-control" name="author"  value="WeCenter官方" autocomplete=false id="author" placeholder="插件作者">
                        </div>

                        <div class="form-group">
                            <label for="author_url">作者主页</label>
                            <input type="text" class="form-control" name="author_url" value="https://www.wecenter.com/" autocomplete=false id="author_url" placeholder="作者主页">
                        </div>

                        <div class="form-group">
                            <label for="plugin_url">说明页面</label>
                            <input type="text" class="form-control" name="plugin_url" value="" autocomplete=false id="plugin_url" placeholder="填写外部页面或插件内的访问链接">
                        </div>

                        <div class="form-group">
                            <label for="version">插件版本</label>
                            <input type="text" class="form-control" name="version" value="1.0.0" autocomplete=false id="version" placeholder="插件版本,一般为1.0.0三段标识">
                        </div>

                        <div class="form-group">
                            <label for="intro">插件简介</label>
                            <textarea class="form-control" name="description" id="intro" autocomplete=false placeholder="插件简介"></textarea>
                        </div>
                    </div>

                    <!--<div class="tab-content hide" id="nav-tab-content-menu">
                        <div class="form-group">
                            <label for="title">一级菜单</label>
                            <input type="radio" name="menu[is_nav]" value="0" checked> 否
                            <input type="radio" name="menu[is_nav]" value="1" > 是
                        </div>

                        <div class="form-group">
                            <table class="table array-table">
                                <thead>
                                <tr>
                                    <th>链接</th>
                                    <th>标题</th>
                                    <th>导航</th>
                                    <th>图标</th>
                                    <th>父级</th>
                                    <th>CURD</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-content hide" id="nav-tab-content-config">

                    </div>

                    <div class="tab-content hide" id="nav-tab-content-setting">

                    </div>-->
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-flat btn-primary" onclick="AWS_ADMIN.api.ajaxForm('#plugin-form')">提 交</button>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}
