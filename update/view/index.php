<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{:L('本地升级')}</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/static/common/css/bootstrap.min.css">
    <style>
        .tag{
            background: rgb(92, 128, 209,0.1) !important;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
            color: #165dff !important;
            border: none;
            position: relative;
        }
        .tag:hover,.tag.active{
            background:#165dff !important;
            color:#fff !important;
            cursor:pointer;
            border:none
        }
        .jumpBox{
            width: 100%;
            border-radius: 8px
        }
    </style>
</head>
<body style="background: #0a0a0a">
<div style="width: 800px;margin: 100px auto;">
    <div class="bg-white text-center py-3 jumpBox">
        <h1 class="mb-3" style="font-size: 1.3rem">本地升级程序</h1>
        <div class="m-3" style="border: 2px dotted #eee;padding: 15px;border-radius: 8px">
            <dl>
                <dd>欢迎使用 WeCenter 升级程序, 本程序仅适用于 4.0.3 及以上版本升级<br />
                    <strong style="color:orange">升级过程可能比较缓慢, 在未显示升级成功之前不要关闭浏览器!</strong>
                </dd>
            </dl>
            <dl class="mb-1">
                <dt class="d-inline-block">当前数据版本:</dt>
                <dd class="d-inline-block">V{$db_version}</dd>
            </dl>
            <dl>
                <dt class="d-inline-block">升级数据版本:</dt>
                <dd class="text-danger d-inline-block">V{$versions}</dd>
            </dl>
            <b>程序版本:</b> V{:config('version.version')}<span> Build {:config('version.build')}</span>
        </div>
        <p class="clearfix mb-0">
            <!--<a href="javascript:;" onclick="history.back();" class="tag">返回</a>-->
            <a href="{:url('upgrade/run')}" class="tag active">下一步</a>
        </p>
    </div>
</div>
</body>