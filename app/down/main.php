<?php
class main extends AWS_CONTROLLER
{
    public function get_access_rule()
    {
        $rule_action['rule_type'] = "white";    // 黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
        $rule_action['actions'][] = 'download_file';
        return $rule_action;
    }

    public function download_file_action()
    {
        $fileName=trim($_POST['url']);
        $uid=intval($_POST['uid']);
        if (!$this->user_info['permission']['down_flie'])
        {
            if (!$uid || !$this->user_id)
            {
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请先登录')));
            }else{
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('你所在用户组没有权限下载附件')));
            }
        }

        $fileName = online_decrypt($fileName);
        $_tmp=parse_url($fileName);
        if(!$_tmp['host']){
            $fileName=http_type(). $_SERVER['HTTP_HOST'].$fileName;
        }
        //下载钩子
        run_hook('download_action',$_POST);
        H::ajax_json_output(AWS_APP::RSM($fileName, 1, null));
    }
}