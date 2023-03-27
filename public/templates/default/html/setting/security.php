{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>input('uid')])}
            <div class="col-md-10" id="wrapMain">
                {include file="setting/nav"}
                <div class="bg-white mt-1 p-3" id="tabMain">
                    <div class="form-group row mt-3">
                        <label class="col-md-2 col-form-label"> {:L('账号密码')} </label>
                        <div class="col-md-6 form-inline">
                            <input type="password" class="form-control" placeholder="******" name="password" value="">
                            <button class="btn btn-primary aw-ajax-open ml-1" data-url="{:url('account/modify_password')}">{:L('修改密码')}</button>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label"> {:L('交易密码')} </label>
                        <div class="col-md-6 form-inline">
                            <input type="password" class="form-control" placeholder="******" name="deal_password" value="">
                            <button class="btn btn-primary aw-ajax-open ml-1" data-url="{:url('account/modify_deal_password')}">{:L('修改交易密码')}</button>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>
{/block}