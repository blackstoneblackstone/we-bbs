{extend name="$theme_block" /}
{block name="meta_script"}
<link rel="stylesheet" type="text/css" href="{$static_url}mobile/lib/mui/mui.min.css"/>
<script type="text/javascript" src="{$static_url}mobile/lib/mui/mui.min.js"></script>
{/block}

{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('与')} {$user.nick_name} {:L('对话')}</div>
    <!--<a class="aui-header-right" href="{:url('setting/index')}" data-pjax="pageMain"><i class="fa fa-ellipsis-h"></i></a>-->
</header>
{/block}

{block name="main"}
<div id="chat-div">
<div class="aui-content">
    <div class="aui-chat" id="chatBox">
        {if !empty($list)}
        {foreach $list as $v}
        <div class="aui-chat-item {$v.uid == $user_id ? 'aui-chat-right' : 'aui-chat-left'}" data-total="{$total}">
            <div class="aui-chat-header">
                {:date('Y-m-d H:i:s',$v['send_time'])}
                {if $user_id==$v['uid']}
                {if $v.read_time}
                ({:L('对方于')} {:date('Y-m-d H:i:s',$v['read_time'])} {:L('已读')})
                {else/}
                <span class="text-danger">{:L('未读')}</span>
                {/if}
                {/if}
            </div>
            <div class="aui-chat-media">
                <a href="{$v.user.url}" class="d-block">
                    <img src="{$v['user']['avatar']}" alt="{$v['user']['name']}" style="width: 2rem;height: 2rem"/>
                </a>
            </div>
            <div class="aui-chat-inner">
                <div class="aui-chat-name">{$v.user.name}</div>
                <div class="aui-chat-content">
                    <div class="aui-chat-arrow"></div>
                    {$v.message}
                </div>
            </div>
        </div>
        {/foreach}
        {/if}
    </div>
</div>
</div>

<script type="text/javascript">
    let page = 1,
        receiver = '{$user.nick_name}';

    window.addEventListener("load", function(){
        mui.init({
            gestureConfig: {
                tap: true, //默认为true
                doubletap: true, //默认为false
                longtap: true, //默认为false
                swipe: true, //默认为true
                drag: true, //默认为true
                hold: true, //默认为false，不监听
                release: true //默认为false，不监听
            },
            pullRefresh : {
                container: '#chat-div',//待刷新区域标识，querySelector能定位的css选择器均可，比如：id、.class等
                up : {
                    height:50,//可选.默认50.触发上拉加载拖动距离
                    auto: false,//可选,默认false.自动上拉加载一次
                    contentrefresh : "正在加载...",//可选，正在加载状态时，上拉加载控件上显示的标题内容
                    contentnomore:'没有更多数据了',//可选，请求完毕若没有更多数据时显示的提醒内容；
                    callback: function () {
                        page++
                        let _this = this
                        $.get(baseUrl+'/inbox/page?receiver='+receiver, {page: page}, function (res) {
                            if (res.data.html) {
                                $('#chatBox').append(res.data.html)
                                if (page == res.data.total) {
                                    _this.endPullupToRefresh(true)
                                    mui('#chat-div').pullRefresh().disablePullupToRefresh();
                                } else {
                                    _this.endPullupToRefresh(false)
                                }
                            } else {
                                _this.endPullupToRefresh(true)
                                mui('#chat-div').pullRefresh().disablePullupToRefresh();
                            }
                        }, 'json')
                    }
                }
            }
        });
        /*var btns = document.querySelectorAll(".aui-btn");
        for(var i = 0; i < btns.length; i++){
            aui.touchDom(btns[i], "#FFF", "var(--aui-blue)", "1px solid var(--aui-blue)");
        }*/
        aui.chatbox.init({
            warp: 'body',
            autoFocus: true,
            record: {
                use: false,
            },
            emotion: {
                use: false,
                path: '{$static_url}mobile/img/chat/emotion/',
                file: 'emotion.json'
            },
            extras: {
                use: false,
                btns: [
                    {title: '相册', icon: 'iconimage'},
                    {title: '拍摄', icon: 'iconcamera_fill'},
                    {title: '语音通话', icon: 'iconvideo_fill'},
                    {title: '位置', icon: 'iconaddress1'},
                    {title: '红包', icon: 'iconredpacket_fill'},
                    {title: '语音输入', icon: 'icontranslation_fill'},
                    {title: '我的收藏', icon: 'iconcollection_fill'},
                    {title: '名片', icon: 'iconcreatetask_fill'},
                    {title: '文件', icon: 'iconmail_fill'}
                ],
            },
            events: ['submit'],
        }, function(){

        })

        //发送
        aui.chatbox.addEventListener({name: 'submit'}, function(ret){
            $.post(baseUrl+'/inbox/send', {recipient_uid: receiver, message: ret.data.value}, function (res) {
                if (res.code == 1) {
                    aui.toast({msg: res.msg}, function () {
                        window.location.reload();
                    })
                } else {
                    aui.toast({msg: res.msg})
                }
            }, 'json')

        });
    });
</script>
{/block}

{block name="sideMenu"}{/block}

{block name="footer"}{/block}