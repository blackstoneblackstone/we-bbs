<div class="bg-white">
    {if $error==''}
    {if $user_id && ($user_info['permission']['publish_answer_enable']=='Y' || isSuperAdmin() || isNormalAdmin()) }
    <form method="post" id="ajaxForm" onsubmit="return false">
        {:token_field()}
        <input type="hidden" name="question_id" value="{$question_id}">
        <input type="hidden" name="id" value="{$answer_id}">
        <input type="hidden" name="access_key" value="{$access_key}">
        <input type="hidden" id="captcha">
        <div class="form-group">
            {if isset($answer_info['id'])}
            {:hook('editor',['name'=>'content','cat'=>'answer','value'=>$answer_info['content'],'access_key'=>$access_key])}
            {else/}
            {:hook('editor',['name'=>'content','cat'=>'answer','value'=>isset($answer_info['content']) ? $answer_info['content'] : '','access_key'=>$access_key])}
            {/if}
        </div>

        <!--发布附加钩子-->
        {:hook('publish_extend',['info'=>$answer_info,'page'=>'answer'])}
        <!--发布附加钩子-->

        <div class="form-group clearfix" style="margin-bottom: 60px">
            {if !$answer_id}
            {if !$is_focus}
            <label class="font-9 mr-3" style="color: #76839b">
                <input class="aw-checkbox" type="checkbox" value="1" name="focus_question"> {:L('关注问题')}
            </label>
            {/if}

            {if $setting.enable_anonymous=='Y'}
            <label class="font-9 mr-3" style="color: #76839b">
                <input class="aw-checkbox" type="checkbox" value="1" name="is_anonymous"> {:L('匿名')}
            </label>
            {/if}
            {/if}
            <a href="{:url('page/score')}" class="font-9" style="color: #76839b" target="_blank"><i class="fa fa-database"></i> {:get_setting("score_unit")}{:L('规则')}</a>
            {if get_setting('auto_save_draft')=='Y'}
            <script>
                //自动保存时间间隔
                var AutoSaveTime = parseInt("{:get_setting('auto_save_draft_time')}")*360;
                //设置自动保存
                setInterval(function (item_type,itemId){
                    var formData = AWS_MOBILE.common.formToJSON('ajaxForm');
                    $.ajax({
                        url:baseUrl + '/ajax/save_draft',
                        dataType: 'json',
                        type:'post',
                        data:{
                            data:formData,
                            item_id:'{$answer_info.id|default=0}',
                            item_type:'answer'
                        },
                        success: function (result)
                        {
                        },
                        error:  function (error) {
                            if ($.trim(error.responseText) !== '') {
                                layer.closeAll();
                                AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                            }
                        }
                    });
                }, AutoSaveTime);
            </script>
            {/if}
            <button type="button" class="btn btn-primary btn-sm float-right aw-answer-submit px-3">{:L('提交回答')}</button>
        </div>
    </form>
    <script>
        zxEditor.addFooterButton({
            name: 'save-draft',
            // 按钮外容器样式名称
            class: '',
            // 按钮内i元素样式名
            icon: 'iconfont icon-caogaoxiang',
            // 需要注册的监听事件名
            on: 'save-draft'
        })

        zxEditor.on('save-draft', function () {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }

            var formData = AWS_MOBILE.common.formToJSON('ajaxForm');

            $.ajax({
                url:baseUrl + '/ajax/save_draft',
                dataType: 'json',
                type:'post',
                data:{
                    data:formData,
                    item_id:'{$answer_info.id|default=0}',
                    item_type:'answer'
                },
                success: function (result)
                {
                    let msg = result.msg ? result.msg : '保存成功';
                    if(result.code> 0)
                    {
                        AWS_MOBILE.api.success(msg)
                    }else{
                        AWS_MOBILE.api.error(msg)
                    }
                },
                error:  function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        });

        $(document).on('click', '.aw-answer-submit', function (e) {
            var that = this;
            $(that).prop('disabled', true);
            {if $answer_captcha_enable}
            $('#captcha').captcha({
                callback: function () {
                    publishAnswer(that)
                }
            });
            {else/}
                publishAnswer(that)
                {/if}
                });

         function publishAnswer(obj) {
            var form = $('#ajaxForm');
            $.ajax({
                url: "{:url('ajax.question/save_answer')}",
                dataType: 'json',
                type: 'post',
                data: form.serialize(),
                success: function (result) {
                    if(result.code)
                    {
                        mescroll.resetUpScroll();
                        AWS_MOBILE.api.closeOpen();
                    }else{
                        $(that).prop('disabled', false);
                    }
                    AWS_MOBILE.api.success(result.msg);
                }
            });
        };
    </script>
    {else/}
    <p class="text-center">{:L('抱歉')}，{:L('您所在用户组没有回答问题的权限')}！</p>
    {/if}
    {else/}
    <p class="text-center text-danger">{$error}</p>
    {/if}
</div>