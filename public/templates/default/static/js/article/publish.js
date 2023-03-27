function publishArticle(obj)
{
    let that = obj;
    let form = $($(that).parents('form')[0]);
    $('.aw-article-publish').attr('disabled',true);
    $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            if(result.code)
            {
                AWS.api.success(result.msg, result.url);
            }else{
                $('.aw-article-publish').attr('disabled',false);
                AWS.api.error(result.msg, result.url);
            }
        },
        error: function (error) {
            if ($.trim(error.responseText) !== '') {
                AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
            }
        }
    })
}

$(document).on('click', '.aw-timing-publish', function () {
    layer.open({
        type: 1,
        closeBtn: 1,
        anim: 2,
        content: $('#timing-publish-modal').html()
    });
    layui.laydate.render({
        elem: '#timing' //指定元素
    });
    $('.select-choose').click(function () {
        var timing = $('#timing').val();
        if(timing)
        {
            $('input[name=wait_time]').val(timing);
        }else{
            $('input[name=wait_time]').val(0);
        }
        layer.closeAll();
    });
});

$(document).on('click', '.aw-preview', function () {
    const that = this;
    const form = $($(that).parents('form')[0]);
    const formData = {};
    const t = form.serializeArray();
    let url = $(that).data('url');
    $.each(t, function () {
        formData[this.name] = this.value;
    });
    $.ajax({
        url: url,
        dataType: 'json',
        type: 'post',
        data: {
            data: formData,
        },
        success: function () {
            AWS.common.openNewWindow(url);
        },
    })
});

function verification(data){
    var arr = {};
    $.each(data, function() {
        arr[this.name] = this.value;
    });
    return true;
}

if(SYS_ATTACH==0){
    if(!ITEM_ID || !ATTACH_LEN)
    {
        $('.attach-upload-list').hide();
        $('#uploadListAction').hide();
    }
    var attachUpload = $('.aw-attach-upload');
    var path = attachUpload.data('path');
    var uploadListIns = layui.upload.render({
        elem: '#testList'
        ,elemList: $('#attachList')
        ,url: baseUrl + "/upload/index?path=" + path +'&access_key='+ACCESS_KEY+'&upload_type=file'
        ,accept: 'file'
        ,multiple: true
        ,number: 3
        ,auto: false
        ,bindAction: '#uploadListAction'
        ,choose: function(obj){
            var that = this;
            var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
            //读取本地文件
            obj.preview(function(index, file, result){
                $('.attach-upload-list').show();
                $('#uploadListAction').show();
                var tr = $(['<tr id="upload-'+ index +'">'
                    ,'<td>'+ file.name +'</td>'
                    ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                    ,'<td><div class="layui-progress" lay-filter="progress-demo-'+ index +'"><div class="layui-progress-bar" lay-percent=""></div></div></td>'
                    ,'<td>'
                    ,'<button type="button" class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                    ,'<button type="button" class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                    ,'</td>'
                    ,'</tr>'].join(''));

                //单个重传
                tr.find('.demo-reload').on('click', function(){
                    obj.upload(index, file);
                });

                //删除
                tr.find('.demo-delete').on('click', function(){
                    delete files[index]; //删除对应的文件
                    tr.remove();
                    uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                });

                that.elemList.append(tr);
                layui.element.render('progress'); //渲染新加的进度条组件
            });
        }
        ,done: function(res, index, upload){ //成功的回调
            var that = this;
            if(res.code == 1){ //上传成功
                var tr = that.elemList.find('tr#upload-'+ index),tds = tr.children();
                tds.eq(3).html('上传成功'); //清空操作
                $('.layui-progress-bar').css('width','100%');
                delete this.files[index]; //删除文件队列已经上传成功的文件
                layer.msg('上传成功');
                return;
            }else{
                layer.msg(res.msg);
            }
            this.error(index, upload);
        }
        ,allDone: function(obj){ //多文件上传完毕后的状态回调
            console.log(obj)
        }
        ,error: function(index, upload){ //错误回调
            var that = this;
            var tr = that.elemList.find('tr#upload-'+ index)
                ,tds = tr.children();
            tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
        }
        ,progress: function(n, elem, e, index){
            layui.element.progress('progress-demo-'+ index, n + '%');
        }
    });
}

//上传文章封面
AWS.upload.webUpload('filePicker_cover','cover_preview','cover','article')
