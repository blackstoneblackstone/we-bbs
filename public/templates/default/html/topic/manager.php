{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <form method="post" action="{:url('topic/manager')}">
            <div class="row">
                <div class="aw-left col-md-9 col-sm-12 bg-white">
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
                        <div class="form-group">
                            <strong >{:L('话题详情')}:</strong>
                            <div class="mt-2">
                                {:hook('editor',['name'=>'description','cat'=>'topic','value'=>$info['description'],'access_key'=>$access_key])}
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary px-3 btn-sm aw-ajax-form mr-3">{:L('提交修改')}</button>
                        </div>
                    </div>
                </div>
                <div class="aw-right col-md-3 col-sm-12">
                    <div class="bg-white p-3">
                        <div class="mb-3">
                            <strong class="mb-2">{:L('话题封面')}:<span class="font-8 text-danger">（* {:L('建议图片尺寸')}1:1）</span></strong>
                            <div class="mt-2 clearfix">
                                <div id="fileList_cover" class="uploader-list"></div>
                                <div id="filePicker_cover">
                                    <a href="{$info['pic']|default='/static/common/image/default-cover.svg'}" target="_blank">
                                        <img class="image_preview_info" src="{$info['pic']|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                    </a>
                                </div>
                                <input type="hidden" name="pic" value="{$info['pic']|default='/static/common/image/default-cover.svg'}" class="article-cover">
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong >{:L('话题别名')}:</strong>
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
                        <div class="mb-3">
                            <strong>{:L('关联话题')}:</strong>
                            <div class="mt-2">
                                <div class="page-detail-topic">
                                    <ul class="d-block py-1" id="awTopicList">
                                        {if !empty($info['relation_topics'])}
                                        {volist name="$info['relation_topics']" id="v"}
                                        <li class="d-inline-block my-1 aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}"><em class="tag">{$v.title}</em></a></li>
                                        {/volist}
                                        <input type="hidden" name="topics" value="{:implode(',',array_column($info['relation_topics'],'id'))}">
                                        {/if}
                                    </ul>
                                    <a href="javascript:;" class="text-primary mt-2 font-9 aw-ajax-open d-block" data-url="{:url('ajax/merge_topic',['item_id'=>isset($info['id']) ? $info['id'] : 0])}" data-title="相关话题"><i class="icon-add"></i>
                                        {:L('关联话题')}</a>&nbsp;&nbsp;<span class="font-9 text-muted"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
	</div>
</div>
<script>
    //上传文章封面
    AWS.upload.webUpload('filePicker_cover','cover_preview','pic','topic');
</script>

{/block}
