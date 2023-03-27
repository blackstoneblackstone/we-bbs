$(window).scroll(function ()
{
    if(!AWS.common.isMobile())
    {
        if ($(window).scrollTop() >= ($(window).height() / 3))
        {
            $('#aw-question-fixed').fadeIn().show();
        }
        else
        {
            $('#aw-question-fixed').fadeOut().hide();
        }
    }
    let questionHeight = $('.aw-question-container').height();
    if(questionHeight>$(window).height() && $(window).scrollTop()+($(window).height() / 2) < questionHeight)
    {
        $('.aw-question-container .actions').addClass('fixed-bottom').addClass('bg-white').addClass('container').addClass('p-3');
    }else{
        $('.aw-question-container .actions').removeClass('fixed-bottom').removeClass('bg-white').removeClass('container').removeClass('p-3');
    }
});

/**
 * 回答编辑器
 */
$(document).on('click', '.awsAnswerEditor', function (e) {
    if(!userId)
    {
        return AWS.User.login();
    }

    if($(this).data('enable')==1)
    {
        return layer.msg('您所在用户组没有回答问题的权限');
    }
    var questionId = $(this).data('question-id');
    var answerId = $(this).data('answer-id');
    var answerEditorContainer = $('#answerEditor');
    answerEditorContainer.toggle();
    $.ajax({
        url: baseUrl+'/ajax/editor?_ajax=1',
        dataType: '',
        type: 'post',
        data: {
            question_id: questionId,
            answer_id: answerId,
        },
        success: function (result) {
            answerEditorContainer.html(result);
            if(!answerEditorContainer.is(':hidden'))
            {
                let offset= answerEditorContainer.offset();
                $('body,html').animate({
                    scrollTop:offset.top
                })
            }
        },
    });
});

/*问题评论*/
$(document).on('click', '.questionCommentBtn', function (e) {
    var answerId = $(this).data('id');
    var boxElement = $('#questionCommentBox-'+answerId);
    var content = boxElement.find('div.questionCommentList');
    var pageElement = boxElement.find('div.pageElement');
    var ajaxUrl = baseUrl+'/comment/question?id='+answerId;
    var formBox = boxElement.find('div.commentForm');
    boxElement.toggle();
    if(boxElement.is(':hidden'))
    {
        return true;
    }

    if(userId)
    {
        var formString = '' +
            '<form class="p-3" method="post" id="questionForm-'+answerId+'" style="background-color: #fafafa;">' +
            '   <input type="hidden" name="question_id" value="'+answerId+'">' +
            '   <div class="clearfix">' +
            '     <input type="text" name="message" class="form-control comment-input float-left" placeholder="写下您的评论..." style="width: calc(100% - 85px)">' +
            '     <button type="button" class="btn btn-primary px-3 saveQuestionComment float-right" >发布</button>' +
            '   </div>' +
            '</form>';

        $(document).on('click', '.saveQuestionComment', function (e) {
            $.ajax({
                url: baseUrl+'/comment/save_question_comment',
                dataType: 'json',
                type: 'post',
                data: $('#questionForm-'+answerId).serialize(),
                success: function (result)
                {
                    let msg = result.msg ? result.msg : '评论成功';
                    if (result.code > 0) {
                        layer.msg(result.msg);
                        let c = $('.question-comment-count')
                        c.text(1 + parseInt(c.eq(0).text()))
                        content.prepend(result.data.html);
                        $('#questionForm-'+answerId).find('input.comment-input').val('');
                    } else {
                        layer.msg(result.msg);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        });
    }else{
        formString = '<p class="text-center text-muted pb-3">' +
            '    <span class="d-block">您还未登录,点击 <a href="javascript:;" onclick="AWS.User.login()">登录</a> 发表你的评论吧!</span>' +
            '</p>';
    }
    formBox.html(formString);
    ajaxPage(content,pageElement,ajaxUrl);
});

/*回答评论*/
$(document).on('click', '.answerCommentBtn', function (e) {
    var answerId = $(this).data('id');
    var boxElement = $('#answerCommentBox-'+answerId);
    var content = boxElement.find('div.answerCommentList');
    var pageElement = boxElement.find('div.pageElement');
    var ajaxUrl = $(this).data('url');
    var formBox = boxElement.find('div.commentForm');
    boxElement.toggle();

    if(boxElement.is(':hidden'))
    {
        return true;
    }
    if(userId)
    {
        var formString = '' +
            '<form class="p-3"  method="post" id="commentForm-'+answerId+'" style="background-color: #fafafa;">' +
            '   <input type="hidden" name="answer_id" value="'+answerId+'">' +
            '   <div class="clearfix">' +
            '     <input type="text" name="message" class="form-control comment-input float-left comment-form-input" placeholder="写下您的评论..." style="width: calc(100% - 85px)">' +
            '     <button type="button" class="btn btn-primary px-3 saveAnswerComment float-right" >发布</button>' +
            '   </div>' +
            '</form>';
    }else{
        formString = '<p class="text-center text-muted pb-3">' +
            '    <span class="d-block">您还未登录,点击 <a href="javascript:;" onclick="AWS.User.login()">登录</a> 发表你的评论吧!</span>' +
            '</p>';
    }
    formBox.html(formString);
    $(document).on('click', '.saveAnswerComment', function (e) {
        $.ajax({
            url: baseUrl+'/comment/save_answer_comment',
            dataType: 'json',
            type: 'post',
            data: $('#commentForm-'+answerId).serialize(),
            success: function (result)
            {
                let msg = result.msg ? result.msg : '评论成功';
                if (result.code > 0) {
                    layer.msg(result.msg);
                    let c = $('.answer-comment-count'+answerId)
                    c.text(1 + parseInt(c.eq(0).text()))
                    content.prepend(result.data.html);
                    $('#commentForm-'+answerId).find('input.comment-input').val('');
                } else {
                    layer.msg(result.msg);
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    layer.closeAll();
                    AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });
    //编辑器@人
    AWS.User.at_user_lists('#commentForm-'+answerId+' .comment-form-input', 5);
    ajaxPage(content,pageElement,ajaxUrl);
});

/*ajax分页*/
function ajaxPage(content,pageElement,ajaxUrl)
{
    var curPage = 1; //当前页码
    var total,pageSize,totalPage,string; //总记录数，每页显示数，总页数,内容
    getData(1);
    $(pageElement).on('click','span a',function(){
        var rel = $(this).attr("rel");
        if(rel){
            getData(rel);
        }
    });

    function getData(page)
    {
        $.ajax({
            url:ajaxUrl,
            type:'POST',
            data: {
                'page':page
            },
            /*beforeSend:function(){
                layer.load();
            },*/
            success:function(json){
                total = json.total; //总记录数
                pageSize = json.per_page; //每页显示条数
                curPage = page; //当前页
                totalPage = json.last_page; //总页数
                string = json.html;
                $(content).find('*').remove();
                if(totalPage>1 && string)
                {
                    getPageBar();
                }

                if(!userId)
                {
                    string = string ? string : '<p class="text-center text-muted">' +
                        '    <span class="d-block">暂无评论,点击 <a href="javascript:;" onclick="AWS.User.login()">登录</a> 发表你的评论吧!</span>' +
                        '</p>';
                }else{
                    string = string ? string : '<p class="text-center text-muted">' +
                        '    <span class="d-block">暂无评论,快来发表你的评论吧!</span>' +
                        '</p>';
                }
                $(content).append(string);
            },
            /*complete:function(){ //生成分页条
                getPageBar();
            },*/
            error:function(){
                layer.msg("数据加载失败");
            }
        });
    }

    //获取分页条
    function getPageBar(){
        $(pageElement).find('*').remove();
        //页码大于最大页数
        if(curPage>totalPage) curPage=totalPage;
        //页码小于1
        if(curPage<1) curPage=1;
        pageStr = "<span>共"+total+"条</span><span>"+curPage+"/"+totalPage+"</span>";
        //如果是第一页
        if(curPage==1){
            pageStr += "<span>首页</span><span>上一页</span>";
        }else{
            pageStr += "<span><a href='javascript:void(0)' rel='1'>首页</a></span><span><a href='javascript:void(0)' rel='"+(curPage-1)+"'>上一页</a></span>";
        }

        //如果是最后页
        if(curPage>=totalPage){
            pageStr += "<span>下一页</span><span>尾页</span>";
        }else{
            pageStr += "<span><a href='javascript:void(0)' rel='"+(parseInt(curPage)+1)+"'>下一页</a></span><span><a href='javascript:void(0)' rel='"+totalPage+"'>尾页</a></span>";
        }

        $(pageElement).addClass('px-3 pb-3').append(pageStr);
    }
}