{extend name="$theme_block" /}
{block name="main"}
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 mb-2">
            <div class="bg-white p-3">
                <h2 class="my-3 text-center">{$info.title|raw}</h2>
                {if $info.description}
                <div class="bg-light my-2 p-3">
                    {$info.description|raw}
                </div>
                {/if}
                <div class="aw-content">
                    {$info.contents|raw}
                </div>
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}
            {:widget('sidebar/announce')}
            {:widget('sidebar/hotTopic',['uid'=>$user_id])}
            {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
            {:widget('sidebar/hotUsers',['uid'=>$user_id])}
            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>
{/block}