{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title">{:L('编辑话题')}</div>
    <div class="aui-header-right">
        <a class="text-primary" onclick="AWS_MOBILE.api.ajaxForm('#topicForm')" href="javascript:;">{:L('保存')}</a>
    </div>
</header>
{/block}
{block name="main"}
<div class="main-container mt-1 bg-white">
    <form method="post" action="{:url('topic/manager')}" class="mb-5" id="topicForm">
        <div class="p-3">
            <input type="hidden" name="topic_id" value="{$info.id}">
            {:token_field()}
            <div class="form-group">
                <strong >{:L('话题名称')}:</strong>
                <div class="mt-2">
                    <input class="aw-form-control" type="text" name="title" placeholder="{:L('请输入话题名称')}" value="{$info.title}">
                </div>
            </div>
            <div class="form-group">
                <strong >{:L('话题摘要')}:</strong>
                <div class="mt-2">
                    <textarea class="aw-form-control" style="height: auto" name="seo_description" rows="4" placeholder="{:L('请填写话题描述')}" >{$info.seo_description}</textarea>
                </div>
            </div>
            <div class="mb-3">
                <strong class="mb-2">{:L('话题封面')}:<span class="font-8 text-danger">（* {:L('建议图片尺寸')}1:1）</span></strong>
                <div class="mt-2 clearfix">
                    <div id="fileList_cover" class="uploader-list"></div>
                    <div id="filePicker_cover" class="position-relative">
                        <a href="{$info['pic']|default='/static/common/image/default-cover.svg'}" target="_blank">
                            <img class="image_preview_info" src="{$info['pic']|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                        </a>
                    </div>
                    <input type="hidden" name="pic" value="{$info['pic']|default='/static/common/image/default-cover.svg'}" class="article-cover">
                </div>
            </div>
            <div class="mb-3">
                <strong>{:L('话题别名')}:</strong>
                <div class="mt-2">
                    <input class="aw-form-control" type="text" name="url_token" placeholder="{:L('填写话题别名')}" value="{$info.url_token}">
                </div>
            </div>
            <div class="mb-3">
                <strong>{:L('话题SEO标题')}:</strong>
                <div class="mt-2">
                    <input class="aw-form-control" type="text" name="seo_title" placeholder="{:L('填写话题SEO标题')}" value="{$info.seo_title}">
                </div>
            </div>

            <div class="mb-3">
                <strong>{:L('话题关键词')}:</strong>
                <div class="mt-2">
                    <input class="aw-form-control" type="text" name="seo_keywords" placeholder="{:L('填写话题关键词')}" value="{$info.seo_keywords}">
                </div>
            </div>
            <div class="form-group">
                <strong >{:L('话题详情')}:</strong>
                <div class="mt-2">
                    {:hook('editor',['name'=>'description','cat'=>'topic','value'=>$info['description'],'access_key'=>$access_key])}
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    AWS_MOBILE.upload.webUpload('filePicker_cover','cover_preview','pic','topic');
</script>

{/block}
{block name="footer"}{/block}