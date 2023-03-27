{if in_array('qq',$config['base']['enable'])}
<a href="{:url('ThirdAuth/connect',['platform'=>'qq'])}" class="mr-2" target="_blank"><i class="fab fa-qq text-primary" style="font-size: 2rem"></i></a>
{/if}

{if in_array('wechat',$config['base']['enable'])}
{if $isMobile}
<a href="{:url('ThirdAuth/connect',['platform'=>'wechat'])}" class="mr-2"  target="_blank"><i class="fab fa-wechat text-success" style="font-size: 2rem"></i></a>

{else/}
<a data-url="{:url('ThirdAuth/qrcode')}" href="javascript:;" data-width="100"  target="_blank" class="mr-2 aw-ajax-open"><i class="fab fa-wechat text-success" style="font-size: 2rem"></i></a>
{/if}
{/if}

{if in_array('weibo',$config['base']['enable'])}
<a href="{:url('ThirdAuth/connect',['platform'=>'weibo'])}"  target="_blank"><i class="fab fa-weibo text-warning" style="font-size: 2rem"></i></a>
{/if}
