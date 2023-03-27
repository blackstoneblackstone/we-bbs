{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="aw-left col-md-9 col-sm-12 px-0">
				<div class="bg-white p-3">
					<form method="post" action="{:url('column/apply')}">
                        <input type="hidden" name="id" value="{$info.id|default=0}">
						{:token_field()}
						<div class="form-group">
                            {:L('专栏封面')}:<span class="font-8 text-danger">（* {:L('建议图片尺寸')}1:1）</span>
							<div class="aw-width-1-3 mt-3">
                                <div class="column-cover-box">
                                    <div id="fileList_cover" class="uploader-list"></div>
                                    <div id="filePicker_cover">
                                        <a href="/static/common/image/default-cover.svg" target="_blank">
                                            <img class="image_preview_info rounded" src="{$info.cover|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                        </a>
                                    </div>
                                    <input type="hidden" name="cover" value="{$info.cover|default='/static/common/image/default-cover.svg'}" class="article-cover">
                                </div>
							</div>&nbsp;&nbsp;&nbsp;
						</div>

						<div class="form-group">
                            <input class="aw-form-control" type="text" value="{$info.name|default=''}" name="name" placeholder="{:L('专栏名称')}">
                        </div>

						<div class="form-group">
							<textarea class="aw-form-control" style="height: auto;line-height: normal" name="description" id="content_text" rows="5" placeholder="{:L('专栏简介')}">{$info.description|default=''}</textarea>
						</div>

						<div class="form-group">
							<button type="button" class="btn btn-primary aw-ajax-form px-4">{:L('提交')}</button>
						</div>
					</form>
				</div>
			</div>
            <div class="aw-right col-md-3 col-sm-12">
                <div class="r-box mb-2">
                    <div class="r-title">
                        <h4>{:L('申请说明')}</h4>
                    </div>
                    <div class="pb-2">
                        <dl class="text-muted font-9">
                            <dt>{:L('专栏名称')}：</dt>
                            <dd>{:L('请用准确的语言描述您的专栏名称')}</dd>
                        </dl>
                        <dl class="text-muted font-9">
                            <dt>{:L('专栏简介')}：</dt>
                            <dd>{:L('详细补充您的专栏介绍,并提供一些相关的素材以供参与者更多的了解您专栏的主题思想')}</dd>
                        </dl>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
{/block}