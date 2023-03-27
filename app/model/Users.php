<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------

namespace app\model;
use app\common\library\helper\ImageHelper;
use app\common\library\helper\IpHelper;
use app\common\library\helper\LogHelper;
use app\common\library\helper\MailHelper;
use app\common\library\helper\NotifyHelper;
use app\common\library\helper\RandomHelper;
use app\common\library\helper\StringHelper;
use app\common\library\helper\TokenHelper;
use app\logic\search\libs\ElasticSearch;
use Overtrue\Pinyin\Pinyin;
use Pay\Exceptions\Exception;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;

/**
 * 公用用户模型
 * Class Users
 * @package app\model
 */
class Users extends BaseModel
{
    protected $pk = 'uid';
    protected $name = 'users';
    public static $_token;
    public static $_user;
    public static $_is_login;

    /**
     * 删除用户
     * @param $uid
     * @param int $realMove 1真实删除
     * @return bool
     */
    public static function removeUser($uid, int $realMove=0): bool
    {
        $uid = is_array($uid) ? $uid : explode(',',$uid);
        db()->startTrans();
        try {
            if($realMove){
                //真实删除用户
                db('users')->whereIn('uid',$uid)->delete();
                //问题
                $question_ids = db('question')->whereIn('uid',$uid)->column('id');
                //删除问题
                db('question')->whereIn('id',$question_ids)->delete();
                //删除问题下的回答
                db('answer')->whereIn('question_id',$question_ids)->delete();
                //删除该用户的回答
                db('answer')->whereIn('uid',$uid)->delete();
                //删除操作记录
                db('action_log')->whereIn('uid',$uid)->delete();
                db('action_log_all')->whereIn('uid',$uid)->delete();
                //删除关注好友记录
                db('users_follow')->whereRaw('fans_uid IN('.implode(',',$uid).') OR friend_uid IN('.implode(',',$uid).')')->delete();
                //删除用户的文章
                $article_ids = db('article')->whereIn('uid',$uid)->column('id');
                db('article')->whereIn('id',$article_ids)->delete();
                db('article_comment')->whereIn('article_id',$article_ids)->delete();
                db('article_comment')->whereIn('uid',$uid)->delete();
                //删除通知和私信数据
                db('users_inbox')->whereIn('uid',$uid)->delete();
                db('users_inbox_dialog')->whereRaw('`sender_uid` IN('.implode(',',$uid).') OR `recipient_uid` IN('.implode(',',$uid).')')->delete();

                db('users_notify')->whereRaw('`sender_uid` IN('.implode(',',$uid).') OR `recipient_uid` IN('.implode(',',$uid).')')->delete();

                //删除首页数据
                db('post_relation')->whereIn('uid',$uid)->delete();
            }else{
                db('users')->whereIn('uid',$uid)->update(['status'=>0]);
                //问题
                $question_ids = db('question')->whereIn('uid',$uid)->column('id');
                //删除问题
                db('question')->whereIn('id',$question_ids)->update(['status'=>0]);
                //删除问题下的回答
                db('answer')->whereIn('question_id',$question_ids)->update(['status'=>0]);
                //删除该用户的回答
                db('answer')->whereIn('uid',$uid)->update(['status'=>0]);
                //删除操作记录
                db('action_log')->whereIn('uid',$uid)->update(['status'=>0]);
                db('action_log_all')->whereIn('uid',$uid)->update(['status'=>0]);

                //删除用户的文章
                $article_ids = db('article')->whereIn('uid',$uid)->column('id');
                db('article')->whereIn('id',$article_ids)->update(['status'=>0]);
                db('article_comment')->whereIn('article_id',$article_ids)->update(['status'=>0]);
                db('article_comment')->whereIn('uid',$uid)->update(['status'=>0]);

                //删除通知和私信数据
                db('users_inbox')->whereIn('uid',$uid)->update(['status'=>0]);
                db('users_inbox_dialog')->whereRaw('`sender_uid` IN('.implode(',',$uid).') OR `recipient_uid` IN('.implode(',',$uid).')')->update(['status'=>0]);

                db('users_notify')->whereRaw('`sender_uid` IN('.implode(',',$uid).') OR `recipient_uid` IN('.implode(',',$uid).')')->update(['status'=>0]);

                //删除首页数据
                db('post_relation')->whereIn('uid',$uid)->update(['status'=>0]);
            }
            db()->commit();
            return true;
        }
        catch (\Exception $e)
        {
            self::setError($e->getMessage());
            db()->rollback();
            return false;
        }
    }

    /**
     * 恢复用户
     * @param $uid
     * @return bool
     */
    public static function recoverUsers($uid): bool
    {
        $uid = is_array($uid) ? $uid : explode(',',$uid);
        db()->startTrans();
        try {
            db('users')->whereIn('uid',$uid)->update(['status'=>1]);
            //问题
            $question_ids = db('question')->whereIn('uid',$uid)->column('id');
            //删除问题
            db('question')->whereIn('id',$question_ids)->update(['status'=>1]);
            //删除问题下的回答
            db('answer')->whereIn('question_id',$question_ids)->update(['status'=>1]);
            //删除该用户的回答
            db('answer')->whereIn('uid',$uid)->update(['status'=>1]);
            //删除操作记录
            db('action_log')->whereIn('uid',$uid)->update(['status'=>1]);
            db('action_log_all')->whereIn('uid',$uid)->update(['status'=>1]);

            //删除用户的文章
            $article_ids = db('article')->whereIn('uid',$uid)->column('id');
            db('article')->whereIn('id',$article_ids)->update(['status'=>1]);
            db('article_comment')->whereIn('article_id',$article_ids)->update(['status'=>1]);
            db('article_comment')->whereIn('uid',$uid)->update(['status'=>1]);

            //删除通知和私信数据
            db('users_inbox')->whereIn('uid',$uid)->update(['status'=>1]);
            db('users_inbox_dialog')->whereRaw('`sender_uid` IN('.implode(',',$uid).') OR `recipient_uid` IN('.implode(',',$uid).')')->update(['status'=>1]);

            db('users_notify')->whereRaw('`sender_uid` IN('.implode(',',$uid).') OR `recipient_uid` IN('.implode(',',$uid).')')->update(['status'=>1]);

            //删除首页数据
            db('post_relation')->whereIn('uid',$uid)->update(['status'=>1]);
            db()->commit();
            return true;
        }
        catch (\Exception $e)
        {
            self::setError($e->getMessage());
            db()->rollback();
            return false;
        }
    }

    /**
     * 判断用户登录，并返回所有信息，写入session
     * @param $account
     * @param $password
     * @param int $remember
     * @param string $client
     * @return bool|mixed
     */
    public static function getLogin($account, $password, int $remember = 0, string $client = 'pc')
    {
        $user = self::checkUserExist($account);
        if (!$user) {
            self::setError('用户不存在');
            return false;
        }

        if($user['status']==0 || $user['status']==2)
        {
            self::setError('用户不存在或未审核通过');
            return false;
        }

        $errorCount = cache($user['uid'].'_error_count') ? : 0;
        if ($user['password']!=compile_password($password,$user['salt']) || cache($user['uid'].'_error_time'))
        {
            $errors_exceeds_limit_password = intval(get_setting('errors_exceeds_limit_password'));
            $password_error_limit_time = intval(get_setting('password_error_limit_time'));

            //限制时长
            if(cache($user['uid'].'_error_time') && $password_error_limit_time)
            {
                $time = $password_error_limit_time - round((time()- (int)cache($user['uid'].'_error_time'))/60) ;
                self::setError('请等待'.$time.'分钟后重试');
                cache($user['uid'].'_error_count',null);
            }else{
                ++$errorCount;
                if($errors_exceeds_limit_password && $errors_exceeds_limit_password<=$errorCount)
                {
                    cache($user['uid'].'_error_time',time(),['expire'=> $password_error_limit_time *60]);
                    self::setError('您已达到最大重试次数,请等待'.$password_error_limit_time.'分钟后重试');
                }else{
                    if($errors_exceeds_limit_password)
                    {
                        self::setError('用户密码不正确,您还可继续重试'. (int)($errors_exceeds_limit_password - $errorCount) .'次');
                        cache($user['uid'].'_error_count',$errorCount);
                    }else{
                        self::setError('用户密码不正确');
                    }
                }
            }
            return false;
        }
        $user['client'] = $client;
        return self::extracted($user,$remember);
    }

    /**
     * 新用户注册
     * @param string $account
     * @param string $password
     * @param array $extend
     * @param bool $login
     * @param bool $admin_register
     * @param string $client
     * @return mixed
     */
    public static function registerUser(string $account, string $password, array $extend = [], bool $login=true, $admin_register=false, string $client = 'pc')
    {
        if (!$account || !$password) {
            self::setError('账号或密码必填');
            return false;
        }

        if (self::checkUserExist($account) || (isset($extend['nick_name']) && self::checkUserExist($extend['nick_name']))) {
            self::setError('用户已存在');
            return false;
        }

        $data = array();
        $salt = RandomHelper::alnum();
        $data['create_time'] = time();
        $data['update_time'] = time();

        $data = $extend ? array_merge($data, $extend) : $data;
        $data['user_name'] = $account;
        $data['password'] = compile_password($password, $salt);
        $data['salt'] = $salt;
        $data['reg_ip'] = request()->ip();
        $data['is_first_login'] = 1;
        $data['nick_name'] = $data['nick_name'] ?? $account;

        $pinyin = new Pinyin();
        $url_token= $pinyin->permalink($account,'');
        $token = db('users')->where('url_token',$url_token)->value('url_token');
        $url_token = $token ? $url_token.'-'.time() : $url_token;
        if(!$url_token)
        {
            $url_token = StringHelper::uuid('uniqid');
        }

        $data['url_token'] = $url_token;
        $data['integral_group_id']= $extend['integral_group_id']??($data['integral_group_id'] ?? 1);
        $data['reputation_group_id']= $extend['reputation_group_id']??($data['reputation_group_id'] ?? 1);
        $data['group_id'] = $extend['group_id']??($data['group_id']??4);
        $data['client'] = $client;

        if(!$data['group_id'] && get_setting('register_valid_type')=='email')
        {
            $data['group_id']=3;//未验证系统组
        }

        if(get_setting('register_valid_type')=='admin')
        {
            $data['status']=2;
        }
        //$data['group_id'] = $data['group_id']?:4;
        try {
            $uid = db('users')->strict(false)->insertGetId($data);
            //用户通知配置
            db('users_extends')->insert([
                'uid'=>$uid,
                'inbox_setting'=>'all',
                'notify_setting'=>json_encode(NotifyHelper::getDefaultNotifyConfig(),JSON_UNESCAPED_UNICODE),
            ]);

            //添加积分记录
            LogHelper::addIntegralLog('REGISTER',$uid,'users',$uid);
            ElasticSearch::instance()->create('users',db('users')->find($uid));

            if($admin_register)
            {
                return $uid;
            }

            if (get_setting('register_valid_type') == 'admin') {
                self::setError('注册成功，等待管理员审核');
                return false;
            } else {
                if($login)
                {
                    Users::directLogin($uid);
                }
                return $uid;
            }
        }catch (Exception $e)
        {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取用户信息
     * @param $uid
     * @param bool $extend
     * @return array|false
     */
    public static function getUserInfo($uid, bool $extend=true)
    {
        if (!$uid) {
            $user_info['avatar'] = '/static/common/image/default-avatar.svg';
            $user_info['url'] = 'javascript:;';
            $user_info['name'] = '游客';
            $user_info['group_id'] = 5;
            $user_info['status'] = 1;
            $user_info['group_name'] = '游客组';
            $user_info['verified'] = '';
            $user_info['reputation_group_id'] = 1;
            $user_info['integral_group_id'] = 1;
            $user_group_info = self::getUserGroupInfo();
            if($user_group_info)
            {
                $user_info = array_merge($user_info,$user_group_info);
            }
            return $user_info;
        }

        $user_info = db('users')->find($uid);
        $user_extend_info = $extend ? db('users_extends')->withoutField('id')->where('uid',$uid)->find() : [];

        if(!$user_info)
        {
            return false;
        }
        $user_info['is_online'] = self::isOnline($uid);
        $user_info['avatar'] = $user_info['avatar'] ? : '/static/common/image/default-avatar.svg';
        $user_info['url'] = get_user_url($uid);
        $user_info['name'] = $user_info['nick_name'];
        $verify_info = $user_info['verified'] ? db('users_verify_type')->where(['name'=>$user_info['verified'],'status'=>1])->field('icon,title')->find() : [];
        $user_info['verified_icon'] = $verify_info ? $verify_info['icon'] : '';
        $user_info['verified_name'] = $verify_info ? $verify_info['title'] : '';

        $user_group_info = self::getUserGroupInfo($uid);
        if($user_group_info)
        {
            $user_info = array_merge($user_info,$user_group_info);
        }

        if($user_extend_info && $extend)
        {
            $user_info = array_merge($user_info,$user_extend_info);
        }
        return $user_info;
    }

    /**
     * 获取用户信息，根据字段
     * @param $uid
     * @param mixed $field
     * @param bool $extend
     * @param mixed $extend_field
     * @return false|array
     */
    public static function getUserInfoByUid($uid,$field='*',bool $extend=false,$extend_field='*')
    {
        $user_info = db('users')->where(['uid'=>$uid])->field($field)->find();
        if(!$user_info)
        {
            return false;
        }

        if (isset($user_info['avatar']) && $user_info['avatar']) {
            $user_info['avatar'] = ImageHelper::replaceImageUrl($user_info['avatar']);
        } else {
            $user_info['avatar'] = request()->domain().'/static/common/image/default-avatar.svg';
        }

        if (!isset($user_info['signature']) || !$user_info['signature']) $user_info['signature'] = '暂无个人签名...';

        $user_info['url'] = get_user_url($uid);
        $user_info['name'] = $user_info['nick_name'] ??'';
        $verify_info = $user_info['verified'] ? db('users_verify_type')->where(['name'=>$user_info['verified'],'status'=>1])->field('icon,title')->find() : [];
        $user_info['verified_icon'] = $verify_info ? $verify_info['icon'] : '';
        $user_info['verified_name'] = $verify_info ? $verify_info['title'] : '';
        $user_extend_info = $extend ? db('users_extends')->withoutField('id')->where('uid',$uid)->field($extend_field)->find() : [];

        if($user_extend_info)
        {
            $user_info = array_merge($user_info,$user_extend_info);
        }

        return $user_info;
    }

    /**
     * 获取用户组信息
     * @param int $uid
     * @return array|mixed
     */
    public static function getUserGroupInfo(int $uid=0)
    {
        //游客组
        if(!$uid)
        {
            return [
                'permission'=>json_decode(db('admin_group')->where('id',5)->value('permission'),true)
            ];
        }

        static $groups = [];
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        // 执行查询
        //$map[] = ['status', '=', 1];
        $map[] = ['uid', '=', $uid];

        $user_info = db('users')->where($map)->field('reputation_group_id,group_id,integral_group_id')->find();

        //判断前台使用的分组类型
        if(get_setting('frontend_group_type')=='reputation')
        {
            $user_groups = db('users_reputation_group')->where("id", intval($user_info['reputation_group_id']))->field('title as group_name,permission,group_icon')->find();
        }else{
            $user_groups = db('users_integral_group')->where("id", intval($user_info['integral_group_id']))->field('title as group_name,permission,group_icon')->find();
        }
        //非普通用户使用系统组权限
        if(intval($user_info['group_id'])!=4)
        {
            $permission = db('admin_group')->where("id", intval($user_info['group_id']))->field('permission,title')->find();
            $user_groups['permission'] = $permission['permission'];
            $user_groups['group_name'] = $permission['title'];
            $user_groups['permission'] = json_decode($user_groups['permission'],true);
            $list = db('users_permission')->whereRaw("`group`='common' OR `group`='system'")->column('value','name');
        }else{
            $user_groups['permission'] = isset($user_groups['permission']) ? json_decode($user_groups['permission'],true) : [];
            $list = db('users_permission')->whereRaw("`group` = 'common' OR `group`='".get_setting('frontend_group_type')."'")->column('value','name');
        }
        $user_groups['group_name'] = $user_groups['group_name'] ?? '未知组';
        $user_groups['permission'] = array_merge($list,$user_groups['permission']);

        $groups[$uid] = $user_groups ?: [];
        return $user_groups;
    }

    /**
     * 根据用户ids获取用户信息
     * @param $ids
     * @param string $field
     * @param int $status
     * @return array|false|false[]
     */
    public static function getUserInfoByIds($ids,string $field='',int $status=1)
    {
        if (!is_array($ids) || count($ids) == 0) {
            return false;
        }
        $ids = array_unique($ids);

        $where = [];

        if($status!=99)
        {
            $where['status'] = $status;
        }

        $user_info = db('users')->where($where)->withoutField('password')->whereIn('uid',implode(',', $ids))->field($field)->select()->toArray();

        $data = array();
        if ($user_info)
        {
            $domain = request()->domain();
            foreach ($user_info as $key => $val)
            {
                $data[$val['uid']] = $val;
                $data[$val['uid']]['is_online'] = self::isOnline($val['uid']);
                $data[$val['uid']]['avatar'] = $val['avatar'] ? ImageHelper::replaceImageUrl($val['avatar']): $domain.'/static/common/image/default-avatar.svg';
                $data[$val['uid']]['name'] = $val['nick_name'];
                $data[$val['uid']]['url'] = get_user_url($val['uid']);
            }
        }
        return $data;
    }

    /**
     * 更新用户字段
     * @param $uid
     * @param $data //主表用户信息
     * @param $extend //附加表用户信息
     * @return mixed
     */
    public static function updateUserFiled($uid,$data=null,$extend=null): bool
    {
        // 更新主表
        if ($data) {
            if (isset($data['password']) && $data['password']) {
                $data['salt'] = $data['salt'] ?? RandomHelper::alnum();
                $data['password'] = compile_password($data['password'], $data['salt']);
            }

            $pinyin = new Pinyin();
            if (isset($data['url_token']) && $data['url_token']) $data['url_token'] = $pinyin->permalink($data['url_token'],'');

            if (!db('users')->where(['uid'=>$uid])->update($data)) return false;
        }

        // 更新附属表用户信息
        if ($extend && !db('users_extends')->where(['uid'=>$uid])->update($extend)) return false;

        //更新缓存信息
        if ($uid === session('login_uid')) {
            $user =self::getUserInfo($uid);
            session('login_user_info', $user);
        }

        return true;
    }

    /**
     * 检查用户名是否已存在
     * @param $account
     * @param string $field
     * @return mixed
     */
    public static function checkUserExist($account, string $field='*') {
        $where = $whereOr = array();
        if (MailHelper::isEmail($account)) {
            $where['email'] = $account;
        } else if (preg_match("/^1[3-9]\d{9}$/", $account)) {
            $where['mobile'] = $account;
        } else {
            $where['user_name'] = $account;
            $whereOr['nick_name']=$account;
        }
        return db('users')->where($where)->whereOr($whereOr)->field($field)->find();
    }

    /**
     * 解析内容中的用户名
     * @param $content
     * @param bool $with_user 返回用户信息
     * @param bool $to_uid 转换用户名为uid
     * @return array
     */
    public static function parseAtUser($content, bool $with_user = false, bool $to_uid = false): array
    {
        $result = $all_users = array();
        $content_uid = '';
        preg_match_all('/@([^@,:\s,]+)/i', strip_tags($content), $result);
        if (is_array($result[1]))
        {
            $match_name = array();
            foreach ($result[1] as $user_name)
            {
                if (in_array($user_name, $match_name, true))
                {
                    continue;
                }
                $match_name[] = $user_name;
            }

            $match_name = array_unique($match_name);

            arsort($match_name);

            $content_uid = $content;

            foreach ($match_name as $user_name)
            {
                $user_info = self::checkUserExist($user_name);
                if ($user_info)
                {
                    $content = str_replace('@' . $user_name, '<a href="'.get_user_url($user_info['uid'])  . '" class="aw-username text-primary mr-1" data-id="'.$user_info['uid'].'" target="_blank">@' . $user_info['nick_name'] . '</a>', $content);
                    if ($to_uid)
                    {
                        $content_uid = str_replace('@' . $user_name, '@' . $user_info['uid'], $content_uid);
                    }
                    if ($with_user)
                    {
                        $all_users[$user_info['uid']] = $user_info;
                    }
                }
            }
        }
        return [$content,$all_users,$content_uid];
    }

    /**
     * 获取用户列表
     * @param array $where
     * @param array $order
     * @param int $current_page
     * @param int $per_page
     * @param int $uid
     * @param string $pjax
     * @return array
     */
    public static function getUserList(array $where=[], array $order=[],int $current_page=1, int $per_page=10, int $uid=0,string $pjax='mainTab'): array
    {
        $order = $order ?:$order= ['create_time'=>'desc'];

        $list = db('users')->where($where)->withoutField('password')->order($order)->paginate([
            'list_rows' => $per_page ?: 10, //每页数量
            'page' => $current_page ?: 1, //当前页面
            'query'=>request()->param(),
            'pjax'=>$pjax
        ]);
        $page = $list->render();
        $user = $list->toArray();
        $result = array();
        foreach ($user['data'] as $value)
        {
            $result[$value['uid']] = $value;
            $result[$value['uid']]['is_online'] = self::isOnline($value['uid']);
            $result[$value['uid']]['has_focus'] = self::checkFocus($uid,$value['uid']);
            $result[$value['uid']]['avatar'] = $value['avatar'] ?: '/static/common/image/default-avatar.svg';
            $result[$value['uid']]['url'] = get_user_url($value['uid']);
            $result[$value['uid']]['name'] = $value['nick_name'];
            $verify_info = $result[$value['uid']]['verified'] ? db('users_verify_type')->where(['name'=>$result[$value['uid']]['verified'],'status'=>1])->field('icon,title')->find() : [];
            $result[$value['uid']]['verified_icon'] = $verify_info ? $verify_info['icon'] : '';
            $result[$value['uid']]['verified_name'] = $verify_info ? $verify_info['title'] : '';
            //判断前台使用的分组类型

            if(intval($value['group_id'])!=4)
            {
                $result[$value['uid']]['group_name'] = db('admin_group')->where("id", intval($value['group_id']))->value('title');
            }else{
                if(get_setting('frontend_group_type')=='reputation')
                {
                    $result[$value['uid']]['group_name'] = db('users_reputation_group')->where("id", intval($value['reputation_group_id']))->value('title');
                }else{
                    $result[$value['uid']]['group_name'] = db('users_integral_group')->where("id", intval($value['reputation_group_id']))->value('title');
                }
            }
        }
        return ['list'=>$result,'page'=>$page,'total'=>$user['last_page']];
    }

    /**
     * 是否关注
     * @param $uid
     * @param $target_uid
     * @return mixed
     */
    public static function checkFocus($uid,$target_uid)
    {
        return db('users_follow')->where(['fans_uid'=>$uid,'friend_uid'=>$target_uid])->value('id');
    }

    //检测用户是否在线
    public static function isOnline($uid): bool
    {
        //1分钟内不再检测
        $last_login_time = cache('last_login_time_'.$uid);

        if($last_login_time && time() < $last_login_time +(get_setting('online_check_time') * 60))
        {
            return true;
        }

        $map[] = array('uid', '=', $uid);
        $last_login_time = db('users_online')->where($map)->value('last_login_time');

        if(!$last_login_time) {
            return false;
        }

        if ($last_login_time + ((int)get_setting('online_check_time') * 60) > time()) {
            cache('last_login_time_'.$uid,$last_login_time,60);
            return true;
        }
        return false;
    }

    /**
     * 更新用户通知未读数
     * @param $recipient_uid
     * @return bool
     */
    public static function updateNotifyUnread($recipient_uid)
    {
        $unread_num = db('users_notify')->where(['recipient_uid'=>(int)$recipient_uid,'read_flag'=>0])->count();
        return self::updateUserFiled($recipient_uid,['notify_unread'=>$unread_num]);
    }

    /**
     * 更新私信数
     * @param $uid
     * @return mixed
     */
    public static function updateInboxUnread($uid)
    {
        $sender_unread_num = db('users_inbox_dialog')->where(['sender_uid'=> (int)$uid])->sum('sender_unread');
        $recipient_unread_num = db('users_inbox_dialog')->where(['recipient_uid'=> (int)$uid])->sum('recipient_unread');
        $unread_num = (int)($sender_unread_num + $recipient_unread_num);
        return self::updateUserFiled($uid,['inbox_unread'=>$unread_num]);
    }

    /**
     * 获取热门用户
     * @param int $uid
     * @param array $where
     * @param array $order
     * @param int $per_page
     * @param int $page
     * @return mixed
     */
    public static function getHotUsers(int $uid=0, array $where=[], array $order=[], int $per_page=5, int $page=1,$is_api= false)
    {
        $where = !empty($where) ? $where : [['status','=',1],['reputation','>',0]];
        $order = !empty($order) ? $order : ['reputation'=>'DESC','answer_count'=>'DESC'];
        $list = db('users')
            ->where([['uid','<>',$uid]])
            ->where($where)
            ->orderRaw('RAND()')
            ->order($order)
            ->limit($per_page)
            ->select()
            ->toArray();
        $avatar = request()->domain().'/static/common/image/default-avatar.svg';
        foreach ($list as $key=>$val)
        {
            if($is_api)
            {
                $list[$key]['avatar'] =$val['avatar'] ? ImageHelper::replaceImageUrl($val['avatar']) : $avatar;
            }
            $list[$key]['url'] = get_user_url($val['uid']);
            $list[$key]['is_focus'] = db('users_follow')->where(['fans_uid'=> (int)$val['uid']])->value('id');
        }
        return $list;
    }

    /**
     * 更新主页访问量
     * @param $user_id
     * @param int $uid
     * @return bool
     */
    public static function updateUsersViews($user_id, int $uid=0): bool
    {
        $cache_key = md5('cache_user_'.$user_id.'_'.$uid);
        $cache_result = cache($cache_key);
        if($cache_result) {
            return true;
        }
        cache($cache_key,$cache_key,['expire'=>60]);
        return db('users')->where(['uid'=>$user_id])->inc('views_count')->update();
    }

    /**
     * 退出登录
     * @return bool
     */
    public static function logout(): bool
    {
        $token = session('login_token');
        if($token)
        {
            TokenHelper::delete($token);
        }
        session('admin_login_uid',null);
        session('admin_login_user_info',null);
        session('login_uid',null);
        session('login_user_info',null);
        $cookieKey = config('app.token')['key'].'_'.md5('login_uid');
        cookie($cookieKey,null);
        self::$_is_login = false;
        self::$_user = null;
        hook("user_logout", self::$_user);
        return true;
    }

    /**
     * 手机号登录注册
     * @param $mobile
     * @param $code
     * @param array $extend
     * @param int $remember
     * @param bool $ignoreApproval
     * @param string $client
     * @return mixed
     */
    public static function loginByMobile($mobile,$code,array $extend=[],int $remember=0, $ignoreApproval = false, $client = 'pc')
    {
        $user = self::checkUserExist($mobile);
        if(!$code)
        {
            self::setError('验证码不可为空');
            return false;
        }
        $cache_code = cache('sms_'.$mobile);

        if($cache_code!=$code)
        {
            self::setError('验证码不正确');
            return false;
        }

        if($user)
        {
            if($user['status']==0 || $user['status']==2)
            {
                self::setError('用户不存在或未审核通过');
                return false;
            }
            self::directLogin($user['uid']);
            return $user['uid'];
        }

        $data = array();
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['user_name'] = $extend['username']??'m'.$mobile;
        $data['mobile'] = $mobile;
        $data['group_id'] = $extend['group_id']??($data['group_id']??4);
        $data['salt'] = RandomHelper::alnum();
        $data['password'] = isset($extend['password']) ? compile_password($extend['password'], $data['salt']) : compile_password(RandomHelper::alnum(16), $data['salt']);
        $data['is_first_login'] = 1;
        $data['nick_name'] = $extend['username']??'m_'.md5($mobile);
        $pinyin = new Pinyin();
        $url_token = $pinyin->permalink($data['user_name'],'');
        $data['url_token'] = $url_token;
        $data['is_valid_mobile'] = 1;
        $data['integral_group_id']= $data['integral_group_id'] ?? 1;
        $data['reputation_group_id']= $data['reputation_group_id'] ?? 1;
        $data['client'] = $client;
        if(!$data['group_id'] && get_setting('register_valid_type')=='email' && get_setting('register_type')=='all')
        {
            $data['group_id']=3;//未验证系统组
        }
        //$data['group_id'] = $data['group_id'] ?? 4;
        if (get_setting('register_valid_type') == 'admin' && !$ignoreApproval)  $data['status'] = 2;

        $uid = db('users')->insertGetId($data);

        //用户通知配置
        db('users_extends')->insert([
            'uid'=>$uid,
            'inbox_setting'=>'all',
            'notify_setting'=>json_encode(NotifyHelper::getDefaultNotifyConfig(),JSON_UNESCAPED_UNICODE),
        ]);

        //添加积分记录
        LogHelper::addIntegralLog('REGISTER',$uid,'users',$uid);

        ElasticSearch::instance()->create('users',db('users')->find($uid));

        if (get_setting('register_valid_type') == 'admin' && !$ignoreApproval) {
            self::setError('注册成功，等待管理员审核');
            return false;
        } else {
            self::directLogin($uid,$remember);
            return $uid;
        }
    }

    /**
     * 直接登录
     * @param $uid
     * @param int $remember
     * @return mixed
     */
    public static function directLogin($uid,int $remember=0)
    {
        self::logout();
        $user = self::getUserInfo($uid);
        if (!$user) {
            self::setError('用户不存在');
            return false;
        }
        return self::extracted($user,$remember);
    }

    /**
     * @param $user
     * @param int $remember
     * @return false|mixed
     */
    public static function extracted($user,int $remember=0)
    {
        //登录成功后钩子
        hook('loginAfter',['user'=>$user,'remember'=>$remember]);

        $ip = IpHelper::getRealIp();
        if ($user['status'] == 1) {
            if($remember && get_setting('remember_login_enable')=='Y') {
                config('session.expire',$remember);
                $cookieKey = config('app.token')['key'].'_'.md5('login_uid');
                cookie($cookieKey,authCode($user['uid'],'ENCODE'),['expire' =>$remember]);
            };

            $data['last_login_time'] = time();
            $data['last_login_ip'] = $ip;
            $data['client'] = $user['client'] ?? 'pc';
            self::update($data, ['uid' => $user['uid']]);

            session('last_login_time', $data['last_login_time']);
            if ($data['last_login_ip']) {
                session('last_login_ip', $data['last_login_ip']);
            } else {
                session('last_login_ip', $ip);
            }

            session('access_time', time());

            // 检测是否有同一IP的记录，有更新，否则 添加
            $map = array();
            //$map[] = ['last_login_ip', '=', $ip];
            $map[] = ['uid', '=', $user['uid']];
            $online_id = db('users_online')->where($map)->value('id');
            $last_url = request()->url();
            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            $data = array();
            $data['uid'] = $user['uid'];
            $data['last_login_time'] = time();
            if (!$online_id) {
                // 插入在线用户表
                $data['last_url'] = $last_url;
                $data['user_agent'] = $user_agent;
                $data['last_login_ip'] = $ip;
                db('users_online')->insert($data);
            } else {
                // 更新在线用户表
                $data['last_login_ip'] = $ip;
                $data['last_url'] = $last_url;
                $data['user_agent'] = $user_agent;
                db('users_online')->where($map)->save($data);
            }
            unset($user['password']);

            session('login_uid', $user['uid']);
            session('login_user_info', $user);

            self::$_user = $user;
            session('login_token',RandomHelper::uuid());
            $token = session('login_token');

            self::$_token = $token;
            self::$_is_login = true;

            //token 过期时间
            $token_expire_time = $remember ?: config('token.expire');
            TokenHelper::set($token, $user['uid'], $token_expire_time);

            //添加行为记录
            LogHelper::addActionLog('user_login', 'users', $user['uid'], $user['uid']);
            //添加积分记录
            LogHelper::addIntegralLog('LOGIN', $user['uid'], 'users', $user['uid']);
            return $user;
        }

        if ($user['status']==0) {
            self::setError('该账号已被管理员删除！');
        }

        if ($user['status'] == 3 ) {
            $users_forbidden = db('users_forbidden')->where(['uid' => $user['uid'], 'status' => 1])->find();
            if($users_forbidden && $users_forbidden['forbidden_time']>time())
            {
                self::setError('该账号已被管理员封禁！封禁原因：' . $users_forbidden['forbidden_reason'] . ';解封时间：' . date('Y-m-d H:i:s', $users_forbidden['forbidden_time']));
            }else{
                db('users')->where(['uid'=>$user['uid']])->update(['status'=>1]);
            }
        }
        self::setError('账号异常');
        return false;
    }

    /**
     * 更新用户积分组
     * @param $uid
     * @return mixed
     */
    public static function updateUsersIntegralGroup($uid)
    {
        //积分组
        $user_score = db('users')->where(['uid'=> (int)$uid])->value('integral');
        $user_score = $user_score?:0;
        $user_group_id = db('users_integral_group')->where([
            ['min_integral','<=',$user_score],
            ['max_integral','>',$user_score]
        ])->value('id');

        if($user_group_id)
        {
            return db('users')->where('uid', (int)$uid)->update(['integral_group_id'=>$user_group_id]);
        }
        return false;
    }

    /**
     * 更新用户威望组
     * @param $uid
     * @return mixed
     */
    public static function updateUsersReputationGroup($uid)
    {
        $user_power = db('users')->where(['uid'=> (int)$uid,'status'=>1])->value('reputation');

        $user_group_info = db('users_reputation_group')->where([
            ['min_reputation','<=',$user_power],
            ['max_reputation','>',$user_power]
        ])->field('id,permission')->find();

        if($user_group_info)
        {
            $permission = json_decode($user_group_info['permission'],true);
            if(isset($permission['available_invite_count']))
            {
                self::updateUserFiled($uid,['available_invite_count'=>$permission['available_invite_count']]);
            }
            return db('users')->where('uid', (int)$uid)->update(['reputation_group_id'=>$user_group_info['id']]);
        }
        return false;
    }

    /**
     * 获取用户当天发布文章或问题的数量
     * @param $uid
     * @param $table 'question|article表
     * @return mixed
     */
    public static function getUserPublishNum($uid,$table)
    {
        if (!in_array($table,array('question','article'))){
            return false;
        }
        // 获取当天0秒unix时间戳
        $today_first_seconds = strtotime(date("Y-m-d 00:00:00"));
        // 获取当天最后一秒unix时间戳
        $today_last_seconds = strtotime(date("Y-m-d 23:59:59"));

        return db($table)->where(['uid'=>$uid,'status'=>1])->whereBetweenTime('create_time',$today_first_seconds,$today_last_seconds)->count();
    }

    /**
     * 验证用户是否需要提交验证码才可发文
     * @param $uid
     * @param $table 'question|article|answer表
     * @return bool
     */
    public static function checkUserPublishTimeAndCount($uid,$table): bool
    {
        if (!in_array($table,array('question','article','answer'))){
            return false;
        }
        // 获取时间戳
        $beginTime = time()-(intval(get_setting('publish_content_verify_time')*60));
        $count = db($table)->where([['uid','=',$uid],['status','=',1],['create_time','>=',$beginTime]])->count();
        if($count>=intval(get_setting('publish_content_verify_num')))
        {
            return false;
        }
        return true;
    }

    // 重置密码
    public static function resetPassword($uid, $password)
    {
        $salt = RandomHelper::alnum();
        return self::update([
            'salt' => $salt,
            'password' => compile_password($password, $salt)
        ], ['uid' => $uid]);
    }

    // 批量封禁IP
    public static function batchForbiddenIp($uid)
    {
        $uid = is_array($uid) ? $uid : explode(',', $uid);
        if (self::whereIn('uid', $uid)->save(['forbidden_ip' => 1])) {
            $ip = self::whereIn('uid', $uid)->column('last_login_ip', 'uid');
            $arr = [];
            $time = time();
            foreach ($ip as $key => $val) {
                $arr[] = [
                    'uid' => $key,
                    'ip' => $val,
                    'time' => $time
                ];
            }

            return db('forbidden_ip')->insertAll($arr);
        }

        return false;
    }

    // 批量解除封禁Ip
    public static function batchLiftIp($uid)
    {
        $uid = is_array($uid) ? $uid : explode(',', $uid);
        if (self::whereIn('uid', $uid)->save(['forbidden_ip' => 0])) {
            return db('forbidden_ip')->whereIn('uid', $uid)->delete();
        }

        return false;
    }

    // 封禁用户IP
    public static function forbiddenIp($data)
    {
        if (self::where('uid', $data['uid'])->save(['forbidden_ip' => 1])) {
            return db('forbidden_ip')->strict(false)->insert($data);
        }

        return false;
    }

    // 解封用户IP
    public static function liftIp($uid)
    {
        if (self::where('uid', $uid)->save(['forbidden_ip' => 0])) {
            return db('forbidden_ip')->where('uid', $uid)->delete();
        }

        return false;
    }
}