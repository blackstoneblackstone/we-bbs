<?php
namespace app\backend\extend;
use app\common\controller\Backend;
use app\common\library\helper\FileHelper;
use app\model\admin\AdminAuth;
use think\facade\Request;

class Curd extends Backend
{
    public function index()
    {
        $columns = [
            ['table' , '表名'],
            ['title', '名称'],
            ['status', '启用', 'status', '0',['0' => '否','1' => '是']],
            ['page', '分页' ,'select', '1', ['0' => '否','1' => '是']],
            ['button', '操作'],
        ];
        if ($this->request->param('_list'))
        {
            $list = $this->makeBuilder->tables();
            $result = [];
            foreach ($list as $key=> $val) {
                unset($val['fields']);
                $str = '<a class="btn btn-primary btn-sm aw-ajax-open" data-title="表配置" data-url="' . url('info', ['table' => $val['table']]) . '"><i class="fa fa-edit"></i> 表配置</a> ';
                $str .= '<a class="btn btn-success btn-sm aw-ajax-open" data-title="字段配置" data-url="' . url('field', ['table' => $val['table']]) . '"><i class="fa fa-edit"></i> 字段配置</a> ';
                $str .= '<a class="btn btn-danger btn-sm aw-ajax-get" data-title="生成代码" data-url="' . url('build', ['table' => $val['table']]) . '"><i class="fa fa-edit"></i> 生成代码</a> ';
                $val['button'] = $str;
                $val['table'] = $this->makeBuilder::getPrefix().$val['table'];
                $result[] = $val;
            }
            return [
                'total'        => count($result),
                'per_page'     => 1000,
                'current_page' => 1,
                'last_page'    => 1,
                'data'         => array_values($result),
            ];
        }
        return $this->tableBuilder
            ->setUniqueId('table')
            ->addColumns($columns)
            ->setPagination('false')
            ->fetch();
    }

    //自动构建
    public function build($table)
    {
        return json($this->makeBuilder->build($table));
    }

    //编辑表信息
    public function info($table)
    {
        if($this->request->isPost())
        {
            $data = $this->request->post();
            $data['top_button'] = implode(',',$data['top_button']);
            $data['right_button'] = implode(',',$data['right_button']);
            $this->makeBuilder->save($data);
            $this->success('配置成功');
        }

        $info = $this->makeBuilder->table($table);
        $result =AdminAuth::getMenu();
        return $this->formBuilder
            ->addHidden('table',$table)
            ->addSelect('menu_pid','生成菜单','',$result,$info['menu_pid'])
            ->addText('title','显示名称','',$info['title'])
            ->addCheckbox('top_button','顶部按钮','',['add'=>'添加','delete'=>'删除','export'=>'导出'],$info['top_button'])
            ->addCheckbox('right_button','顶部按钮','',['edit'=>'编辑','delete'=>'删除'],$info['right_button'])
            ->addRadio('page','启用分页','',[0=>'否',1=>'是'],$info['page'])
            //->addRadio('status','自动生成','',[0=>'否',1=>'是'],$info['status'])
            ->fetch();
    }

    public function field($table='')
    {
        $table = str_replace($this->makeBuilder::getPrefix(),'',$table);
        $columns = [
            ['field'  , '字段'],
            ['title', '名称'],
            ['is_search', '搜索字段','select','0',['0' => '否','1' => '是']],
            ['is_list', '列表字段','select','0',['0' => '否','1' => '是']],
            ['is_add', '添加字段','select','0',['0' => '否','1' => '是']],
            ['is_edit', '编辑字段','select','0',['0' => '否','1' => '是']],
            ['type', '表单类型','select','',config('app.fieldType')],
            ['button', '操作'],
        ];

        if ($this->request->param('_list'))
        {
            $table = $this->request->param('table');
            $list = array_values($this->makeBuilder->table($table)['fields']);
            foreach ($list as $k=>$v)
            {
                if(!isset($v['field']))
                {
                    continue;
                }
                $str = '<a class="btn btn-success btn-sm aw-ajax-open" data-title="字段配置" data-url="'.url('manager',['table'=>$table,'field'=>$v['field']]).'"><i class="fa fa-edit"></i> 字段配置</a> ';
                $v['button'] = $str;
                $list[$k] = $v;
            }
            return [
                'total'        => count($list),
                'per_page'     => 1000,
                'current_page' => 1,
                'last_page'    => 1,
                'data'         => $list,
            ];
        }

        return $this->tableBuilder
            ->setDataUrl(Request::baseUrl().'?_list=1&table='.$table)
            ->addColumns($columns)
            ->setPagination('false')
            ->fetch();
    }

    public function manager($table=null,$field=null,$type=null)
    {
        if($type)
        {
            $info = $this->makeBuilder->field($table,$field);
            $this->assign(['type'=>$type,'fieldInfo'=>$info]);
            return $this->fetch('field/type');
        }
        if($field)
        {
            $info = $this->makeBuilder->field($table,$field);
            $this->assign(['info'=>$info,'table'=>$table,'field'=>$field]);
            return $this->fetch('field/add');
        }
    }

    public function change($field=null)
    {
        if($this->request->isPost())
        {
            $data = $this->request->post();
            $saveData = [];
            $saveData['table'] = $data['table'];
            unset($data['table']);
            $data['default'] = $data['settings']['default'];
            $saveData['fields'][$data['field']] = $data;
            $this->makeBuilder->save($saveData);
            $this->success('配置成功');
        }
    }
}