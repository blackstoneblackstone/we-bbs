{extend name="$theme_block" /}
{block name="main"}
<div class="container mt-2">
    <div class="bg-white p-3 aw-overflow-auto">
        {if $info['type']=='question' || $info['type']=='modify_question'}
        <dl>
            <dt class="mb-2">{:L('问题标题')}</dt>
            <dd>{$data.title}</dd>
        </dl>
        {if isset($category_list[$data['category_id']]) && $category_list[$data['category_id']]}
        <dl>
            <dt class="mb-2">{:L('问题分类')}</dt>
            <dd><span class="badge badge-primary">{$category_list[$data['category_id']]}<span</dd>
        </dl>
        {/if}
        {if $topics}
        <dl>
            <dt class="mb-2">{:L('问题话题')}</dt>
            <dd>
                <div class="page-detail-topic">
                    <ul id="awTopicList" class="d-inline p-0">
                        {volist name="$topics" id="v"}
                        <li class="d-inline-block aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                        {/volist}
                    </ul>
                </div>
            </dd>
        </dl>
        {/if}

        <dl>
            <dt class="mb-2">{:L('问题详情')}</dt>
            <dd class="aw-content">{$data['detail']|htmlspecialchars_decode|raw}</dd>
        </dl>
        {/if}

        {if $info['type']=='article' || $info['type']=='modify_article'}
        <dl>
            <dt class="mb-2">{:L('文章标题')}</dt>
            <dd>{$data.title}</dd>
        </dl>
        {if isset($category_list[$data['category_id']]) && $category_list[$data['category_id']]}
        <dl>
            <dt class="mb-2">{:L('文章分类')}</dt>
            <dd><span class="badge badge-primary">{$category_list[$data['category_id']]}<span</dd>
        </dl>
        {/if}
        {if isset($data['column_id']) && $data['column_id']}
        <dl>
            <dt class="mb-2">{:L('所属专栏')}</dt>
            <dd><span class="badge badge-primary">{$column_list[$data['column_id']]}<span</dd>
        </dl>
        {/if}

        {if $topics}
        <dl>
            <dt class="mb-2">{:L('文章话题')}</dt>
            <dd>
                <div class="page-detail-topic mb-2">
                    <ul id="awTopicList" class="d-inline p-0">
                        {volist name="$topics" id="v"}
                        <li class="d-inline-block aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                        {/volist}
                    </ul>
                </div>
            </dd>
        </dl>
        {/if}

        <dl>
            <dt class="mb-2">{:L('文章详情')}</dt>
            <dd class="aw-content">{$data['message']|htmlspecialchars_decode|raw}</dd>
        </dl>
        {/if}

        {if $info['type']=='answer' || $info['type']=='modify_answer'}
        <dl>
            <dt class="mb-2">{:L('问题标题')}</dt>
            <dd><a href="{:url('question/detail',['id'=>$data['question_id']])}" target="_blank">{$data.title}</a></dd>
        </dl>
        <dl>
            <dt class="mb-2">{:L('回答详情')}</dt>
            <dd class="aw-content">{$data['content']|htmlspecialchars_decode|raw}</dd>
        </dl>
        {/if}

        {if $info['status']==2}

        {if $info['reason']}
        <dl>
            <dt class="mb-2">{:L('拒绝原因')}</dt>
            <dd class="aw-content text-danger">{$info['reason']|raw}</dd>
        </dl>
        {/if}

        <!--<a href="{:url()}" target="_blank" class="btn btn-primary px-4">修改内容</a>-->
        {/if}
    </div>
</div>
{/block}