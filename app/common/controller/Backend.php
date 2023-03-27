<?php
namespace app\common\controller;
use app\common\library\builder\FormBuilder;
use app\common\library\builder\MakeBuilder;
use app\common\library\builder\TableBuilder;
use app\common\library\helper\NotifyHelper;
use app\common\library\helper\AuthHelper;
use app\common\library\helper\DataHelper;
use app\common\library\helper\UpgradeHelper;
use app\common\traits\Curd;
use app\model\admin\AdminLog;
use app\model\Users;
use think\App;
use think\facade\Request;
use think\helper\Str;

/**
 * 后台控制器基类
 */
class Backend extends Base
{
    use Curd;
    protected $tableBuilder;
    protected $formBuilder;
    protected $makeBuilder;
    protected $returnUrl;

    /**
     * 无需登录方法
     * @var
     */
    protected $noNeedLogin=['login'];

    /**
     * 无需鉴权方法
     * @var
     */
    protected $noNeedRight;
    protected $auth;
    protected $user_id;
    protected $user_info;
    protected $table;
    protected $model;
    protected $pk='id';

    public function __construct(App $app)
    {
        parent::__construct($app);

        //判断是否入口文件是否一致
        $baseFile = $this->request->baseFile();
        if($baseFile != '/'.config('app.admin'))
        {
            $this->error404();
        }

        //检查IP封禁
        $this->checkIpAllowed();

        if (!$this->request->plugin) {
            $this->view->config([
                'view_path' => app_path() . DS . 'backend' . DS . 'view' . DS,
            ]);
        }

        //数据表格构造器
        $this->tableBuilder = TableBuilder::getInstance();
        //表单构造器
        $this->formBuilder =  FormBuilder::getInstance();

        //CURD页面构造器
        $this->makeBuilder = MakeBuilder::getInstance();

        //权限验证器
        $this->auth =AuthHelper::instance();
        if(session('admin_login_uid') && getLoginUid())
        {
            $this->user_info = Users::getUserInfo(getLoginUid());
        }

        $this->user_id = $this->user_info ? $this->user_info['uid'] : 0;
        $controller = $this->request->controller();
        $actionName = Str::snake(request()->action());

        //检验权限地址
        $checkPath = $controller.'/'.strtolower($actionName);
        if ($this->request->plugin) {
            $checkPath = 'plugins/'.$this->request->plugin . '/' . $this->request->controller() . '/' . strtolower($actionName);
        }
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //检测是否登录
            if (!$this->user_id && !getLoginUid()) {
                hook('admin_no_login', $this);
                $this->loading('/account/login');
            }

            if (!$this->user_id && getLoginUid()) {
                $this->loading('index/login');
            }
            // 判断是否需要验证权限
            // 判断控制器和方法判断是否有对应权限
            if(!$this->auth->isSuperAdmin())
            {
                if (!$this->auth->match($this->noNeedRight) && !$this->auth->check($checkPath, $this->user_id)) {
                    $this->error('您没有访问权限', '/');
                }
            }else{
                if ($this->request->plugin && !$this->auth->check($checkPath, $this->user_id)) {
                    $this->error404();
                }
            }
        }

        //渲染菜单
        if ($this->user_id)
        {
            $menu = $this->auth->getTreeMenu();
            $this->view->assign($menu);
            $breadCrumb = DataHelper::formatBreadCrumb($this->auth->getBreadCrumb($checkPath));
            // 菜单
            $this->view->assign(['breadCrumb' => $breadCrumb]);
            if(get_setting('record_admin_log')=='Y')
            {
                // 进行操作日志的记录
                AdminLog::record();
            }
        }

        $return_url = $_SERVER['HTTP_REFERER'] ?? '/';
        session('return_url',base64_encode($return_url));
        $this->returnUrl = base64_decode(session('return_url'));
        $this->assign('return_url',base64_decode(session('return_url')));

        if(get_setting('email_enable')!='Y' ||get_setting('email_host')=='' || get_setting('email_username')=='')
        {
            $this->assign('email_tips',1);
        }

        $this->assign([
            'baseUrl'=> Request::domain(),
            'notify_count'=>NotifyHelper::getNotifyCount(),
            'notify_list'=>NotifyHelper::getNotifyTextList(),
            'user_id'=>$this->user_id,
            'version'=>env('APP_DEBUG') ? time() : config('version.version'),
            'user_info'=>$this->user_info,
            '_ajax' =>$this->request->param('_ajax',0),
            '_ajax_open'=>$this->request->param('_ajax_open',0),
            'base_url' => $this->request->domain().$this->request->baseFile(),
            'cdnUrl' => get_setting('cdn_url', Request::domain()),
            'setting'=>get_setting(),
            'admin_block'=>app_path(). DS.'backend'. DS . 'view' . DS.'block.php',
            'version_info'=>UpgradeHelper::instance()->checkVersion(),
        ]);

        // 控制器初始化
        $this->initialize();
    }

    public function initialize(){}
}