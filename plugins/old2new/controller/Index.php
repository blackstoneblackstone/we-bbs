<?php
namespace plugins\old2new\controller;
use app\common\controller\PFrontend;
use app\common\library\helper\ImageHelper;
use app\common\library\helper\LogHelper;
use app\common\library\helper\NotifyHelper;
use app\common\library\helper\StringHelper;
use Overtrue\Pinyin\Pinyin;
use think\App;
use think\facade\Config;
use think\facade\Db;

class Index extends PFrontend
{
    protected $dbObj;
    protected $uploadUrl;
    protected $plugin_config;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $plu_config = get_plugins_config('old2new');
        $config = [
            'default'     => 'mysql',
            'connections' => [
                'mysql'   => config('database.connections.mysql'),
                'convert'=>[
                    // 数据库类型
                    'type'              => 'mysql',
                    // 服务器地址
                    'hostname'          => $plu_config['db_host']??'',
                    // 数据库名
                    'database'          => $plu_config['db_name']??'',
                    // 用户名
                    'username'          => $plu_config['db_username']??'',
                    // 密码
                    'password'          => $plu_config['db_password']??'',
                    // 端口
                    'hostport'          => '3306',
                    // 数据库连接参数
                    'params'            => [],
                    // 数据库编码默认采用utf8
                    'charset'           =>  'utf8mb4',
                    'collation' => 'utf8mb4_general_ci',
                    // 数据库表前缀
                    'prefix'            => $plu_config['db_prefix'],
                    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
                    'deploy'            => 0,
                    // 数据库读写是否分离 主从式有效
                    'rw_separate'       => false,
                    // 读写分离后 主服务器数量
                    'master_num'        => 1,
                    // 指定从服务器序号
                    'slave_no'          => '',
                    // 是否严格检查字段是否存在
                    'fields_strict'     => true,
                    // 是否需要断线重连
                    'break_reconnect'   => false,
                    // 监听SQL
                    'trigger_sql'       => true,
                    // 开启字段缓存
                    'fields_cache'      => false,
                    // 字段缓存路径
                    'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
                ],
            ],
        ];
        $this->plugin_config = $plu_config;
        Config::set($config,'database');
        $this->dbObj = Db::connect('convert');
        $this->uploadUrl = $plu_config['web_site'] ?: '';
    }

    public function index()
    {
        return $this->fetch('/index');
    }

    public function init()
    {
        $type = $this->request->param('type', 'clean_up');
        $page = $this->request->param('page', 1);

        if(!$this->dbObj->query("select version()"))
        {
            $this->error('待转换数据库链接失败，请检查数据库配置!');
        }

        switch ($type) {
            case 'clean_up':
                $this->clean_up();
                $this->success('数据准备完成, 接下来转换用户',plugins_url('init?type=import_users&page=1'));
                break;

            case 'import_users':
                // 用户转换
                if (!$this->import_users($page))
                {
                    $this->success('用户导入完成, 开始导入分类',plugins_url('init?type=import_category&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入用户, 当前第 ' . $page . ' 批',plugins_url('init?type=import_users&page='.$page));
                }
                break;

            case 'import_category':
                if ($this->import_category())
                {
                    $this->success('分类导入完成, 开始导入话题',plugins_url('init?type=import_topic&page=1'));
                }
                break;

            case 'import_topic':
                if ($this->import_topic())
                {
                    $this->success('话题导入完成, 开始导入问题',plugins_url('init?type=import_question&page=1'));
                }
                break;

            case 'import_question':
                //导入问题
                if (!$this->import_question($page))
                {
                    $this->success('问题导入完成, 开始导入问题评论',plugins_url('init?type=import_question_comment&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入问题, 当前第 ' . $page . ' 批',plugins_url('init?type=import_question&page='.$page));
                }
                break;

            case 'import_question_comment':
                //导入问题
                if (!$this->import_question_comment($page))
                {
                    $this->success('问题评论导入完成, 开始导入回答',plugins_url('init?type=import_answer&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入问题评论, 当前第 ' . $page . ' 批',plugins_url('init?type=import_question_comment&page='.$page));
                }
                break;

            case 'import_answer':
                //导入问题
                if (!$this->import_answer($page))
                {
                    $this->success('回答导入完成, 开始导入回答评论',plugins_url('init?type=import_answer_comment&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入回答, 当前第 ' . $page . ' 批',plugins_url('init?type=import_answer&page='.$page));
                }
                break;

            case 'import_answer_comment':
                //导入问题
                if (!$this->import_answer_comment($page))
                {
                    $this->success('回答评论导入完成, 开始导入文章',plugins_url('init?type=import_article&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入回答评论, 当前第 ' . $page . ' 批',plugins_url('init?type=import_answer_comment&page='.$page));
                }
                break;

            case 'import_article':
                //导入问题
                if (!$this->import_article($page))
                {
                    $this->success('文章导入完成, 开始导入文章评论',plugins_url('init?type=import_article_comment&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入文章, 当前第 ' . $page . ' 批',plugins_url('init?type=import_article&page='.$page));
                }
                break;

            case 'import_article_comment':
                if (!$this->import_article_comment($page))
                {
                    $this->success('文章评论导入完成, 开始导入专栏',plugins_url('init?type=import_column&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入文章评论, 当前第 ' . $page . ' 批',plugins_url('init?type=import_article_comment&page='.$page));
                }
                break;

            case 'import_column':
                if (!$this->import_column($page))
                {
                    $this->success('专栏导入完成, 开始导入首页数据',plugins_url('init?type=import_post_index&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入专栏, 当前第 ' . $page . ' 批',plugins_url('init?type=import_column&page='.$page));
                }
                break;

            case 'import_post_index':
                if (!$this->import_post_index($page))
                {
                    $this->success('首页数据导入完成, 开始导入话题关联数据',plugins_url('init?type=import_topic_relation&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入首页数据, 当前第 ' . $page . ' 批',plugins_url('init?type=import_post_index&page='.$page));
                }
                break;

            case 'import_topic_relation':
                if (!$this->import_topic_relation($page))
                {
                    $this->success('话题关联数据导入完成, 开始导入话题关注数据',plugins_url('init?type=import_topic_focus&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入话题关联数据, 当前第 ' . $page . ' 批',plugins_url('init?type=import_topic_relation&page='.$page));
                }
                break;

            case 'import_topic_focus':
                if (!$this->import_topic_focus($page))
                {
                    $this->success('导入话题关注数据导入完成, 开始导入积分数据',plugins_url('init?type=import_integral_log&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入话题关注数据, 当前第 ' . $page . ' 批',plugins_url('init?type=import_topic_focus&page='.$page));
                }
                break;


            case 'import_integral_log':
                if (!$this->import_integral_log($page))
                {
                    $this->success('积分记录导入完成, 开始导入问题赞踩记录',plugins_url('init?type=import_question_vote&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入积分记录数据, 当前第 ' . $page . ' 批',plugins_url('init?type=import_integral_log&page='.$page));
                }

                break;

            case 'import_question_vote':
                if (!$this->import_question_vote($page))
                {
                    $this->success('问题赞踩导入完成, 开始导入文章赞踩',plugins_url('init?type=import_article_vote&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入问题赞踩记录数据, 当前第 ' . $page . ' 批',plugins_url('init?type=import_question_vote&page='.$page));
                }
                break;
            case 'import_article_vote':
                if (!$this->import_article_vote($page))
                {
                    $this->success('文章赞踩导入完成, 开始导入回答感谢',plugins_url('init?type=import_answer_thanks_vote&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入文章赞踩记录数据, 当前第 ' . $page . ' 批',plugins_url('init?type=import_article_vote&page='.$page));
                }

                break;

            case 'import_answer_thanks_vote':
                if (!$this->import_answer_thanks_vote($page))
                {
                    $this->success('回答感谢导入完成, 开始导入用户关注记录',plugins_url('init?type=import_users_follow&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入回答感谢记录数据, 当前第 ' . $page . ' 批',plugins_url('init?type=import_answer_thanks_vote&page='.$page));
                }
                break;

            case 'import_users_follow':

                if (!$this->import_users_follow($page))
                {
                    $this->success('用户关注记录导入完成, 数据已转换完成',plugins_url('index'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入用户关注记录, 当前第 ' . $page . ' 批',plugins_url('init?type=import_users_follow&page='.$page));
                }
                break;

            case 'import_topic_related':

                if (!$this->import_topic_related($page))
                {
                    $this->success('导入完成',plugins_url('init?type=import_topic_redirect&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入, 当前第 ' . $page . ' 批',plugins_url('init?type=import_topic_related&page='.$page));
                }
                break;

            case 'import_topic_redirect':

                if (!$this->import_topic_redirect($page))
                {
                    $this->success('导入完成',plugins_url('init?type=import_question_redirect&page=1'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入, 当前第 ' . $page . ' 批',plugins_url('init?type=import_topic_redirect&page='.$page));
                }
                break;

            case 'import_question_redirect':

                if (!$this->import_question_redirect($page))
                {
                    $this->success('导入完成, 数据已转换完成',plugins_url('index'));
                }
                else
                {
                    $page++;
                    $this->success('正在导入, 当前第 ' . $page . ' 批',plugins_url('init?type=import_question_redirect&page='.$page));
                }
                break;
        }
    }

    /**
     * 导入前准备
     */
    private function clean_up()
    {
        db()->query('TRUNCATE table '.get_table('users'));
        db()->query('TRUNCATE table '.get_table('integral_log'));
        db()->query('TRUNCATE table '.get_table('users_extends'));
        db()->query('TRUNCATE table '.get_table('question'));
        db()->query('TRUNCATE table '.get_table('question_comment'));
        db()->query('TRUNCATE table '.get_table('category'));
        db()->query('TRUNCATE table '.get_table('answer'));
        db()->query('TRUNCATE table '.get_table('answer_comment'));
        db()->query('TRUNCATE table '.get_table('action_log'));
        db()->query('TRUNCATE table '.get_table('article'));
        db()->query('TRUNCATE table '.get_table('article_comment'));
        db()->query('TRUNCATE table '.get_table('topic'));
        db()->query('TRUNCATE table '.get_table('topic_relation'));
        db()->query('TRUNCATE table '.get_table('users_follow'));
        db()->query('TRUNCATE table '.get_table('attach'));
        db()->query('TRUNCATE table '.get_table('post_relation'));
        db()->query('TRUNCATE table '.get_table('column'));
    }

    /*导入用户信息*/
    private function import_users($page): bool
    {
        $users = $this->dbObj->name('users')->page($page,300)->order('uid','ASC')->select()->toArray();
        if($users)
        {
            $permission = '{"visit_website":"Y","publish_question_enable":"Y","publish_question_approval":"Y","publish_article_enable":"N","publish_article_approval":"Y","publish_answer_enable":"Y","publish_answer_approval":"Y","modify_answer_approval":"N","modify_article_approval":"Y","modify_question_approval":"Y","available_invite_count":"5","create_topic_enable":"N","publish_approval_time_start":"23","publish_approval_time_end":"5","publish_url":"N","publish_question_num":"3","publish_article_num":"3","topic_manager":"N","edit_content_topic":"N"}';
            foreach ($users as $key => $val)
            {
                if(!db('users')->where(['user_name'=>$val['user_name']])->whereOr(['uid'=>$val['uid']])->value('uid'))
                {
                    $pinyin = new Pinyin();
                    $url_token = $pinyin->permalink($val['user_name'],'');
                    $token = db('users')->where('url_token',$url_token)->value('url_token');
                    $url_token = $token ? $url_token.'-'.$val['uid'] : $url_token;

                    if(!$url_token || $url_token=='null' || $url_token=='Null')
                    {
                        $url_token = StringHelper::uuid('uniqid');
                    }

                    $reputation_group_id = intval($val['reputation_group']);
                    if($reputation_group_id>4)
                    {
                        if(!db('admin_group')->where(['id'=>$reputation_group_id])->value('id'))
                        {
                            $group_info = $this->dbObj->name('users_group')->where(['group_id'=>$reputation_group_id])->find();
                            $reputation_group_id = db('admin_group')->insertGetId([
                                'permission'=>$permission,
                                'rules'=>0,
                                'title'=>$group_info['group_name'],
                                'status'=>1,
                                'system'=>0
                            ]);
                        }
                    }

                    $uid = db('users')->insertGetId(array(
                        'uid'=>$val['uid'],
                        'user_name' => $val['user_name'],
                        'nick_name' => $val['user_name'],
                        'email' => $val['email'],
                        'mobile'=>$val['mobile'],
                        'avatar'=>$val['avatar_file'] ? '/storage/avatar/'.str_replace('_min','_max',$val['avatar_file']) : '/static/common/image/default-avatar.svg',
                        'integral'=>intval($val['integral']),
                        'reputation'=>intval($val['reputation']),
                        'birthday'=>intval($val['birthday']),
                        'fans_count'=>intval($val['fans_count']),
                        'friend_count'=>intval($val['friend_count']),
                        'question_count'=>intval($val['question_count']),
                        'article_count'=>intval($val['article_count']),
                        'answer_count'=>$val['answer_count'],
                        'agree_count'=>$val['agree_count'],
                        'available_invite_count'=> max(intval($val['invitation_available']), 0),
                        'password' => $val['password'],
                        'salt' => $val['salt'],
                        'create_time' => $val['reg_time'],
                        'last_login_time' => $val['last_login'],
                        'reputation_group_id' => $reputation_group_id,
                        'integral_group_id' =>  1,
                        'is_valid_email' => 1,
                        'is_first_login' => intval($val['is_first_login']),
                        'last_login_ip'=>long2ip($val['last_ip']),
                        'reg_ip'=>ip2long($val['reg_ip']),
                        'group_id' => intval($val['group_id']),
                        'url_token'=> $url_token,
                        'verified'=>$val['verified'],
                        'status'=>$val['is_del'] ? ($val['forbidden'] ? 3 : 0) : 1
                    ));

                    db('users_extends')->insert( array(
                        'uid' => $uid,
                        'inbox_setting'=>'all',
                        'notify_setting'=>json_encode(NotifyHelper::getDefaultNotifyConfig(),JSON_UNESCAPED_UNICODE),
                    ));
                }
            }
            return true;
        }
        return false;
    }

    /*导入问答赞踩记录*/
    private function import_question_vote($page): bool
    {
        $topic = $this->dbObj->name('answer_vote')->page($page,300)->select()->toArray();
        if($topic)
        {
            foreach ($topic as $val)
            {
                if(!db('question_vote')->where(['id'=>$val['voter_id']])->value('id'))
                {
                    db('question_vote')->insert([
                        'id'=>$val['voter_id'],
                        'uid'=>$val['vote_uid'],
                        'item_type'=>'answer',
                        'item_id'=>$val['answer_id'],
                        'vote_value'=>$val['vote_value'],
                        'weigh_factor'=>$val['reputation_factor'],
                        'item_uid'=>$val['answer_uid'],
                        'create_time'=>$val['add_time'],
                    ]);
                }
            }
            return true;
        }
        return false;
    }

    /*导入文章赞踩记录*/
    private function import_article_vote($page): bool
    {
        $topic = $this->dbObj->name('article_vote')->page($page,300)->select()->toArray();
        if($topic)
        {
            foreach ($topic as $val)
            {
                if(!db('article_vote')->where(['id'=>$val['id']])->value('id'))
                {
                    db('article_vote')->insert([
                        'id'=>$val['id'],
                        'uid'=>$val['uid'],
                        'item_type'=>$val['type'],
                        'item_id'=>$val['item_id'],
                        'vote_value'=>$val['rating'],
                        'weigh_factor'=>$val['reputation_factor'],
                        'item_uid'=>$val['item_uid'],
                        'create_time'=>$val['time'],
                    ]);
                }

            }
            return true;
        }
        return false;
    }

    /*导入回答感谢记录*/
    private function import_answer_thanks_vote($page): bool
    {
        $topic = $this->dbObj->name('answer_thanks')->page($page,300)->select()->toArray();
        if($topic)
        {
            foreach ($topic as $val)
            {
                if(!db('answer_thanks')->where(['id'=>$val['id']])->value('id'))
                {
                    db('answer_thanks')->insert([
                        'id'=>$val['id'],
                        'uid'=>$val['uid'],
                        'answer_id'=>$val['answer_id'],
                        'create_time'=>$val['time'],
                    ]);
                }
            }
            return true;
        }
        return false;
    }

    //导入用户积分记录
    private function import_integral_log($page): bool
    {
        $integral_log = $this->dbObj->name('integral_log')->page($page,300)->select()->toArray();

        if($integral_log)
        {
            foreach ($integral_log as $val)
            {
                db('integral_log')->insert([
                    'uid'=>$val['uid'],
                    'record_id'=>intval($val['item_id']),
                    'action_type'=>$val['action'],
                    'integral'=>$val['integral'],
                    'remark'=>$val['note'],
                    'balance'=>$val['balance'],
                    'record_db'=>'',
                    'create_time'=>$val['time']
                ]);
            }
            return true;
        }
        return false;
    }

    //导入关注记录
    private function import_users_follow($page): bool
    {
        $users_follow = $this->dbObj->name('user_follow')->order('follow_id','ASC')->page($page,300)->select()->toArray();
        if($users_follow)
        {
            foreach ($users_follow as $key => $val)
            {
                $data=[
                    'fans_uid'=>$val['fans_uid'],
                    'friend_uid'=>$val['friend_uid'],
                    'status'=>$val['is_del']?0:1,
                    'create_time'=>$val['add_time']
                ];

                db('users_follow')->insert($data);
            }
            return true;
        }
        return false;
    }

    //导入分类
    private function import_category(): bool
    {
        $category = $this->dbObj->name('category')->select()->toArray();
        foreach ($category as $key=>$val)
        {
            db('category')->insert([
                'id'=>$val['id'],
                'title'=>$val['title'],
                'type'=>'common',
                'icon'=>isset($val['icon']) && $val['icon']!='' ? $this->replace_pic($val['icon']) : '',
                'pid'=>intval($val['parent_id']),
                'url_token'=>$val['url_token'],
                'status'=>1
            ]);
        }
        return true;
    }

    //导入话题
    private function import_topic(): bool
    {
        $topic = $this->dbObj->name('topic')->select()->toArray();
        foreach ($topic as $val)
        {
            if(!db('topic')->where(['id'=>$val['topic_id']])->value('id'))
            {
                db('topic')->insert([
                    'id'=>$val['topic_id'],
                    'title'=>$val['topic_title'],
                    'discuss'=>$val['discuss_count'],
                    'description'=>$this->replace_upload($val['topic_description']),
                    'pid'=>$val['parent_id'],
                    'url_token'=>$val['url_token'],
                    'top'=>$val['topic_top']??0,
                    'pic'=>$val['topic_pic'] ? '/storage/topic/'.str_replace('_32_32','',$this->replace_pic($val['topic_pic'])):'/static/common/image/topic.svg',
                    'lock'=>$val['topic_lock'],
                    'seo_title'=>$val['seo_title'],
                    'related'=>$val['user_related'],
                    'focus'=>$val['focus_count'],
                    'discuss_week'=>intval($val['discuss_count_last_week']),
                    'discuss_month'=>intval($val['discuss_count_last_month']),
                    'is_parent'=>intval($val['is_parent']),
                    'status'=>1
                ]);
            }
        }
        return true;
    }

    //导入问题
    private function import_question($page): bool
    {
        $questions = $this->dbObj->name('question')->page($page,200)->order('question_id','ASC')->select()->toArray();
        if($questions)
        {
            foreach ($questions as $key => $val)
            {
                if(!db('question')->where(['id'=>$val['question_id']])->value('id'))
                {
                    $question_detail = $this->replace_upload($val['question_detail']);
                    $question_id = db('question')->insertGetId(array(
                        'id' => $val['question_id'],
                        'title' => $val['question_content'],
                        'detail'=>$question_detail,
                        'search_text'=>str_cut(strip_tags(htmlspecialchars_decode($question_detail)),0,55535),
                        'uid'=>$val['published_uid'],
                        'focus_count'=>$val['focus_count'],
                        'view_count'=>$val['view_count'],
                        'answer_count'=>$val['answer_count'],
                        'comment_count' => $val['comment_count'],
                        'answer_users' => $val['answer_users'],
                        'category_id' => $val['category_id'],
                        'agree_count' => $val['agree_count'],
                        'against_count' =>  $val['against_count'],
                        'last_answer' =>  $val['last_answer'],
                        'popular_value' => $val['popular_value'],
                        'popular_value_update' => $val['popular_value_update'],
                        'is_lock' => $val['lock'],
                        'is_anonymous' => $val['anonymous'],
                        'is_recommend'=>$val['is_recommend'],
                        'thanks_count'=>$val['thanks_count'],
                        'set_top'=>$val['set_top'],
                        'set_top_time' =>$val['set_top_time'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'sort'=>$val['sort'],
                        'create_time'=>$val['add_time'],
                        'update_time'=>$val['update_time']
                    ));
                    //添加行为记录
                    LogHelper::addActionLog('publish_question','question',$question_id,$val['published_uid'],$val['anonymous'],$val['add_time']);
                }
            }
            return true;
        }
        return false;
    }

    //导入问题评论
    private function import_question_comment($page): bool
    {
        $questions = $this->dbObj->name('question_comments')->page($page,500)->order('id','ASC')->select()->toArray();
        if($questions)
        {
            foreach ($questions as $key => $val)
            {
                if(!db('question_comment')->where(['id'=>$val['id']])->value('id'))
                {
                    db('question_comment')->insert(array(
                        'question_id' => $val['question_id'],
                        'id' => $val['id'],
                        'message'=>$val['message'],
                        'uid'=>$val['uid'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'create_time'=>$val['time'],
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入回答
    private function import_answer($page): bool
    {
        $answers = $this->dbObj->name('answer')->page($page,500)->order('answer_id','ASC')->select()->toArray();
        if($answers)
        {
            foreach ($answers as $key => $val)
            {
                if(!db('answer')->where(['id'=>$val['answer_id']])->value('id'))
                {
                    $answer_content = $this->replace_upload($val['answer_content']);
                    db('answer')->insert(array(
                        'question_id' => $val['question_id'],
                        'id' => $val['answer_id'],
                        'content'=>$answer_content,
                        'uid'=>intval($val['uid']),
                        'agree_count' => $val['agree_count'],
                        'search_text'=>str_cut(strip_tags(htmlspecialchars_decode($answer_content)),0,55535),
                        'against_count' =>  $val['against_count'],
                        'comment_count' =>  $val['comment_count'],
                        'uninterested_count' =>  $val['uninterested_count'],
                        'thanks_count' =>  $val['thanks_count'],
                        'answer_user_ip' =>  long2ip($val['ip']),
                        'is_best' =>  $val['is_best'],
                        'best_uid' =>  $val['best_uid'],
                        'best_time' =>  $val['best_time'],
                        'is_anonymous' =>  $val['anonymous'],
                        'publish_source' =>  $val['publish_source'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'create_time'=>$val['add_time'],
                    ));
                    //添加行为日志
                    LogHelper::addActionLog('publish_answer','answer',$val['answer_id'],intval($val['uid']),$val['anonymous'],$val['add_time'],'question',$val['question_id']);
                }
            }
            return true;
        }
        return false;
    }

    //导入回答评论
    private function import_answer_comment($page): bool
    {
        $result = $this->dbObj->name('answer_comments')->page($page,500)->order('id','ASC')->select()->toArray();
        if($result)
        {
            foreach ($result as $key => $val)
            {
                if(!db('answer_comment')->where(['id'=>$val['id']])->value('id'))
                {
                    db('answer_comment')->insert(array(
                        'answer_id' => $val['answer_id'],
                        'id' => $val['id'],
                        'message'=>$val['message'],
                        'uid'=>$val['uid'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'create_time'=>$val['time'],
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入文章
    private function import_article($page): bool
    {
        $articles = $this->dbObj->name('article')->page($page,500)->order('id','ASC')->select()->toArray();
        if($articles)
        {
            foreach ($articles as $val)
            {
                if(!db('article')->where(['id'=>$val['id']])->value('id'))
                {
                    $message = $this->replace_upload($val['message']);
                    db('article')->insert(array(
                        'id' => $val['id'],
                        'title' => $val['title'],
                        'message'=>$message,
                        'search_text'=>str_cut(strip_tags(htmlspecialchars_decode($message)),0,55535),
                        'uid'=>$val['uid'],
                        'view_count'=>$val['views'],
                        'comment_count' => $val['comments'],
                        'category_id' => $val['category_id'],
                        'agree_count' => $val['votes'],
                        'column_id' =>  $val['column_id'],
                        'cover' => $val['article_img'] ? $this->replace_pic($val['article_img']) : '',
                        'sort' => $val['sort'],
                        'is_recommend'=>$val['is_recommend'],
                        'set_top'=>$val['set_top'],
                        'set_top_time' =>$val['set_top_time'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'create_time'=>$val['add_time'],
                        'update_time'=>$val['add_time']
                    ));
                    //添加行为记录
                    LogHelper::addActionLog('publish_article','article',$val['id'],$val['uid'],0,$val['add_time']);
                }
            }
            return true;
        }
        return false;
    }

    //导入文章评论
    private function import_article_comment($page): bool
    {
        $comments = $this->dbObj->name('article_comments')->page($page,500)->order('id','ASC')->select()->toArray();
        if($comments)
        {
            foreach ($comments as $key => $val)
            {
                if(!db('article_comment')->where(['id'=>$val['id']])->value('id'))
                {
                    db('article_comment')->insert(array(
                        'article_id' => $val['article_id'],
                        'id' => $val['id'],
                        'message'=>$val['message'],
                        'uid'=>$val['uid'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'at_uid'=>$val['at_uid'],
                        'agree_count' => $val['votes'],
                        'create_time'=>$val['add_time'],
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入专栏
    private function import_column($page): bool
    {
        $columns = $this->dbObj->name('column')->page($page,200)->order('column_id','ASC')->select()->toArray();
        if($columns)
        {
            foreach ($columns as $val)
            {
                if(!db('column')->where(['id'=>$val['column_id']])->value('id'))
                {
                    db('column')->insert(array(
                        'id' => $val['column_id'],
                        'name' => $val['column_name'],
                        'verify'=>$val['is_verify'],
                        'uid'=>$val['uid'],
                        'description'=>$val['column_description'],
                        'focus_count'=>$val['focus_count'],
                        'recommend'=>$val['recommend'],
                        'cover'=>$this->replace_pic($val['column_pic']),
                        'sort'=>$val['sort'],
                        'reason'=>$val['reson'],
                        'post_count'=>db('article')->where(['column_id'=>$val['column_id']])->count(),
                        'create_time'=>$val['add_time'],
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入首页数据
    private function import_post_index($page): bool
    {
        $posts_index = $this->dbObj->name('posts_index')->page($page,500)->order('id','ASC')->select()->toArray();
        if($posts_index)
        {
            foreach ($posts_index as $val)
            {
                if(!db('post_relation')->where(['id'=>$val['id']])->value('id'))
                {
                    db('post_relation')->insert(array(
                        'id' => $val['id'],
                        'item_id' => $val['post_id'],
                        'item_type'=>$val['post_type'],
                        'uid'=>$val['uid'],
                        'view_count'=>$val['view_count'],
                        'is_anonymous' => $val['anonymous'],
                        'category_id' => $val['category_id'],
                        'agree_count' => $val['agree_count'],
                        'answer_count' => $val['answer_count'],
                        'popular_value' => $val['popular_value'],
                        'is_recommend'=>$val['is_recommend'],
                        'set_top'=>$val['set_top'],
                        'set_top_time' =>$val['set_top_time'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'create_time'=>$val['add_time'],
                        'update_time'=>$val['update_time']
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入话题关联
    private function import_topic_relation($page): bool
    {
        $posts_index = $this->dbObj->name('topic_relation')->page($page,500)->order('id','ASC')->select()->toArray();
        if($posts_index)
        {
            foreach ($posts_index as $key => $val)
            {
                if(!db('topic_relation')->where(['id'=>$val['id']])->value('id'))
                {
                    db('topic_relation')->insert(array(
                        'id' => $val['id'],
                        'item_id' => $val['item_id'],
                        'item_type'=>$val['type'],
                        'uid'=>$val['uid'],
                        'topic_id'=>$val['topic_id'],
                        'status' =>$val['is_del'] ? 0 : 1,
                        'create_time'=>$val['add_time'],
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入话题关注记录
    private function import_topic_focus($page): bool
    {
        $topic_focus = $this->dbObj->name('topic_focus')->page($page,500)->order('focus_id','ASC')->select()->toArray();
        if($topic_focus)
        {
            foreach ($topic_focus as $key => $val)
            {
                if(!db('topic_focus')->where(['id'=>$val['focus_id']])->value('id'))
                {
                    db('topic_focus')->insert(array(
                        'id' => $val['focus_id'],
                        'topic_id' => $val['topic_id'],
                        'uid'=>$val['uid'],
                        'create_time'=>$val['add_time'],
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入相关话题
    private function import_topic_related($page): bool
    {
        $topic_focus = $this->dbObj->name('related_topic')->page($page,500)->order('id','ASC')->select()->toArray();
        if($topic_focus)
        {
            foreach ($topic_focus as $key => $val)
            {
                if(!db('topic_related')->where(['id'=>$val['id']])->value('id'))
                {
                    db('topic_related')->insert(array(
                        'id' => $val['id'],
                        'source_id' => $val['topic_id'],
                        'target_id'=>$val['related_id'],
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入话题合并
    private function import_topic_redirect($page): bool
    {
        $topic_focus = $this->dbObj->name('topic_merge')->page($page,500)->order('id','ASC')->select()->toArray();
        if($topic_focus)
        {
            foreach ($topic_focus as $key => $val)
            {
                if(!db('topic_merge')->where(['id'=>$val['id']])->value('id'))
                {
                    db('topic_merge')->insert(array(
                        'id' => $val['id'],
                        'source_id' => $val['source_id'],
                        'target_id'=>$val['target_id'],
                        'create_time'=>$val['time'],
                        'uid'=>$val['uid']
                    ));
                }
            }
            return true;
        }
        return false;
    }

    //导入问题重定向
    private function import_question_redirect($page): bool
    {
        $topic_focus = $this->dbObj->name('redirect')->page($page,500)->order('id','ASC')->select()->toArray();
        if($topic_focus)
        {
            foreach ($topic_focus as $key => $val)
            {
                if(!db('question_redirect')->where(['id'=>$val['id']])->value('id'))
                {
                    db('question_redirect')->insert(array(
                        'id' => $val['id'],
                        'item_id' => $val['item_id'],
                        'target_id'=>$val['target_id'],
                        'create_time'=>$val['time'],
                        'uid'=>$val['uid']
                    ));
                }
            }
            return true;
        }
        return false;
    }

    private function replace_upload($content): string
    {
        if(!$content) return $content;
        $content = htmlspecialchars_decode($content);
        $images = ImageHelper::srcList($content);
        if(!is_array($images) )return $content;
        foreach ($images as  $v) {
            if(!strstr( $v,'http') && !strstr( $v,'https') && $this->uploadUrl && !strstr($v,$this->uploadUrl))
            {
                $url = str_replace('/uploads', '/storage', $v);
            }else{
                $url = str_replace(['https://'.$this->uploadUrl.'/uploads','http://'.$this->uploadUrl.'/uploads'], '/storage', $v);
            }
            $content = str_replace($v, $url, $content);
        }
        return htmlspecialchars($content);
    }

    private function replace_pic($pic='')
    {
        if(!$pic) return $pic;

        if(!strstr($pic,'http') && !strstr($pic,'https') && $this->uploadUrl && !strstr($pic,$this->uploadUrl))
        {
            $url = str_replace('/uploads', '/storage', $pic);
        }else{
            $url = str_replace(['https://'.$this->uploadUrl.'/uploads','http://'.$this->uploadUrl.'/uploads'], '/storage', $pic);
        }
        return $url;
    }
}