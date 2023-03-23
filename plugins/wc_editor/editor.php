<?php
/**
 * 编辑器插件
 */
class editor extends AWS_CONTROLLER
{
    protected $plugin_info;
    protected $plugin_config;
    public $hooks=['editor'];
    public function __construct()
    {
        parent::__construct();
        $this->plugin_info = get_hook_info('editor');
        $this->plugin_config = get_hook_config('editor');
    }

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;        //此处方法可以自行实现
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        return true;
    }

    public function editor($param)
    {
        $type = $this->plugin_info['state'] == 1 ? $this->plugin_config['type']['value'] : '';
        PLUTPL::assign('path', str_replace('?', '', base_url()) . '/plugins/wc_editor/static');
        PLUTPL::assign('type', $type);
        PLUTPL::assign('user_info', $this->user_info);
        PLUTPL::assign('config', $this->plugin_config);
        PLUTPL::assign('param', $param);
        PLUTPL::output("editor/init");
    }


    public function upload_file()
    {
        //上传钩子
        run_hook('upload_action_hook',['method'=>'upload_file','type'=>'plugins_editor']);

        if (!$this->user_info['permission']['upload_attach'] and !($this->user_info['permission']['is_administortar'] OR $this->user_info['permission']['is_moderator']) and $_GET['cat'] != 'avatar')
        {
            H::ajax_json_output(['error' => 1, 'message' => '没有上传权限']);
        }

        if (get_hook_info('osd')['state'] == 1 && get_hook_config('osd')['group']['base']['config']['status']['value'] != 'no')
        {
            $ret = hook('osd', 'upload_files', ['type' => 'wangeditor', 'field' => 'upload', 'cat' => $_GET['cat']]);
            H::ajax_json_output($ret);exit;
        }

        $cat_array = explode(',',get_setting('upload_cat_type'));

        if(!in_array($_GET['cat'],$cat_array))
        {
            H::ajax_json_output(['error' => 1, 'msg' => '不允许的文件上传目录']);
        }

        $dir = str_replace(['/','\\'],'',$_GET['cat']);
        $_GET['cat'] = $dir;
        $upload_path = get_setting('upload_dir') . '/'.$dir.'/' .  gmdate('Ymd');
        AWS_APP::upload()->initialize(array(
            'allowed_types' => get_setting('allowed_upload_types'),
            'upload_path' => $upload_path,
            'max_size' => $this->plugin_config['fileMaxSize']['value'] * 1024 * 1024,
        ))->do_upload('upload');

        if (AWS_APP::upload()->get_error())
        {
            switch (AWS_APP::upload()->get_error())
            {
                default:
                    return array('msg'=>'错误代码: ' . AWS_APP::upload()->get_error(),'error' => 1);
                    break;

                case 'upload_invalid_filetype':
                    return array('msg'=>'文件类型无效','error' => 1);
                    break;

                case 'upload_invalid_filesize':
                    return array('msg'=>'文件尺寸过大, 最大允许尺寸为 ' . get_setting('upload_avatar_size_limit') .  ' KB','error' => 1);
                    break;
            }
        }

        if (! $upload_data = AWS_APP::upload()->data())
        {
            return array('msg'=>'上传失败, 请与管理员联系','error' => 1);
        }
        $upload_url = get_setting('upload_url') . '/'.$dir.'/' .  gmdate('Ymd');
        $file_url = $upload_url.'/'.$upload_data['file_name'];
        // 判断文件类型
        $data[$upload_data['orig_name']] = $file_url;
        $this->model('publish')->add_attach($_GET['cat'], $upload_data['orig_name'], $_GET['attach_access_key'], time(), $upload_data['file_name'],$upload_data['is_image']);
        H::ajax_json_output(['errno' => 0, 'data' => $data]);
    }

    /**
     * 上传视频
     */
    public function upload_video()
    {
        //上传钩子
        run_hook('upload_action_hook',['method'=>'upload_video','type'=>'plugins_editor']);

        if (!$this->user_info['permission']['upload_attach'] and !($this->user_info['permission']['is_administortar'] || $this->user_info['permission']['is_moderator']) and $_GET['cat'] != 'avatar')
        {
            H::ajax_json_output(['error' => 1, 'message' => '没有上传权限']);
        }
        if (get_hook_info('osd')['state'] === 1 && get_hook_config('osd')['group']['base']['config']['status']['value'] !== 'no')
        {
            $ret = hook('osd', 'upload_files', ['type' => 'wangeditor', 'field' => 'upload', 'cat' => $_GET['cat'],'method'=>'video']);
            H::ajax_json_output($ret);
        } else {
            $cat_array = explode(',',get_setting('upload_cat_type'));

            if(!in_array($_GET['cat'],$cat_array))
            {
                H::ajax_json_output(['error' => 1, 'msg' => '不允许的文件上传目录']);
            }

            $dir = str_replace(['/','\\'],'',$_GET['cat']);
            $_GET['cat'] = $dir;
            $upload_path = get_setting('upload_dir') . '/'.$dir.'/' .  gmdate('Ymd');
            AWS_APP::upload()->initialize(array(
                'allowed_types' => get_setting('allowed_upload_types'),
                'upload_path' => $upload_path,
                'max_size' => $this->plugin_config['fileMaxSize']['value'] * 1024 * 1024,
            ))->do_upload('upload');

            if (AWS_APP::upload()->get_error())
            {
                switch (AWS_APP::upload()->get_error())
                {
                    default:
                        return array('msg'=>'错误代码: ' . AWS_APP::upload()->get_error(),'error' => 1);
                        break;

                    case 'upload_invalid_filetype':
                        return array('msg'=>'文件类型无效','error' => 1);
                        break;

                    case 'upload_invalid_filesize':
                        return array('msg'=>'文件尺寸过大, 最大允许尺寸为 ' . get_setting('upload_avatar_size_limit') .  ' KB','error' => 1);
                        break;
                }
                exit();
            }

            if (! $upload_data = AWS_APP::upload()->data())
            {
                return array('msg'=>'上传失败, 请与管理员联系','error' => 1);
            }

            $upload_url = get_setting('upload_url') . '/'.$dir.'/' .  gmdate('Ymd');
            $file_url = $upload_url.'/'.$upload_data['file_name'];
            // 判断文件类型
            $data[$upload_data['orig_name']] = $file_url;
            $this->model('publish')->add_attach($_GET['cat'], $upload_data['orig_name'], $_GET['attach_access_key'], time(), $upload_data['file_name'],$upload_data['is_image']);
            H::ajax_json_output(['errno' => 0, 'data' => $data]);
        }
    }

    /**
     * markdown编辑器上传方法
     */
    public function upload_markdown_file()
    {
        //上传钩子
        run_hook('upload_action_hook',['method'=>'upload_markdown_file','type'=>'plugins_editor']);

        if (!$this->user_info['permission']['upload_attach'] && !($this->user_info['permission']['is_administortar'] || $this->user_info['permission']['is_moderator']) and $_GET['cat'] != 'avatar')
        {
            H::ajax_json_output(['error' => 1, 'message' => '没有上传权限']);
        }
        if (get_hook_info('osd')['state'] == 1 && get_hook_config('osd')['group']['base']['config']['status']['value'] != 'no')
        {
            $ret = hook('osd', 'upload_files', ['type' => 'markdown', 'field' => 'editormd-image-file', 'cat' => $_GET['cat']]);
            H::ajax_json_output($ret);
        } else {
            $cat_array = explode(',',get_setting('upload_cat_type'));
            if(!in_array($_GET['cat'],$cat_array))
            {
                H::ajax_json_output(['error' => 1, 'msg' => '不允许的文件上传目录']);
            }
            $dir = str_replace(['/','\\'],'',$_GET['cat']);
            $_GET['cat'] = $dir;
            $upload_path = get_setting('upload_dir') . '/'.$dir.'/' .  gmdate('Ymd');
            AWS_APP::upload()->initialize(array(
                'allowed_types' => get_setting('allowed_upload_types'),
                'upload_path' => $upload_path,
                'max_size' => $this->plugin_config['fileMaxSize']['value'] * 1024 * 1024,
            ))->do_upload('editormd-image-file');

            if (AWS_APP::upload()->get_error())
            {
                switch (AWS_APP::upload()->get_error())
                {
                    default:
                        return array('msg'=>'错误代码: ' . AWS_APP::upload()->get_error(),'error' => 1);
                        break;

                    case 'upload_invalid_filetype':
                        return array('msg'=>'文件类型无效','error' => 1);
                        break;

                    case 'upload_invalid_filesize':
                        return array('msg'=>'文件尺寸过大, 最大允许尺寸为 ' . get_setting('upload_avatar_size_limit') .  ' KB','error' => 1);
                        break;
                }
            }

            if (!$upload_data = AWS_APP::upload()->data())
            {
                return array('msg'=>'上传失败, 请与管理员联系','error' => 1);
            }

            $upload_url = get_setting('upload_url') . '/'.$dir.'/' .  gmdate('Ymd');
            $file_url = $upload_url.'/'.$upload_data['file_name'];
            $this->model('publish')->add_attach($_GET['cat'], $upload_data['orig_name'], $_GET['attach_access_key'], time(), $upload_data['file_name'],$upload_data['is_image']);
            H::ajax_json_output(['success' => 1, 'message ' => '', 'url' => $file_url]);
        }
    }
}