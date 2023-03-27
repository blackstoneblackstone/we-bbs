{extend name="$theme_block" /}
{block name="header"}{/block}
{block name="main"}
<style>
    .message-bubble {
        display: block;
        position: relative;
        margin-bottom: 20px;
    }
    .message-bubble .message-avatar {
        position: absolute;
        left: 0;
        top: 0;
    }
    .message-bubble .message-avatar img {
        width: 50px;
        height: 50px;
    }
    .message-bubble .message-text {
        margin-left: 62px;
        background-color: #f4f4f4;
        border-radius: 4px;
        padding: 10px 16px;
        position: relative;
        /* display: inline-block; */
        float: left;
        line-height: 23px;
        max-width: 70%;
        align-items: center;
    }
    .message-bubble.me .message-text {
        float: right;
    }
    .message-bubble .message-text p {
        font-size: 15px;
        padding: 0;
        margin: 0;
        line-height: 25px;
    }
    /* Message Bubble "me" */
    .message-bubble.me .message-avatar {
        left: auto;
        right: 0;
    }
    .message-bubble.me .message-text {
        margin-left: 0;
        margin-right: 62px;
        background-color: #1b74e8;
        color: #fff;
        position: relative;
    }

    /* Arrow */
    .message-bubble .message-text:before {
        content: "";
        width: 0;
        height: 0;
        border-top: 0px solid transparent;
        border-bottom: 11px solid transparent;
        border-right: 10px solid #f4f4f4;
        left: -8px;
        right: auto;
        top: 0px;
        position: absolute;
    }
    .message-bubble.me .message-text:before {
        border-top: 0px solid transparent;
        border-bottom: 11px solid transparent;
        border-left: 9px solid #1b74e8;
        border-right: none;
        right: -7px;
        left: auto;
    }
</style>
<div class="bg-white" >
    {if $user_name}
    <div class="mb-1 aw-overflow-auto bg-white" id="inbox-dialog-container" style="height: 300px"></div>
    {/if}

    <form method="post" action="{:url('inbox/send')}" class="pb-3 pt-2 px-3">
        {if $user_name}
        <input type="hidden" name="recipient_uid" value="{$user_name}">
        {else/}
        <div class="form-group position-relative">
            <input type="text" class="form-control" id="searchUser" name="recipient_uid" value="" placeholder="{:L('搜索您想要发送私信的用户')}...">
            <div class="aw-dropdown mt-2 border px-3" style="display: none">
                <div class="aw-dropdown-list aw-overflow-auto text-left"></div>
            </div>
            <script>
                AWS.Dropdown.bind_dropdown_list('#searchUser', 'inbox');
            </script>
        </div>

        {/if}
        <div class="form-group overflow-hidden">
            <textarea type="text" name="message" rows="4" class="form-control float-left border-0 bg-light" placeholder="{:L('私信内容')}"></textarea>
        </div>
        <div class="clearfix">
            <div class="float-left">
                <!--<div class="dropdown d-inline-block">
                    <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cogs d-none d-sm-inline-block"></i> {:L('私信设置')}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-0 font-size-sm">
                        <div class="text-center d-block py-2" style="min-width: 100px">
                            <a class="dropdown-item" href="javascript:;">
                                <span>{:L('拉黑')}</span>
                            </a>
                        </div>
                    </div>
                </div>-->
            </div>
            <button class="btn btn-primary px-3 btn-sm d-block aw-ajax-submit float-right" type="button">{:L('发送私信')}</button>
        </div>
    </form>
</div>
{/block}
