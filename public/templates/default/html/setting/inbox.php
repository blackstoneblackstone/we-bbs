{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>input('uid')])}
            <div class="col-10"  id="wrapMain">
                {include file="setting/nav"}

			</div>
		</div>
	</div>
</div>
{/block}