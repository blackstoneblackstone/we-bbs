{extend name="$theme_block" /}
{block name="main"}
<style>
    .topic-item  dl{overflow: hidden;margin-bottom: 0;padding: 0.6rem 0;    border-bottom: 1px solid #EFEFEF;}
    .topic-item  dl:last-child{border:none;}
    .topic-item  dt{width: 74px;height: 74px;float: left}
    .topic-item  dt img{width: 74px !important;height: 74px !important;}
    .topic-item  dd{float: right;width: calc(100% - 84px);margin-bottom: 0;height: 74px}
    .topic-item  dd p{color: #999}
    .center-main .swiper-slide {width: auto!important}
</style>
<div class="container mt-2">
    <div class="row">
        <div class="col-md-9 aw-left mb-2 center-main">
            <div class="media mb-2 p-3 bg-white rounded">
                <div class="authorAvatar mr-3">
                    <img src="{$user.avatar}" onerror="this.src='/static/common/image/default-avatar.svg'">
                </div>
                <div class="media-body">
                    <div>
                        <dl class="mb-0">
                            <dd>
                                <h2 class="mb-1 font-12">
                                    <a href="{$user.url}">
                                        {$user.name}
                                        <span class="badge badge-success">{$user.group_name}</span>
                                        {if $user.verified_icon}
                                        <img src="{$user.verified_icon}" style="width: 18px;height: 18px">
                                        {/if}
                                    </a>
                                    {if $user.status==3}
                                    <span class="badge badge-danger">{:L('已封禁')}</span>
                                    {/if}
                                </h2>
                            </dd>
                            <dd class="aw-two-line">{$user.signature|default=L('这家伙还没有留下自我介绍')}</dd>
                            <dd>
                                {$user.sex==1 ? L('男') : ($user.sex==2 ? L('女') : L('保密'))}
                            </dd>
                            <dd style="font-size:12px;">
                                <span>{:L('关注')}: {$user.friend_count}</span>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>{:L('粉丝')}: {$user.fans_count}</span>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>{:L($setting.score_unit)}: {$user.integral}</span>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>{:L($setting.power_unit)}: {$user.reputation}</span>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>{:L('访问')}: {$user.views_count}</span>{if $user_id==$user.uid}&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>{:L('余额')}: {$user.money}</span>{/if}
                            </dd>
                            {if $user_id}
                            <div class="aw-home-user-btn" style="right: 10px;bottom: 20px">
                                {if $user_id==$user.uid}
                                <a class="btn btn-primary btn-sm px-4" href="{:url('member/setting/profile')}">{:L('编辑资料')}</a>
                                {else/}
                                <a class="btn btn-primary btn-sm px-4 {if $user['has_focus']}active{/if} {$user['has_focus'] ? 'ygz' : 'gz'}" href="javascript:;"
                                   onclick="AWS.User.focus(this,'user','{$user.uid}')">{$user['has_focus'] ? L('已关注') : L('关注')}</a>
                                <a  class="btn btn-outline-primary btn-sm px-4 mr-2" href="javascript:;" onclick="AWS.User.inbox('{$user.nick_name}')">{:L('私信')}</a>
                                {/if}

                                {:hook('peopleDetailExtendBtn',$user)}
                            </div>
                            {/if}
                        </dl>
                    </div>
                </div>
            </div>
            <div class="swiper-container bg-white">
                <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-3 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
                    <li class="nav-item swiper-slide {if $type=='dynamic'}i-active{/if}">
                        <a class="nav-link {if $type=='dynamic'}active{/if}" data-pjax="tabMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'dynamic'])}">{:L('动态')}</a>
                    </li>
                    <li class="nav-item swiper-slide {if $type=='question'}i-active{/if}">
                        <a class="nav-link {if $type=='question'}active{/if}" data-pjax="tabMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'question'])}">{:L('提问')} {$question_count}</a>
                    </li>
                    <li class="nav-item swiper-slide {if $type=='answer'}i-active{/if}">
                        <a class="nav-link {if $type=='answer'}active{/if}" data-pjax="tabMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'answer'])}">{:L('回答')} {$answer_count}</a>
                    </li>
                    <li class="nav-item swiper-slide {if $type=='article'}i-active{/if}">
                        <a class="nav-link {if $type=='article'}active{/if}" data-pjax="tabMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'article'])}">{:L('文章')} {$article_count}</a>
                    </li>
                    <li class="nav-item swiper-slide {if $type=='friend'}i-active{/if}">
                        <a class="nav-link {if $type=='friend'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'friend'])}" data-pjax="tabMain">{:L('关注的人')}</a>
                    </li>
                    <li class="nav-item swiper-slide {if $type=='fans'}i-active{/if}">
                        <a class="nav-link {if $type=='fans'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'fans'])}" data-pjax="tabMain">{:L('关注TA的')}</a>
                    </li>
                    <li class="nav-item swiper-slide {if $type=='column'}i-active{/if}">
                        <a class="nav-link {if $type=='column'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'column'])}" data-pjax="tabMain">{:L('关注的专栏')}</a>
                    </li>
                    <li class="nav-item swiper-slide {if $type=='topic'}i-active{/if}">
                        <a class="nav-link {if $type=='topic'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'topic'])}" data-pjax="tabMain">{:L('关注的话题')}</a>
                    </li>
                    {:hook('people_nav',['user'=>$user])}
                </ul>
            </div>
            <div class="tab-content bg-white bg-white" id="tabMain">
                <div class="tab-pane fade show active">
                    <div class="aw-common-list">
                        {:widget('member/getUserPost', ['uid' => $user['uid'], 'type' => $type])}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 aw-right px-xs-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}

            <div class="media p-3 shadow-sm bg-white rounded">
                <div class="media-body">
                    <h6 class="font-weight-bold">{:L('个人成就')}</h6>
                    <div class="pl-2">
                        <dl class="text-muted">
                            <dd><i class="iconfont">&#xe870;</i>{:L('获得 %s 次赞同',$user.agree_count)}</dd>
                            <dd>{:L('被 %s 人关注了',$user.fans_count)}</dd>
                            <dd>{:L('关注了 %s 人',$user.friend_count)}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        let k = parseInt($('.i-active').index())
        let navSwiper = new Swiper('.swiper-container', {
            speed: 600,
            grabCursor: true,
            slidesPerView: "auto",
            initialSlide: k,
            slidesPerGroup: 3
        })
    })
</script>
{/block}