<div class="aw-card aw-card-default aw-card-small aw-card-body mb-3">
	<div class="profile-details" style="margin-top: 15px">
		<div class="profile-image">
			<img src="{$user_info.avatar}" alt="">
		</div>
		<div class="profile-details-info mb-3">
			<h1> {$user_info['nick_name']} </h1>
			<p> {$user_info['signature']|default=L('暂无个人简介')}</p>
		</div>
		<div class="profile-meta aw-grid">
			<dl class="aw-width-1-4">
				<dt>{$user_info['question_count']}</dt>
				<dd>{:L('问题')}</dd>
			</dl>
			<dl class="aw-width-1-4">
				<dt>{$user_info['answer_count']}</dt>
				<dd>{:L('回答')}</dd>
			</dl>
			<dl class="aw-width-1-4">
				<dt>{$user_info['article_count']}</dt>
				<dd>{:L('文章')}</dd>
			</dl>
			<dl class="aw-width-1-4">
				<dt>{$user_info['agree_count']}</dt>
				<dd>{:L('获赞')}</dd>
			</dl>
		</div>
        {if $user_info['uid']!=$uid}
        <div class="aw-grid mt-3">
            <div class="aw-width-1-2">
                <a href="javascript:;" class="aw-display-block button aw-focus" data-type="user">{:L('关注')}</a>
            </div>
            <div class="aw-width-expand">
                <a href="javascript:;" class="aw-display-block button aw-send-inbox" data-uid="{$user_info['uid']}">{:L('私信')}</a>
            </div>
        </div>
        {/if}
	</div>
</div>