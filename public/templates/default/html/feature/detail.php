{extend name="$theme_block" /}
{block name="header"}
<div class="w-top-img" style="height:300px;background: url('{$info.image}') center center;background-size:cover;">
    {include file="global/nav"}
</div>
{/block}
{block name="main"}
<div class="container px-0" style="margin-top: -80px">
    <div class="bg-white p-3" style="border-radius: 10px">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb px-0 pt-0" style="background: transparent">
                <li class="breadcrumb-item"><a href="{$baseUrl}">{:L('社区')}</a></li>
                <li class="breadcrumb-item"><a href="{:url('feature/index')}">{:L('专题列表')}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$info.title}</li>
            </ol>
        </nav>

        <h2 class="font-weight-bolder">{$info.title}</h2>
        <p class="w-100 text-muted mt-3">{$info.description|raw}</p>
        <div class="page-detail-topic mt-3">
            {if !empty($topics)}
            <ul id="awTopicList" class="d-inline p-0">
                {volist name="$topics" id="v"}
                <li class="d-inline-block aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                {/volist}
            </ul>
            {/if}
        </div>
    </div>
</div>

<div class="container aw-wrap mt-2">
    <div class="row">
        <div class="aw-left col-md-9 col-sm-12 px-0">
            <div class="aw-nav-container clearfix bg-white pl-3">
                <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
                    <li class="nav-item"><a class="nav-link {if $sort=='new'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'new'])}">{:L('专题动态')}</a></li>
                    <li class="nav-item"><a class="nav-link {if $sort=='hot'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'hot'])}">{:L('热门内容')}</a></li>
                    <li class="nav-item"><a class="nav-link {if $sort=='best'}active{/if}" data-pjax="tabMain" href="{:url('feature/detail',['token'=>$info['url_token'],'sort'=>'best'])}">{:L('最佳回复')}</a></li>
                </ul>
            </div>
            <div class="tab-content" id="tabMain">
                <div class="bg-white">
                    <div class="aw-common-list">
                        {:widget('common/feature_content',['sort'=>$sort,'feature_id'=>$info['id']])}
                    </div>
                </div>
            </div>
        </div>

        <div class="aw-right radius col-md-3 px-xs-0 pr-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}

            {:widget('sidebar/writeNav')}

            {:widget('sidebar/announce')}

            {:widget('sidebar/focusTopic',['uid'=>$user_id])}

            {:widget('sidebar/hotTopic',['uid'=>$user_id])}

            {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}

            {:widget('sidebar/hotUsers',['uid'=>$user_id])}

            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>
{/block}