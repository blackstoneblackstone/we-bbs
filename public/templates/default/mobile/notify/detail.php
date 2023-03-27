{extend name="$theme_block" /}
{block name="meta_script"}
<link rel="stylesheet" type="text/css" href="{$static_url}mobile/lib/mui/mui.min.css"/>
<link rel="stylesheet" type="text/css" href="{$static_url}mobile/css/aui.chatbox.css"/>
<script type="text/javascript" src="{$static_url}mobile/lib/mui/mui.min.js"></script>
<script type="text/javascript" src="{$static_url}mobile/js/aui.chatbox.js"></script>
{/block}

{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('通知私信')}</div>
    <a class="aui-header-right" href="{:url('setting/index')}" data-pjax="pageMain"><i class="fa fa-ellipsis-h"></i></a>
</header>
{/block}

{block name="main"}
<div class="aui-content" style="padding: 15px 0 0 0;">

</div>

<script type="text/javascript">
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
            }
        });
        var btns = document.querySelectorAll(".aui-btn");
        for(var i = 0; i < btns.length; i++){
            aui.touchDom(btns[i], "#FFF", "var(--aui-blue)", "1px solid var(--aui-blue)");
        }
        aui.chatbox.init({
            warp: 'body',
            autoFocus: true,
            record: {
                use: true,
            },
            emotion: {
                use: true,
                path: '{$static_url}mobile/img/chat/emotion/',
                file: 'emotion.json'
            },
            extras: {
                use: true,
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
            events: ['recordStart', 'recordCancel', 'recordEnd', 'chooseEmotionItem', 'chooseExtrasItem', 'submit'],
        }, function(){

        })
        //开始录音
        aui.chatbox.addEventListener({name: 'recordStart'}, function(ret){
            console.log(ret);
            //aui.toast({msg: ret.msg})
        });
        //取消录音
        aui.chatbox.addEventListener({name: 'recordCancel'}, function(ret){
            console.log(ret);
            //aui.toast({msg: ret.msg})
        });
        //结束录音
        aui.chatbox.addEventListener({name: 'recordEnd'}, function(ret){
            console.log(ret);
            aui.toast({msg: ret.msg})
        });
        //选择表情
        aui.chatbox.addEventListener({name: 'chooseEmotionItem'}, function(ret){
            console.log(ret);
            //aui.toast({msg: ret.data.text});
        });
        //选择附加功能
        aui.chatbox.addEventListener({name: 'chooseExtrasItem'}, function(ret){
            console.log(ret);
            aui.toast({msg: ret.data.title});
        });
        //发送
        aui.chatbox.addEventListener({name: 'submit'}, function(ret){
            console.log(ret);
            aui.toast({msg: ret.data.value})
        });
    });
</script>
{/block}

{block name="sideMenu"}{/block}

{block name="footer"}{/block}