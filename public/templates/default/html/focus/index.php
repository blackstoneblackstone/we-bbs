{extend name="$theme_block" /}
{block name="main"}
<style>
    .topic-item  dl{overflow: hidden;margin-bottom: 0;padding: 0.6rem 0;    border-bottom: 1px solid #EFEFEF;}
    .topic-item  dl:last-child{border:none;}
    .topic-item  dt{width: 64px;height: 64px;float: left}
    .topic-item  dt img{width: 64px !important;height: 64px !important;}
    .topic-item  dd{float: right;width: calc(100% - 74px);margin-bottom: 0;height: 64px}
    .topic-item  dd p{color: #999}
    .topic-item:hover{ background: #e6f1ff;cursor: pointer}
</style>
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>input('uid')])}
            <div class="col-md-10" id="wrapMain">
                <div class="block mb-1 new-block">
                    <div class="block-header">
                        <b>{:L('我的关注')}</b>
                        <div class="float-right">
                            <div class="mod-head-more dropdown show">
                                <a href="javascript:;" class="dropdown-toggle d-none-arrow aw-focus-show" data-toggle="dropdown">
                                    <span>{:L('关注的问题')}</span>
                                </a>
                                <div class="dropdown-menu text-center aw-focus-dropdown">
                                    <span class="arrow"></span>
                                    <ul>
                                        <li class="active dropdown-item" data-type="question"><a href="javascript:;">{:L('关注的问题')}</a></li>
                                        <li class="dropdown-item" data-type="friend"><a href="javascript:;">{:L('关注的人')}</a></li>
                                        <li class="dropdown-item" data-type="fans"><a href="javascript:;">{:L('关注我的')}</a></li>
                                        <li class="dropdown-item" data-type="column"><a href="javascript:;">{:L('关注的专栏')}</a></li>
                                        <li class="dropdown-item" data-type="topic"><a href="javascript:;">{:L('关注的话题')}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabMain" class="py-2 bg-white aw-common-list"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function (){
        var url = baseUrl + '/focus/focus_list/';
        var uid = parseInt("{$user.uid}");
        AWS.api.ajaxLoadMore('#tabMain',url,{type:'question',uid:uid},null,'json');
        $('.aw-focus-dropdown ul li').click(function ()
        {
            let type =  $(this).data('type');
            $('.aw-focus-dropdown ul li').removeClass('active');
            $(this).addClass('active');
            $('.aw-focus-show').find('span').text($(this).find('a').text());
            $('#tabMain').empty();
            AWS.api.ajaxLoadMore('#tabMain',url,{type:type,uid:uid},null,'json');
        });
    })
</script>
{/block}
