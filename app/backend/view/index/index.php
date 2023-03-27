{extend name="block" /}
{block name="main"}
{:hook('backendIndexPage')}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="layui-tab layui-tab-card">
                <ul class="layui-tab-title">
                    <li class="layui-this">数据总览</li>
                    <li>财务概况</li>
                    <li>用户数据</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="layui-inline echart-date">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input date-area" id="user-date-area" name="date_area" placeholder="日期区间" value="" readonly style="width: 260px">
                                    </div>
                                </div>
                                <div id="user-main" style="width: 100%;height:400px;"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="layui-inline echart-date">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input date-area" id="question-date-area" name="date_area" placeholder="日期区间" value="" readonly style="width: 260px">
                                    </div>
                                </div>
                                <div id="content-main" style="width: 100%;height:400px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item"><div class="text-warning pt-3 pb-3">各模块的收入({:get_setting("score_unit")}、金额)</div></div>
                    <div class="layui-tab-item">
                        <table class="col-md-6 table table-hover">
                            <tbody>
                            <tr>
                                <td>会员总数</td>
                                <td>{$usersInfo.users_count}</td>
                                <td>已激活会员</td>
                                <td>{$usersInfo.users_valid_email_count}</td>
                            </tr>
                            <tr>
                                <td>文章总数</td>
                                <td>{$usersInfo.article_count}</td>
                                <td >专栏总数</td>
                                <td>{$usersInfo.column_count}</td>
                            </tr>
                            <tr>
                                <td>问题总数</td>
                                <td>{$usersInfo.question_count}</td>
                                <td>待回答问题</td>
                                <td>{$usersInfo.no_answer_count}</td>
                            </tr>
                            <tr>
                                <td>回答总数</td>
                                <td>{$usersInfo.answer_count}</td>
                                <td>最佳答案数量</td>
                                <td>{$usersInfo.best_answer_count}</td>
                            </tr>
                            <tr>
                                <td>待审核问题</td>
                                <td>{$usersInfo.approval_question_count}</td>
                                <td>待审核回答</td>
                                <td>{$usersInfo.approval_answer_count}</td>
                            </tr>
                            <tr>
                                <td>话题总数</td>
                                <td>{$usersInfo.topic_count}</td>
                                <td>附件总数</td>
                                <td>{$usersInfo.attach_count}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="card-title">系统信息</div>
                    <div class="card-tools"><span>系统环境相关配置</span></div>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <td align="right">当前版本</td>
                            <td>WeCenter V{$sysInfo.version}</td>
                            <td align="right">服务器IP</td>
                            <td>{$sysInfo.ip}</td>
                        </tr>
                        <tr>
                            <td align="right">操作系统</td>
                            <td>{$sysInfo.os}</td>
                            <td align="right">服务器域名</td>
                            <td>{$sysInfo.domain}</td>
                        </tr>
                        <tr>
                            <td align="right">服务器环境</td>
                            <td>{$sysInfo.web_server}</td>
                            <td align="right">PHP 版本</td>
                            <td>{$sysInfo.php_version}</td>
                        </tr>
                        <tr>
                            <td align="right">服务器时间</td>
                            <td>{:date('Y-m-d H:i:s')}</td>
                            <td align="right">服务器时区</td>
                            <td>{$sysInfo.timezone}</td>
                        </tr>
                        <tr>
                            <td align="right">Mysql 版本</td>
                            <td>{$sysInfo.mysql_version}</td>
                            <td align="right">GD 版本</td>
                            <td>{$sysInfo.gd_info}</td>
                        </tr>
                        <tr>
                            <td align="right">上传限制</td>
                            <td>{$sysInfo.file_upload}</td>
                            <td align="right">最大内存:</td>
                            <td>{$sysInfo.memory_limit}</td>
                        </tr>
                        <tr>
                            <td align="right">执行时间</td>
                            <td>{$sysInfo.max_ex_time}</td>
                            <td align="right">安全模式</td>
                            <td>{$sysInfo.safe_mode}</td>
                        </tr>
                        <tr>
                            <td align="right">Zlib支持</td>
                            <td>{$sysInfo.zlib}</td>
                            <td align="right">Curl支持</td>
                            <td>{$sysInfo.curl}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/common/js/echarts.js"></script>
<script src="/static/admin/js/echat.js"></script>
<script>
    $(function () {
        var dateArr = getDate();
        //图表数据接入
        var echart = new Echarts('#user-main', 'line', G_BASE_URL+'/Index/statistic/?tag=new_user,user_valid&start_date=' + dateArr[1] + '&end_date=' + dateArr[0]);
        var echart2 = new Echarts('#content-main', 'line', G_BASE_URL+'/Index/statistic/?tag=new_question,new_answer,new_topic,new_article&start_date=' + dateArr[1] + '&end_date=' + dateArr[0]);

        layui.use(['element','laydate'],function(){
            var element = layui.element//元素操作
            var laydate = layui.laydate;
            //日期时间选择器
            laydate.render({
                elem: '#user-date-area'
                ,type: 'date',
                range: '~',
                format: 'yyyy-mm-dd',
                value: dateArr[1]+' ~ '+dateArr[0],
                done: function (value) {
                    let date_arr = value.split('~'),
                        start_date = $.trim(date_arr[0]),
                        end_date = $.trim(date_arr[1])
                    echart.initChart(echart.url.substring(0, echart.url.search(/&/)) + '&start_date=' + start_date + '&end_date=' + end_date)
                }
            });
            //日期时间选择器
            laydate.render({
                elem: '#question-date-area'
                ,type: 'date',
                range: '~',
                format: 'yyyy-mm-dd',
                value: dateArr[1]+' ~ '+dateArr[0],
                done: function (value) {
                    let date_arr = value.split('~'),
                        start_date = $.trim(date_arr[0]),
                        end_date = $.trim(date_arr[1])
                    echart2.initChart(echart2.url.substring(0, echart2.url.search(/&/)) + '&start_date=' + start_date + '&end_date=' + end_date)
                }
            });
        });
        window.addEventListener("orientationchange", function ()
        {
            echart.render();
            echart2.render();
        }, false);
    });
</script>
{/block}