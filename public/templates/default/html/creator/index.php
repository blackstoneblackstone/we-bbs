{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>$user_id])}
            <div class="col-md-10" id="wrapMain">
                <div class="card border-0">
                    <div class="card-body d-flex">
                        <div class="bg-white mb-1 flex-fill mr-4" style="max-width: 120px">
                            <a href="{:url('setting/profile')}" class="d-block mb-2">
                                <img class="rounded" src="{$user_info.avatar ? : '/static/libs/aw-core/media/avatars/avatar0.jpg'}" style="width:120px;height: 120px">
                            </a>
                        </div>
                        <div class="flex-fill">
                            <h5 class="mb-0 name">{$user_info.nick_name}</h5>
                            <p><span class="badge badge-success">{$user_info.group_name}</span></p>
                            <div class="row">
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{$setting.score_unit}:{$user_info.integral}</span>
                                </div>
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{$setting.power_unit}:{$user_info.reputation}</span>
                                </div>
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{:L('粉丝')}:{$user_info.fans_count}</span>
                                </div>
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{:L('好友')}:{$user_info.friend_count}</span>
                                </div>
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{:L('获赞')}:{$user_info.agree_count}</span>
                                </div>
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{:L('问题')}:{$user_info.question_count}</span>
                                </div>
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{:L('文章')}:{$user_info.article_count}</span>
                                </div>
                                <div class="col-md-3 my-2">
                                    <span class="d-block text-muted">{:L('回答')}:{$user_info.answer_count}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--用户中心主页钩子-->
                {:hook('userCenterIndex')}
            </div>
        </div>
    </div>
</div>
{/block}