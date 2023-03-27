{extend name="$theme_block" /}
{block name="main"}
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 mb-2">
            <div class="card border-0">
                <div class="card-body">
                    <form class="aw-form" method="post" action="{:url('article/publish')}" id="ajaxForm">
                        <input type="hidden" name="id" value="{$article_info.id|default=0}">
                        <!--<input type="hidden" name="wait_time">-->
                        <input type="hidden" name="access_key" value="{$access_key}">
                        <input type="hidden" id="captcha">
                        {:token_field()}
                        <div class="form-group d-flex mb-3">
                            <div class="flex-fill">
                                <input class="aw-form-control" type="text" name="title" value="{$article_info['title']|default=''}" placeholder="{:L('文章标题')}">
                            </div>
                            {if($column_list)}
                            <div class="flex-fill ml-2">
                                <select class="aw-form-control" name="column_id">
                                    <option value="0">{:L('选择专栏')}</option>
                                    {volist name="column_list" id="v"}
                                    <option value="{$v.id}" {if isset($article_info['column_id']) && $v['id']==$article_info['column_id']}selected{/if}>{$v.name}</option>
                                    {/volist}
                                </select>
                            </div>
                            {/if}
                            {if $article_category_list && $setting.enable_category}
                            <div class="flex-fill ml-2" style="max-width: 150px">
                                <select class="aw-form-control" name="category_id">
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
                            {/if}
                        </div>
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
                            <div class="page-detail-topic">
                                <ul class="d-inline p-0" id="awTopicList">
                                    {if !empty($article_info['topics'])}
                                    {volist name="article_info['topics']" id="v"}
                                    <li class="d-inline-block aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}"><em class="tag">{$v.title}</em></a></li>
                                    {/volist}
                                    <input type="hidden" name="topics" value="{:implode(',',array_column($article_info['topics'],'id'))}">
                                    {/if}
                                </ul>
                                <a href="javascript:;" class="text-primary font-weight-bold aw-ajax-open d-inline" data-url="{:url('topic/select',['item_type'=>'article','item_id'=>isset($article_info['id']) ? $article_info['id'] : 0])}"><i class="icon-add"></i>{:L('添加话题')}</a>{if(get_setting('topic_enable')=='Y')} <span class="font-9 text-primary">({:L('至少添加一个')})</span>{/if}
                            </div>
                        </div>
                        <div class="form-group mb-3 aw-content">
                            <label class="mb-3">{:L('文章详情')}</label>
                            {:hook('editor',['name'=>'message','cat'=>'article','value'=>isset($article_info['message']) ? $article_info['message']:'','access_key'=>$access_key])}
                        </div>
                        {if get_plugins_config('paid_attach','enable')=='Y'}
                        {:hook('attachPublish',['info'=>$article_info,'page'=>'article','attach_list'=>$attach_list??[],'access_key'=>$access_key])}
                        {else/}
                        <div class="aw-attach-upload mb-3" data-path="article_attach">
                            <a  class="text-primary cursor-pointer font-weight-bold" id="testList" style="cursor: pointer"><i class="fas fa-cloud-upload-alt"></i> {:L('选择附件')}</a>
                            <input class="layui-upload-file" type="file" accept="" name="file" multiple="">
                            <span class="text-danger font-8">({:L('允许上传文件类型')}:{:get_setting('upload_file_ext')})</span>
                            <a class="cursor-pointer btn btn-primary text-white px-3 btn-sm ml-3 font-weight-bold float-right" id="uploadListAction" style="cursor: pointer">{:L('开始上传')}</a>
                            <div class="attach-upload-list mt-3">
                                <table class="layui-table">
                                    <thead>
                                    <tr>
                                        <th>{:L('文件名')}</th>
                                        <th>{:L('大小')}</th>
                                        <th>{:L('上传进度')}</th>
                                        <th>{:L('操作')}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="attachList">
                                    {if $attach_list && isset($article_info['id'])}
                                    {volist name="$attach_list" id="v"}
                                    <tr>
                                        <td>{$v.name}</td>
                                        <td>{:formatBytes($v.size)}</td>
                                        <td>
                                            <span class="text-success">{:L('上传成功')}</span>
                                        </td>
                                        <td><button type="button" data-id="{$v.id}" data-key="{$v.access_key}" class="layui-btn layui-btn-xs layui-btn-danger aw-attach-delete">{:L('删除')}</button></td>
                                    </tr>
                                    {/volist}
                                    {/if}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {/if}

                        <!--发布附加钩子-->
                        {:hook('publish_extend',['info'=>$article_info,'page'=>'article'])}
                        <!--发布附加钩子-->

                        <div class="mt-4 clearfix">
                            <a href="{:url('page/score')}" target="_blank" ><i class="fa fa-database"></i> {:get_setting("score_unit")}{:L('规则')}</a>
                            <button type="button" class="btn btn-primary btn-sm px-4 aw-article-publish ml-3 float-right">{:L('发表文章')}</button>
                            {if get_setting('auto_save_draft')=='Y'}
                            <script>
                                //自动保存时间间隔
                                let AutoSaveTime = parseInt("{:get_setting('auto_save_draft_time')}")*360;
                                //设置自动保存
                                setInterval(function (item_type,itemId){
                                    var formData = AWS.common.formToJSON('ajaxForm');
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
                                                AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                            }
                                        }
                                    });
                                }, AutoSaveTime);
                            </script>
                            {/if}
                            <button type="button" onclick="AWS.User.draft(this,'article','{$article_info.id|default=0}')" class="btn btn-outline-primary px-4 btn-sm ml-3 float-right">{:L('存草稿')}</button>
                            <button type="button" data-url="{:url('article/preview')}" class="btn btn-outline-primary btn-sm aw-preview px-4 float-right">{:L('预览')}</button>
                        </div>

                        {if !isset($article_info['id'])}
                        <script id="timing-publish-modal" type="text/html">
                            <div class="rounded p-3">
                                <div class="form-group">
                                    <label for="timing"><input type="text" id="timing" placeholder="{:L('选择定时发布时间')}" class="form-control"></label>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm px-4 mr-3 select-choose">{:L('确定选择')}</button>
                            </div>
                        </script>
                        {/if}
                    </form>
                </div>
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            {:hook('content_ocr',['type'=>'article','element'=>''])}
            <div class="r-box mb-2">
                <div class="r-title">
                    <h4>{:L('发布说明')}</h4>
                </div>
                <div class="pb-2 fbsm">
                    <dl class="text-muted font-9">
                        <dt>{:L('文章标题')}：</dt>
                        <dd>{:L('请用准确的语言描述您发布的文章思想')}</dd>
                    </dl>

                    <dl class="text-muted font-9">
                        <dt>{:L('文章补充')}：</dt>
                        <dd>{:L('详细补充您的文章内容')}, {:L('并提供一些相关的素材以供参与者更多的了解您所要文章的主题思想')}</dd>
                    </dl>

                    <dl class="text-muted font-9">
                        <dt>{:L('选择话题')}：</dt>
                        <dd>{:L('选择一个或者多个合适的话题,让您发布的文章得到更多有相同兴趣的人参与,所有人可以在您发布文章之后添加和编辑该文章所属的话题')}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let ACCESS_KEY = "{$access_key}";
    let ATTACH_LEN = parseInt('{:count($attach_list)}');
    let ITEM_ID = parseInt("{$article_info['id']??0}");
    let SYS_ATTACH = "{:get_plugins_config('paid_attach','enable')=='Y' ? 1 : 0}";
</script>
{/block}
{block name="script"}
<script>
    $('.aw-article-publish').click(function (){
        var that = this;
        {if $captcha_enable}
        $('#captcha').captcha({
            callback: function () {
                publishArticle(that)
            }
        });
        {else/}
            publishArticle(that)
            {/if}
            })
</script>
{/block}