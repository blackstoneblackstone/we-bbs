{extend name="$theme_block" /}
{block name="main"}
<div  class="aw-wrap mt-2">
    <div class="container bg-white pb-3">
        <div class="p-3">
            <h6 class="text-red">注意:转换开始后，当前新站点的数据会被清空!</h6>
            <ul>
                <li>1、转换数据前请先配置数据库链接</li>
                <li>2、转换完成后，请把旧网站根本目录下uploads中的文件复制到新版本public/storage 目录下</li>
                <li>3、若转换过程中出现错误。请刷新转换页面进行重试</li>
            </ul>
        </div>
        <a href="{:plugins_url('old2new://Index/init')}" class="btn btn-primary" target="_blank">开始转换数据</a>
    </div>
</div>
{/block}