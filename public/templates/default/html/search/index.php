{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container" >
        <div class="row">
            <div class="aw-left col-md-9 col-sm-12 px-0 mb-1" id="SearchResultMain">
                <div class="position-relative">
                    <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block searchTypeTab px-3">
                        <li class="nav-item"><a class="nav-link {if $type=='all' || !$type}active{/if}" data-pjax="SearchResultMain" href="{:url('search/index',['q'=>$keywords,'sort'=>$sort,'type'=>'all'])}">{:L('全部')}</a></li>
                        {volist name="tab_list" id="v"}
                        <li class="nav-item"><a class="nav-link {if $type==$v.name}active{/if}" data-pjax="SearchResultMain" href="{:url('search/index',['q'=>$keywords,'sort'=>$sort,'type'=>$v.name])}">{:L($v.title)}</a></li>
                        {/volist}
                    </ul>
                </div>
                <div class="bg-white">
                    <div class="search-detail-list bg-white" id="tabMain">
                        <div class="search-detail-info mb-2 clearfix px-3 pt-3">
                            <div class="search-discuss-info">
                                "<em style="font-style: normal;color: red">{:urldecode($keywords)}</em>" {:L('搜索结果')},{:L('为您找到约')} {$total} {:L('条结果')}
                            </div>
                        </div>
                        {if !empty($list)}
                        <div class="aw-common-list">
                            {volist name="$list" id="v"}
                            {if $v['search_type']=="question" }
                            <dl>
                                <dt>
                                    {if $v.is_anonymous}
                                    <a href="javascript:;" class="aw-username">
                                        <img src="/static/common/image/default-avatar.svg"  onerror="this.src='/static/common/image/default-avatar.svg'"  class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                                        <img src="{$v['user_info']['avatar']}"  onerror="this.src='/static/common/image/default-avatar.svg'"  class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('发起了提问')}</i>
                                    <em class="time">{:date_friendly($v['create_time'])}</em>
                                    {if $v['topics']}
                                    <div class="tag d-inline-block">
                                        {volist name="$v['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                        {/volist}
                                    </div>
                                    {/if}
                                </dt>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s1 badge badge-secondary">{:L('问答')}</span>
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title|raw}</a>
                                    </div>
                                    <div class="pcon {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-12 t-imglist row">
                                            {volist name="$v['img_list']" id="img" key="k"}
                                            {if($k<4)}
                                            <div class="col-md-4 aw-list-img">
                                                <img src="{$img|default='/static/common/image/default-cover.svg'}" class="rounded w-100 aw-cut-img" style="margin-bottom: 5px;" >
                                            </div>
                                            {/if}
                                            {/volist}
                                        </div>
                                        <div class="ov-3 col-md-12">
                                            <div class="aw-two-line">
                                                {$v.detail|raw}
                                            </div>
                                        </div>
                                        {else/}
                                        <div class="aw-two-line">
                                            {$v.detail|raw}
                                        </div>
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    <label>
                                        <a type="button" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注问题')} <span class="badge focus-count">{$v.focus_count}</span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i> {$v['comment_count']}评论</label>
                                </dd>
                            </dl>
                            {/if}

                            {if $v['search_type']=="article" }
                            <dl>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s2 badge badge-secondary">{:L('文章')}</span>
                                        {:hook('article_badge')}
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']|raw}</a>
                                    </div>
                                    <div class="pcon {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-3 aw-list-img"><img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%"></div>
                                        <div class="ov-3 col-md-9">
                                            <div class="aw-three-line">
                                                {$v.message|raw}
                                            </div>
                                            {if $v['topics']}
                                            <div class="tags">
                                                {volist name="$v['topics']" id="topic"}
                                                <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                                {/volist}
                                            </div>
                                            {/if}
                                        </div>
                                        {else/}
                                        <div class="aw-three-line">
                                            {$v.message|raw}
                                        </div>
                                        {if $v['topics']}
                                        <div class="tags">
                                            {volist name="$v['topics']" id="topic"}
                                            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                            {/volist}
                                        </div>
                                        {/if}
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    <label class="dz">
                                        <a type="button" class="btn btn-primary btn-sm" onclick="AWS.User.agree(this,'article','{$v['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['agree_count']}</span></a>
                                    </label>
                                    <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {$v['view_count']?:''}{:L('浏览')}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
                                    <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
                                </dd>
                            </dl>
                            {/if}

                            {if $v['search_type']=="users" }
                            <dl class="users d-flex position-relative">
                                <dt class="flex-fill mr-3" style="width: 54px;height: 54px">
                                    <a href="{$v.url}" class="rounded d-block"><img src="{$v.avatar}"  onerror="this.src='/static/common/image/default-avatar.svg'"  alt="{$v.name}" style="width: 54px;height: 54px"></a>
                                    <span class="{$v['is_online'] ? 'online-dot' : 'offline-dot'}"></span>
                                </dt>
                                <dd class="flex-fill w-100 mb-0">
                                    <h3 class="font-11 mb-2"><a href="{$v.url}">{$v.name|raw}</a></h3>
                                    <p class="aw-two-line">{$v['signature']|default=L('这家伙还没有留下自我介绍～')|raw}</p>
                                </dd>
                                {if $user_id && $user_id!=$v.uid}
                                <dd class="position-absolute" style="right: 0;top:10px">
                                    <a href="javascript:;" class="mr-3 text-primary {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'user','{$v.uid}')">
                                        {$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i>'. L('已关注').'</span>' : '<span><i class="icon-plus-circle text-primary"></i> '.L('关注').'</span>'}
                                    </a>
                                    <a href="javascript:;" class="aw-send-inbox text-smooth">{:L('私信')}</a>
                                </dd>
                                {/if}
                            </dl>
                            {/if}

                            {if $v['search_type']=="topic" }
                            <dl class="topic position-relative clearfix d-flex">
                                <dt class="flex-fill" style="max-width: 60px;margin-right: 15px">
                                    <a href="{:url('topic/detail',['id'=>$v['id']])}">
                                        <img src="{$v['pic']|default='/static/common/image/topic.svg'}" onerror="this.src='/static/common/image/topic.svg'" class="rounded" style="width: 60px;height: 60px">
                                    </a>
                                </dt>
                                <dd class="flex-fill" style="width: calc(100% - 75px);">
                                    <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold"># {$v.title|raw}</a>
                                    <div class="mb-0 font-8 aw-one-line">{$v.description|raw}</div>
                                    <p class="font-8 text-muted">
                                        <span class="mr-3">{:L('正在讨论')}：{$v.discuss}</span>
                                        <span class="mr-3"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
                                        {if $user_id}
                                        <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> '.L('已关注').'</span>' : '<span><i class="icon-plus-circle text-primary"></i> '.L('关注').'</span>'}</a>
                                        {/if}
                                    </p>
                                </dd>
                            </dl>
                            {/if}

                            {if $v['search_type']=="answer" }
                            <dl>
                                <dt>
                                    {if (!$v['answer_info'])}
                                    {if $v.is_anonymous}
                                    <a href="javascript:;" class="aw-username">
                                        <img src="/static/common/image/default-avatar.svg" onerror="this.src='/static/common/image/default-avatar.svg'" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                                        <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('发起了提问')}</i>
                                    <em class="time">{:date_friendly($v['create_time'])}</em>
                                    {else/}
                                    {if $v['answer_info']['is_anonymous']}
                                    <a href="javascript:;" class="aw-username" >
                                        <img src="/static/common/image/default-avatar.svg" onerror="this.src='/static/common/image/default-avatar.svg'" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                                    </a>
                                    {else/}
                                    <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name" data-pjax="WrapBody">
                                        <img src="{$v['answer_info']['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['answer_info']['user_info']['name']}">{$v['answer_info']['user_info']['name']}
                                    </a>
                                    {/if}
                                    <i>{:L('回复了问题')}（{$v['answer_count']}{:L('回复')}）</i>
                                    <em class="time">{:date_friendly($v['answer_info']['create_time'])}</em>
                                    {/if}
                                    {if $v['topics']}
                                    <div class="tag d-inline-block">
                                        {volist name="$v['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                        {/volist}
                                    </div>
                                    {/if}
                                </dt>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s1 badge badge-secondary">{:L('问答')}</span>
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        {if (!$v['answer_info'])}
                                        <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title|raw}</a>
                                        {else/}
                                        <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}">{$v.title|raw}</a>
                                        {/if}
                                    </div>
                                    <div class="pcon {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-12 t-imglist row">
                                            {volist name="$v['img_list']" id="img" key="k"}
                                            {if($k<4)}
                                            <div class="col-md-4 aw-list-img">
                                                <img src="{$img|default='/static/common/image/default-cover.svg'}" class="rounded w-100 aw-cut-img" style="margin-bottom: 5px;" >
                                            </div>
                                            {/if}
                                            {/volist}
                                        </div>
                                        <div class="ov-3 col-md-12">
                                            <div class="aw-two-line">
                                                {$v.detail|raw}
                                            </div>
                                        </div>
                                        {else/}
                                        <div class="aw-two-line">
                                            {$v.detail|raw}
                                        </div>
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    {if (!$v['answer_info'])}
                                    <label>
                                        <a type="button" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注问题')} <span class="badge focus-count">{$v.focus_count}</span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
                                    {else/}
                                    <label class="dz">
                                        <a type="button" class="btn btn-primary btn-sm aw-ajax-agree  {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['answer_info']['agree_count']}</span></a>
                                        <a type="button" class="btn btn-primary btn-sm aw-ajax-against  {$v['answer_info']['vote_value']==-1 ? 'active' : ''}" title="{:L('反对回答')}" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>{:L('反对')}<span class="badge"></span></a>
                                    </label>
                                    <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢', $v['answer_info']['thanks_count'])}</label>
                                    <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['answer_info']['comment_count']}{:L('评论')}</label>
                                    {/if}
                                </dd>
                            </dl>
                            {/if}

                            {:hook('search_template_'.$v['search_type'],$v)}

                            {/volist}
                        </div>
                        {$page|raw}
                        {else/}
                        <div class="searchNotFound p-3">
                            <p>{:L('找不到和')} <em style="font-style: normal;color: red">{$keywords}</em> {:L('相符的内容或信息')}。</p>
                            <p>{:L('建议您')}：</p>
                            <ul class="list-unstyled text-muted">
                                <li>1.{:L('请检查输入字词有无错误')}。</li>
                                <li>2.{:L('请换用另外的查询字词')}。</li>
                                <li>3.{:L('请改用较短')}、{:L('较为常见的字词')}。</li>
                            </ul>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="aw-right col-md-3 col-sm-12 px-xs-0">
                <!--侧边栏顶部钩子-->
                {:hook('sidebarTop')}
                {if $search_list}
                <div class="r-box mb-2 hot-topic">
                    <div class="r-title">
                        <h4>{:L('热门搜索')}</h4>
                    </div>
                    <div class="hot-list hot-yh-list pb-2">
                        <div class="sidebarFocusTopic aw-tag">
                            {foreach $search_list as $key=>$v}
                            <a href="{:url('search/index',['q'=>$v['keyword']])}" class="topic-btn d-inline-block mb-2"><em class="tag">{$v.keyword}</em></a>
                            {/foreach}
                        </div>
                    </div>
                </div>
                {/if}

                <!--侧边栏底部钩子-->
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
<script>
    $(function (){
        $(document).on('click', '.searchSortTab li a', function()
        {
            $('.searchSortTab li').removeClass('active');
            $(this).parent('li').addClass('active');
            $('.searchSortTabText span').text($(this).text());
        });
    });
</script>
{/block}