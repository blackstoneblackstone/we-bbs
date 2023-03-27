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
namespace app\mobile;
use app\common\controller\Frontend;
use app\common\library\helper\UploadHelper;
use app\model\Attach as AttachModel;
class Upload extends Frontend
{
    // 上传文件
    public function index()
    {
        $uploadObj = UploadHelper::instance();

        $uploadPath= $this->request->param('path','common');
        $access_key = $this->request->param('access_key',md5($this->user_id.time()));

        //上传初始化钩子
        hook('uploadInit',['uploadValidate'=>$uploadObj->uploadVal()]);
        $result = UploadHelper::instance()->setAccessKey($access_key)->setUploadPath($uploadPath)->setUploadType($this->request->param('upload_type','img'))->upload($this->user_id,'','');
        return json($result);
    }

    //删除附件
    public function remove_attach()
    {
        if($this->request->isPost())
        {
            $id = $this->request->post('id',0,'intval');
            $access_key = $this->request->post('access_key');
            if(!$id || !$access_key)
            {
                $this->error('删除内容错误');
            }
            //删除附件钩子
            hook('attachRemove',$this->request->post());

            if(AttachModel::removeAttach($id,$access_key))
            {
                $this->success('删除成功');
            }
            $this->error('删除失败');
        }
    }

    // 附件下载
    public function download()
    {
        if($this->request->isPost())
        {
            if (!$this->user_id) $this->error('请先登录',$this->returnUrl);
            $name = $this->request->post('name', '', 'trim');
            $name = authCode($name);
            $attach = db('attach')->where(['id' => $name])->find();
            $attach['path'] = str_replace('\\', DS, $attach['path']);
            if (!$attach || !file_exists($attach['path'])) $this->error('附件不存在或已被删除',$this->returnUrl);

            /*附件下载钩子*/
            hook('download_attach',['attach'=>$attach]);

            // 下载附件
            $file_size = filesize($attach['path']);
            $file = fopen($attach['path'], "rb");
            header("Content-type: application/octet-stream");  // 二进制流
            header("Accept-Ranges: bytes");
            header("Accept-Length: " . $file_size);
            $filename = $attach['name'] ?: md5($this->user_id . microtime()) . '.' . $attach['ext'];
            header("Content-Disposition: attachment; filename=" . $filename);  // 重置文件名
            echo fread($file, $file_size);  // 输出文件流
            fclose($file);
            exit;
        }

        if($this->user_id)
        {
            $this->error('请返回后重新点击下载',$this->returnUrl);
        }
        $this->error('请先登录',$this->returnUrl);
    }
}
