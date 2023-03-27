{extend name="block" /}
{block name="main"}
<div class="container-fluid p-3">
    <div class="alert alert-danger">
        <p>
            1、为了防止升级过程中出现数据丢失等问题，建议您在升级前先备份数据库和网站程序！！！<br>
            2、升级前请确保网站根目录及子目录可读写<br>
            3、若升级失败可到官方社区手动下载更新包进行手动升级，下载的升级包目录为script(程序升级文件，可把该目录下的文件覆盖到网站的根目录)，sql(升级sql脚本，可直接在网站对用的数据库中执行该sql，执行前可手动修改aws_为您的数据库前缀 )
        </p>
    </div>

    <div class="card">
        {if $info.data.version}
        <div class="card-body">
            <p style="color: red">
                最新版本：WeCenter V{$info.data.version} {$info.data ? $info.data.authorize : '学习版'}
            </p>
            <p class="mb-3 font-weight-bold">更新内容：</p>
            <div class="border p-3 rounded">
                {$info.data.description|raw}
            </div>
            <div class="block-footer text-center mt-4 mb-3">

                <div class="progress my-3">
                    <div class="progress-bar" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="my-3 d-none" id="success">升级已完成</div>
                <a href="javascript:;" data-url="{:url('download')}" class="btn btn-danger updateBtn">开始升级</a>
            </div>
        </div>
        {else/}
        <div class="block-content">
            <p style="color: red">获取最新版本失败，请检查网络连接是否正常！</p>
        </div>
        {/if}
    </div>
</div>

<script>
    $(document).on('click', '.updateBtn', function (e) {
        e.preventDefault();
        e.target.blur();
        let that = this;
        let options = $.extend({}, $(that).data() || {});
        if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
            options.url = $(that).attr("data-url");
        }
        let error = typeof options.error === 'function' ? options.error : null;
        delete options.success;
        delete options.error;

        if(options.login && !userId)
        {
            layer.msg('您还未登录,请登录后再操作!');
            return false;
        }
        var index= layer.load();

        AWS_ADMIN.api.ajax(options.url, function (res){
            if(res.code)
            {
                $('.progress-bar').css('width','100%').attr('aria-valuenow',100);
                layer.close(index);
                $('#success').removeClass('d-none')
                layer.msg(res.msg,function (){
                    //layer.closeAll();
                    parent.layer.closeAll();
                });
            }else{
                layer.alert(res.msg);
                layer.close(index);
            }
        }, error);
    });
</script>
{/block}