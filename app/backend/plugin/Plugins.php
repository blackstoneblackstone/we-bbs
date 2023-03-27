<?php

namespace app\backend\plugin;
use app\common\controller\Backend;
use app\common\library\helper\PluginsHelper;
use think\App;
use think\facade\Request;

class Plugins extends Backend
{
    protected $PluginsHelper;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->PluginsHelper = PluginsHelper::instance();
    }

    public function index()
    {
        $status = $this->request->param('status',3);
        if ($this->request->param('_list')) {
            // 获取模板列表
            $type = $this->request->param('type');
            $list = $this->PluginsHelper->getPluginsList($status,$type);
            // 渲染输出
            return [
                'total'        => count($list),
                'per_page'     => 1000,
                'current_page' => 1,
                'last_page'    => 1,
                'data'         => $list,
            ];
        }
        $columns = [
            ['title', '插件名称'],
            ['type', '应用类型','tag','',['module'=>'模块','plugins'=>'插件']],
            ['name','插件标识'],
            ['description','备注'],
            ['author', '作者'],
            ['version', '版本'],
            ['button', '操作', 'text']
        ];
        $search = [
            ['select', 'type', '应用类型', '=','',['module'=>'模块','plugins'=>'插件']],
        ];

        return $this->tableBuilder
            ->addColumns($columns)
            ->setSearch($search)
            ->setUniqueId('name')
            ->setDataUrl( Request::baseUrl().'?_list=1&status='.$status)
            ->addTopButtons(['design'=>[
                    'title'   => '设计插件',
                    'class'   => 'btn btn-primary aw-ajax-open',
                    'href'    => '',
                    'url'     =>(string)url('design')
                ],
                'import'=>[
                    'title'   => '导入插件',
                    'class'   => 'btn btn-primary aw-ajax-open',
                    'href'    => '',
                    'url'    => (string)url('import'),
                ],])
            ->setEditUrl((string)url('config', ['name' => '__id__']))
            ->setLinkGroup([
                [
                    'title'=>'全部',
                    'link'=>(string)url('index', ['status' => 3]),
                    'active'=> $status==3
                ],
                [
                    'title'=>'已启用',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'未启用',
                    'link'=>(string)url('index', ['status' => 2]),
                    'active'=> $status==2
                ],
                [
                    'title'=>'未安装',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
            ])
            ->setPagination('false')
            ->setParentIdField('pid')
            ->fetch();
    }

    //插件配置
    public function config(string $name='')
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $name = $data['name'];
            unset($data['name']);
            $postConfig = [];
            foreach ($data as $k=>$v)
            {
                if(strstr($k,'_aws_'))
                {
                    $k = explode('_aws_',$k);
                    $postConfig[$k[0]][$k[1]] = $v;
                }else{
                    $postConfig[$k] = $v;
                }
            }

            $config = $this->PluginsHelper->config($name,true);
            foreach ($config as $key=>$value)
            {
                if(isset($value['config']))
                {
                    foreach ($value['config'] as $k=>$v)
                    {
                        if($v['type']=='select2' || $v['type']=='checkbox')
                        {
                            $config[$key]['config'][$k]['value'] = implode(',',$postConfig[$key][$k]??[]);
                        }else{
                            $config[$key]['config'][$k]['value'] = $postConfig[$key][$k]??'';
                        }
                    }
                }else{
                    if($value['type']=='select2' || $value['type']=='checkbox')
                    {
                        $config[$key]['value'] = implode(',',$postConfig[$key]??[]);
                    }else{
                        $config[$key]['value'] = $postConfig[$key]??'';
                    }
                }
            }
            //写入插件配置备份文件
            $this->PluginsHelper->setPluginsConfig($name,$config);
            $result=db('plugins')->where('name',$name)->update(['config'=>json_encode($config,JSON_UNESCAPED_UNICODE)]);
            if ($result) {
                $this->PluginsHelper->getPluginsConfigs($name,'',true);
                $this->PluginsHelper->getConfig($name,true);
                $this->success('修改成功');
            }

            $this->error('提交失败或数据无变化');
        }

        $config = $this->PluginsHelper->config($name,true);
        if(!$config)
        {
            $this->error('该插件无需配置','index');
        }
        // 如果插件自带配置模版的话加载插件自带的，否则调用表单构建器
        $file = root_path('plugins') .$name.DS. 'config.html';
        if (file_exists($file)) {
            $this->assign([
                'config' => $config
            ]);
            return $this->fetch($file);
        }
        // 获取字段数据
        $columns = $this->PluginsHelper->makeAddColumns($config);
        // 判断是否分组
        $group = $this->PluginsHelper->checkConfigGroup($config);
        // 构建页面
        $this->formBuilder->setFormUrl((string)url('config'))->addHidden('name', $name);

        $group ? $this->formBuilder->addGroup($columns) : $this->formBuilder->addFormItems($columns);
        return $this->formBuilder->fetch();
    }

    //安装插件
    public function install($name)
    {
        $result = $this->PluginsHelper->install($name);
        if($result['code'])
        {
            $this->PluginsHelper->getPluginsConfigs($name,'',true);
            $this->PluginsHelper->getConfig($name,true);
            $this->success($result['msg'],$this->returnUrl);
        }
        $this->error($result['msg']);

    }

    //卸载插件
    public function uninstall($name,$real=0)
    {
        $result=$this->PluginsHelper->uninstall($name,$real);
        $this->PluginsHelper->getPluginsConfigs($name,'',true);
        $this->PluginsHelper->getConfig($name,true);
        if($result['code'])
        {
            $this->success($result['msg'],$this->returnUrl);
        }
        $this->error($result['msg']);
    }

    //更改插件状态
    public function status($name,$status)
    {
        if($status==1)
        {
            $result=$this->PluginsHelper->enable($name);
        }else{
            $result=$this->PluginsHelper->disable($name);
        }
        $this->PluginsHelper->getPluginsConfigs($name,'',true);
        $this->PluginsHelper->getConfig($name,true);
        if($result['code'])
        {
            $this->success($result['msg'],$this->returnUrl);
        }
        $this->error($result['msg']);
    }

    //删除插件
    public function delete($name='')
    {
        $result=$this->PluginsHelper->delete($name);
        $this->PluginsHelper->getPluginsConfigs($name,'',true);
        $this->PluginsHelper->getConfig($name,true);
        if($result['code'])
        {
            $this->success($result['msg'],$this->returnUrl);
        }
        $this->error($result['msg']);
    }

    //设计插件
    public function design()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post();

            if (!$params['title']) $this->error('请填写插件名称');
            if (!$params['name'] || !preg_match('/^[a-zA-z]+$/U', $params['name'])) $this->error('请填写正确的英文插件标识');
            if (db('plugins')->where(['name' => $params['name']])->find()) $this->error('插件已存在, 请重新填写标识');
            if (!$params['author']) $this->error('请填写插件作者');
            if (!$params['version']) $this->error('请填写插件版本');
            if (!$params['description']) $this->error('请填写插件简介');

            $name = $params['name'];
            $plugin_content = file_get_contents(app_path('common/demo/plugin').'Plugin.php');
            $plugin_content = str_replace('app\common\demo\plugin',"plugins\\{$name}", $plugin_content);
            // 初始化插件目录
            copyDir(app_path('common/demo/plugin'), root_path("plugins/{$params['name']}"));

            file_put_contents(root_path("plugins/{$params['name']}").'Plugin.php', $plugin_content);

            $info = [
                'title' => $params['title'],
                'name' => $params['name'],
                'description' => $params['description'],
                'author' => $params['author'],
                'version' => $params['version'],
                'author_url' => $params['author_url'] ?: '',
                'status' => 0,
                'config' => [],
                'setting' => [
                    'tabs' => [],
                    'category' => [],
                    'relation' => [],
                    'commands' => [],
                ],
                'menu' => [
                    'is_nav' => 0,
                    'menu' => [
                        'menu_list' => [
                            [],
                        ]
                    ]
                ]
            ];

            file_put_contents(root_path("plugins/{$params['name']}").'info.php', '<?php'.PHP_EOL.'  return '.var_export($info, true).';');

            $this->success('初始化成功', $this->returnUrl);
        }

        return $this->fetch();
    }

    //导入插件
    public function import(): string
    {
        if ($this->request->isPost())
        {
            $file = $this->request->post('aw-file');
            if(!$file)
            {
                $this->error('请选择您需要导入的插件包');
            }
            $file =str_replace(request()->domain().'/','',$file);
            $file =str_replace(['\\','/'],DS,$file);
            $archive = new \ZipArchive();
            if($archive->open(public_path().$file)===TRUE)
            {
                $archive->extractTo(root_path('plugins'));
                $archive->close();
                unlink(public_path().$file);
                $this->success('导入成功');
            }
            unlink(public_path().$file);
            $this->error('导入失败');
        }
        return $this->formBuilder
            ->addFile('aw-file','导入插件','请选择导入插件')
            ->setFormUrl((string)url('import'))
            ->fetch();
    }

    //导出插件
    public function export($name=null)
    {

    }

    //更新插件
    public function upgrade($name=null)
    {
        $result = $this->PluginsHelper->upgrade($name);
        if($result['code'])
        {
            $this->success($result['msg'],'index');
        }
        $this->error($result['msg'],'index');
    }
}
