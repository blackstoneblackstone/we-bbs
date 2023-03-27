<?php

namespace app\backend\admin;
use app\common\controller\Backend;
use app\common\library\helper\TemplateHelper;
use think\App;
use think\facade\Request;

class Theme extends Backend
{
    protected $TemplateHelper;
    protected $table = 'theme';
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->TemplateHelper = TemplateHelper::instance();
    }

    public function index()
    {
        if ($this->request->param('_list')) {
            // 获取模板列表
            $list = $this->TemplateHelper->getTemplatesList();
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
            ['title', '模板名称'],
            ['name','模板标识'],
            ['description','备注'],
            ['author', '作者'],
            ['version', '版本'],
            ['button', '操作', 'text']
        ];

        return $this->tableBuilder
            ->addColumns($columns)
            ->setPageTips('1、模板包含pc,手机端,微信端，手机端模板对应模板目录为mobile目录，微信端模板对应模板目录为wechat目录<br>
2、创建模板时可以更改部分模板，其他不存在的模板会调用系统默认模板<br>
3、模板可自定义配置文件,可直接在模板中使用{$theme_config.配置字段}或get_theme_setting进行调用')
            ->setUniqueId('name')
            ->addTopButtons(['delete',
                'import'=>[
                    'title'   => '导入模板',
                    'class'   => 'btn btn-primary aw-ajax-open',
                    'href'    => '',
                    'url'    => (string)url('import'),
                ],])
            ->setEditUrl((string)url('config', ['name' => '__id__']))
            ->setPagination('false')
            ->setParentIdField('pid')
            ->fetch();
    }

    //模板配置
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

            $config = $this->TemplateHelper->config($name,true);

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

            //写入模板配置备份文件
            $this->TemplateHelper->setTemplatesConfig($name,$config);
            $result=db('theme')->where('name',$name)->update(['config'=>json_encode($config,JSON_UNESCAPED_UNICODE)]);
            $this->TemplateHelper->getTemplatesConfigs($name,'',true);
            $this->TemplateHelper->getConfig($name,true);
            if ($result) {
                $this->success('修改成功');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }
        $config = $this->TemplateHelper->config($name,true);
        if(!$config)
        {
            $this->error('该模板无需配置');
        }

        $file = public_path('templates') .$name.DS. 'config.html';
        if (file_exists($file)) {
            $this->assign([
                'config' => $config
            ]);
            return $this->fetch($file);
        }

        try {
            // 获取字段数据
            $columns = $this->TemplateHelper->makeAddColumns($config);
            // 判断是否分组
            $group = $this->TemplateHelper->checkConfigGroup($config);
            // 构建页面
            $this->formBuilder->setFormUrl((string)url('config'))->addHidden('name', $name);
            $group ? $this->formBuilder->addGroup($columns) : $this->formBuilder->addFormItems($columns);
            return $this->formBuilder->fetch();
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }

    //安装模板
    public function install($name)
    {
        $result = $this->TemplateHelper->install($name);
        $this->TemplateHelper->getTemplatesConfigs($name,'',true);
        $this->TemplateHelper->getConfig($name,true);
        if($result['code'])
        {
            $this->success($result['msg'],$this->returnUrl);
        }
        $this->error($result['msg'],$this->returnUrl);

    }

    //卸载模板
    public function uninstall($name,$real=0)
    {
        $result=$this->TemplateHelper->uninstall($name,$real);
        $this->TemplateHelper->getTemplatesConfigs($name,'',true);
        $this->TemplateHelper->getConfig($name,true);
        if($result['code'])
        {
            $this->success($result['msg'],$this->returnUrl);
        }
        $this->error($result['msg'],$this->returnUrl);
    }

    public function import(): string
    {
        if ($this->request->isPost())
        {
            $file = $this->request->post('aw-file');
            $file =str_replace(request()->domain().'/','',$file);
            $file =str_replace(['\\','/'],DS,$file);
            $archive = new \ZipArchive();
            if($archive->open(public_path().$file)===TRUE)
            {
                $archive->extractTo(public_path('templates'));
                $archive->close();
                unlink(public_path().$file);
                $this->success('导入成功');
            }
            unlink(public_path().$file);
            $this->error('导入失败');
        }
        return $this->formBuilder
            ->addFile('aw-file','导入模板','请选择导入模板,模板必须为zip文件，模板为templates下对应模板的压缩文件，如default模板,压缩文件夹包含default目录，压缩应为default.zip')
            ->setFormUrl((string)url('import'))
            ->fetch();
    }

    public function delete()
    {
        if ($this->request->isPost())
        {
            $id= $this->request->post('id');
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);

                if(db('theme')->whereIn('name',$ids)->delete())
                {
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }
                return json(['error' => 1, 'msg' => '删除失败']);
            }

            if(db('theme')->where('name',$id)->delete())
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return json(['error' => 1, 'msg' => '删除失败']);
        }
    }

    //同步模板配置
    public function upgrade($name)
    {
        $result = $this->TemplateHelper->upgrade($name);
        $this->TemplateHelper->getTemplatesConfigs($name,'',true);
        $this->TemplateHelper->getConfig($name,true);
        if($result['code'])
        {
            $this->success($result['msg'],$this->returnUrl);
        }
        $this->error($result['msg'],$this->returnUrl);
    }
}
