{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title font-10">{:L('发布文章')}</div>
    <a class="aui-header-right font-10 saveArticle">{:L('发布')}</a>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1 bg-white" style="padding-bottom: 50px">
    <div class="card border-0">
        <div class="card-body p-2">
            <form id="question_form"  method="post" action="{:url('article/publish')}" onsubmit="return false">
                {:token_field()}
                <input type="hidden" name="id" value="{$article_info.id|default=0}">
                <input type="hidden" name="wait_time">
                <input type="hidden" name="access_key" value="{$access_key}">
                <div class="form-group mb-3">
                    <input id="title" name="title" value="{$article_info.title|default=''}" class="aw-form-control" type="text" placeholder="{:L('文章标题')}">
                </div>
                <div class="form-group">
                    <label class="mb-3">{:L('文章分类')}</label>
                    <select class="aw-form-control" name="category_id" title="{:L('请选择一项分类')}" required>
                        <option value="0">{:L('选择分类')}</option>
                        {volist name="article_category_list" id="v"}
                        <option value="{$v.id}" {if isset($article_info['category_id']) && $article_info['category_id']==$v['id']}selected {/if}>{$v.title}</option>
                        {if !empty($v.childs)}
                        {foreach $v.childs as $child}
                        <option value="{$child.id}" {if isset($article_info['category_id']) && $article_info['category_id']==$child['id']}selected {/if}>
                        &nbsp;&nbsp;&nbsp;&nbsp;|__{$child.title}
                        </option>
                        {/foreach}
                        {/if}
                        {/volist}
                    </select>
                </div>

                {if($column_list)}
                <div class="form-group">
                    <label class="mb-3">{:L('文章专栏')}</label>
                    <select class="aw-form-control" name="column_id">
                        <option value="0">{:L('选择专栏')}</option>
                        {volist name="column_list" id="v"}
                        <option value="{$v.id}" {if isset($article_info['column_id']) && $v['id']==$article_info['column_id']}selected{/if}>{$v.name}</option>
                        {/volist}
                    </select>
                </div>
                {/if}
                <div class="form-group mb-3">
                    <label>{:L('文章封面')}:<span class="font-8 text-danger">（* {:L('建议图片尺寸')}178*100{:L('的倍数')}）</span></label>
                    <div class="article-cover-box">
                        <div id="fileList_cover" class="uploader-list"></div>
                        <div id="filePicker_cover" style="margin: 0 auto">
                            <a href="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" target="_blank">
                                <img class="image_preview_info" src="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                            </a>
                        </div>
                        <input type="hidden" name="cover" value="{$article_info['cover']|default=''}" class="article-cover">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="mb-3 w-100">
                        {:L('文章话题')}
                        <a href="javascript:;" class="text-primary font-weight-bold aw-ajax-open float-right" data-url="{:url('topic/select',['item_type'=>'article','item_id'=>isset($article_info['id']) ? $article_info['id'] : 0])}">
                            <i class="icon-add"></i>{:L('添加话题')}
                        </a>
                        {if(get_setting('topic_enable')=='Y')} <span class="font-9 text-primary">({:L('至少添加一个')})</span>{/if}
                    </label>
                    <div class="page-detail-topic swiper-container py-1">
                        <ul class="swiper-wrapper" id="awTopicList">
                            {if !empty($article_info['topics'])}
                            {volist name="article_info['topics']" id="v"}
                            <li class="swiper-slide"><a href="{:url('topic/detail',['id'=>$v['id']])}"><em class="tag">{$v.title}</em></a></li>
                            {/volist}
                            <input type="hidden" name="topics" value="{:implode(',',array_column($article_info['topics'],'id'))}">
                            {/if}
                        </ul>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="mb-3">{:L('文章详情')}</label>
                    {:hook('editor',['name'=>'message','cat'=>'article','value'=>isset($article_info['message']) ? $article_info['message']:'','access_key'=>$access_key])}
                </div>

                <!--发布附加钩子-->
                {:hook('publish_extend',['info'=>$article_info,'page'=>'article'])}
                <!--发布附加钩子-->

                {if get_setting('auto_save_draft')=='Y'}
                <script>
                    //自动保存时间间隔
                    var AutoSaveTime = parseInt("{:get_setting('auto_save_draft_time')}")*360;
                    //设置自动保存
                    setInterval(function (item_type,itemId){
                        var formData = AWS_MOBILE.common.formToJSON('question_form');
                        $.ajax({
                            url:baseUrl + '/ajax/save_draft',
                            dataType: 'json',
                            type:'post',
                            data:{
                                data:formData,
                                item_id:'{$article_info.id|default=0}',
                                item_type:'article'
                            },
                            success: function (result)
                            {

                            },
                            error:  function (error) {
                                if ($.trim(error.responseText) !== '') {
                                    layer.closeAll();
                                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                }
                            }
                        });
                    }, AutoSaveTime);
                </script>
                {/if}

                <div class="mt-4 clearfix">
                    {if !isset($article_info['id']) && $setting.enable_anonymous=='Y'}
                    <label class="mb-0 text-muted mr-3">
                        <input value="1" name="is_anonymous" type="checkbox"  {$article_info.is_anonymous ? 'checked' : ''}> {:L('提交')}匿名提问
                    </label>
                    {/if}
                </div>
            </form>
        </div>
    </div>
</div>
<script>

    //上传文章封面
    AWS_MOBILE.upload.webUpload('filePicker_cover','cover_preview','cover','article')

    zxEditor.addFooterButton({
        name: 'save-draft',
        // 按钮外容器样式名称
        class: '',
        // 按钮内i元素样式名
        icon: 'iconfont icon-caogaoxiang',
        // 需要注册的监听事件名
        on: 'save-draft'
    })

    zxEditor.on('save-draft', function () {
        if (!parseInt(userId)) {
            AWS_MOBILE.User.login();
            return false;
        }

        var formData = AWS_MOBILE.common.formToJSON('question_form');

        $.ajax({
            url:baseUrl + '/ajax/save_draft',
            dataType: 'json',
            type:'post',
            data:{
                data:formData,
                item_id:'{$article_info.id|default=0}',
                item_type:'article'
            },
            success: function (result)
            {
                let msg = result.msg ? result.msg : '保存成功';
                if(result.code> 0)
                {
                    AWS_MOBILE.api.success(msg)
                }else{
                    AWS_MOBILE.api.error(msg)
                }
            },
            error:  function (error) {
                if ($.trim(error.responseText) !== '') {
                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });

    $('.saveArticle').click(function (){
        var that = this;
        {if $captcha_enable}
        $('#captcha').captcha({
            callback: function () {
                publishArticle(that)
            }
        });
        {else/}
        publishArticle(that)
        {/if}})

        function publishArticle (){
            let form = $('#question_form');
            $('.saveArticle').attr('disabled',true);
            $.ajax({
                url: form.attr('action'),
                dataType: 'json',
                type: 'post',
                data: form.serialize(),
                success: function (result) {
                    if(result.code)
                    {
                        AWS_MOBILE.api.success(result.msg, result.url);
                    }else{
                        $('.saveArticle').attr('disabled',false);
                        AWS_MOBILE.api.error(result.msg, result.url);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            })
        }
</script>
{/block}
{block name="sideMenu"}{/block}
{block name="footer"}

{/block}