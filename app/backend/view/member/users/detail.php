{extend name="block" /}
{block name="main"}

<div class="container mt-2">
    <div class="row">
        <div class="col-md-9 aw-left mb-2">
            <div class="media mb-2 p-3 bg-white rounded">
                <div class="authorAvatar mr-3">
                    <img src="{$user.avatar}">
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
                                    <span class="badge badge-danger">已封禁</span>
                                    {/if}
                                </h2>
                            </dd>
                            <dd class="aw-two-line">{$user.signature|default='这家伙还没有留下自我介绍～'}</dd>
                            <dd>
                                性别：{$user.sex==1 ? '男' : ($user.sex==2 ? '女' : '保密')}
                            </dd>
                            <dd>生日：{$user.birthday ? date('Y年m月d日') : ''}</dd>
                            <dd>
                                邮箱：{$user.email}
                            </dd>
                            <dd>手机号码：{$user.mobile}</dd>
                            <dd style="font-size:12px;">
                                <span>关注: {$user.friend_count}</span>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>粉丝: {$user.fans_count}</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>{$setting.score_unit}: {$user.integral}</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>{$setting.power_unit}: {$user.reputation}</span>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<span>访问: {$user.views_count}</span>
                            </dd>
                            <dd style="font-size: 12px">
                                提问：{$question_count} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                                回答：{$answer_count} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                                文章：{$article_count}
                            </dd>
                            <dd style="font-size: 12px">
                                积分：{$user.integral} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                                威望：{$user.reputation}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 aw-right px-xs-0">
            <div class="media p-3 shadow-sm bg-white rounded">
                <div class="media-body">
                    <h6 class="font-weight-bold">个人成就</h6>
                    <div class="pl-2">
                        <dl class="text-muted">
                            <dd><i class="iconfont">&#xe870;</i>获得 {$user.agree_count} 次赞同</dd>
                            <dd>被 {$user.fans_count} 人关注</dd>
                            <dd>关注了 {$user.friend_count} 人</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{/block}