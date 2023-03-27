{if !empty($category_list) && get_setting('enable_category')=='Y'}
<style>
    .swiper-slide {width: auto!important}
</style>
<div class="container my-2 " style=" padding:0">
    <div class="row n-tab align-content-center">
        <div class="swiper-container" style="margin: 0">
            <ul class="nav nav-pills n-nav swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide">
                    <a class="nav-link mb-0 {if !$category}active c-active{/if}" data-k="0" data-pjax="wrapMain" href="{:url($thisRequest,['sort'=>$sort,'category_id'=>0])}">{:L('全部分类')}</a>
                </li>
                {foreach $category_list as $k => $v}
                <li class="nav-item swiper-slide">
                    <a class="nav-link {if $category==$v.id} active c-active{/if}" data-k="{$k}" data-pjax="wrapMain" href="{:url($thisRequest,['sort'=>$sort,'category_id'=>$v['id']])}">{$v.title}</a>
                    {if !empty($v.childs)}
                    <div class="card" style="display: none">
                        <div class="card-body">
                            {foreach $v.childs as $child}
                            <a class="nav-link mb-0 d-inline-block {if $category==$child.id} active c-active{/if}" data-k="{$k}"
                               data-pjax="wrapMain" href="{:url($thisRequest,['sort'=>$sort,'category_id'=>$child.id])}">{$child.title}</a>
                            {/foreach}
                        </div>
                    </div>
                    {/if}
                </li>
                {/foreach}
            </ul>
        </div>
    </div>

    <div class="n-tab align-content-center pl-0">
        <div class="card nav-pills border-0" id="category-children-box">
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        let c_a = $('.c-active'),
            k = parseInt(c_a.data('k'))
        let navSwiper = new Swiper('.swiper-container', {
            speed: 600,
            grabCursor: true,
            slidesPerView: "auto",
            initialSlide: k,
            slidesPerGroup: 3
        })

        // 二级分类
        let c_box = $('#category-children-box')
        $('.swiper-slide').hover(function () {
            if ($(this).find('div.card').length) {
                c_box.html($(this).find('div.card').html())
            } else {
                c_box.empty()
            }
        })

        if (c_a.parent().parent().hasClass('card')) {
            c_box.html(c_a.parent().parent().html())
        }
    })
</script>
{/if}