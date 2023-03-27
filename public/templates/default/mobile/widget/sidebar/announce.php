{if !empty($announce_list)}
<div class="aui-noticebar aui-noticebar-col mb-1">
    <div class="aui-noticebar-main  bg-white">
        <div class="aui-noticebar-left"><i class="iconfont text-danger font-weight-bold iconsystemprompt"></i></div>
        <div class="aui-noticebar-center">
            <div class="aui-noticebar-texts">
                <div class="announce-swiper-container">
                    <div class="swiper-wrapper">
                        {volist name="announce_list" id="v"}
                        <div class="swiper-slide">
                            <p class="aui-noticebar-text"><a data-title="{:L('最新公告')}" data-url="{:url('announce/detail',['id'=>$v.id])}" href="javascript:;" class="text-danger aw-ajax-open">{$v.title}</a></p>
                        </div>
                        {/volist}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    /*aui.announce({
        title: '{$announce_list[0]['title']}',
        msg: '{$announce_list[0]['message']|raw}'
    });*/
    var swiper = new Swiper('.announce-swiper-container', {

        autoplay: true,
        direction:'vertical',
        grabCursor:true,
        autoplayDisableOnInteraction:true,
        mousewheelControl:true,
        autoHeight:true,
        speed: 1000,
        loop: true
    });
</script>
{/if}
