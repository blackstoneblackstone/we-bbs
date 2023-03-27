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
namespace app\common\library\helper;
use app\model\Users;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailHelper
{
    /**
     * 获取邮件模板
     * @return false|string
     */
    public static function getTemplate($template)
    {
        $handle = fopen($template, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
        //通过filesize获得文件大小，将整个文件一下子读到一个字符串中
        $contents = fread($handle, filesize ($template));
        fclose($handle);
        return $contents;
    }

    /**
	 * 验证是否是邮箱
	 * @param $email
	 * @return bool
	 */
	public static function isEmail($email): bool
    {
		if (filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			return true;
		}
		return false;
	}

    /**
     * 发送邮件
     * 若未设置 $email_type 模板类型，需在$data中定义subject 和 message 信息，支持富文本
     * $data 中可设置attachments附件
     * @param mixed $email 发送邮件
     * @param mixed $subject
     * @param mixed $message
     * @param mixed $data
     * @return array
     */
    public static function sendEmail($email='', $subject='', $message='', $data=[]): array
    {
        $emailConfig = get_setting();
        $mail = new PHPMailer(true);

        if(!$emailConfig['email_enable'] || !$emailConfig['email_host'] || !$emailConfig['email_username'] || !$emailConfig['email_password'] || !$emailConfig['email_from'])
        {
            return ['code'=>0,'message'=>'邮件功能未启用或配置不完整'];
        }

        if(isset($email) && !self::isEmail($email))
        {
            return ['code'=>0,'message'=>'邮件地址不正确'];
        }

        //$email = is_array($email) ? $email : explode(',',$email);

        if (!$user_info = Users::checkUserExist($email)) $user_info = ['nick_name' => '用户'];

        $from_username = $data['form_username'] ?? '';
        if(!empty($data) && isset($data['subject']))
        {
            $subject = self::replaceEmailTemplate($data['subject'],$user_info['nick_name'],$from_username,'','');
            $message = self::replaceEmailTemplate($data['message'],$user_info['nick_name'],$from_username,'','');
        }
        $message = self::replaceEmailTemplate(self::getTemplate(root_path().'app/common/tpl/email.tpl'),$user_info['nick_name'],$from_username,$subject,$message);
        try {
            $mail->CharSet="utf-8";
            $mail->Encoding = "base64";
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                         //Send using SMTP
            $mail->Host       = $emailConfig['email_host'];                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $emailConfig['email_username'];                     //SMTP username
            $mail->Password   = $emailConfig['email_password'];                               //SMTP password
            $mail->SMTPSecure = $emailConfig['email_secure'] ? : PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = $emailConfig['email_port'] ?? 25;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->From        =  $emailConfig['email_from']??'';
            $mail->FromName    =  $emailConfig['email_show_name']??'';

            //添加附件
            if(isset($data['attachments']) && $data['attachments'])
            {
                $attachments = is_array($data['attachments']) ? $data['attachments'] : explode(',',$data['attachments']);
                foreach ($attachments as $key=>$val)
                {
                    $mail->addAttachment($val);
                }
            }

            //设置内容
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = strip_tags($subject);
            $mail->Body    = $message;
            $mail->send();
            $emailLogData = [
                'send_to'=>$email,
                'subject'=>$subject,
                'message'=>$message,
                'status'=>1,
                'error_message'=>'',
                'create_time'=>time()
            ];
            db('email_log')->insert($emailLogData);
            return ['code'=>1,'message'=>'邮件发送成功'];
        } catch (Exception $e) {
            $emailLogData = [
                'send_to'=>$email,
                'subject'=>$subject,
                'message'=>$message,
                'status'=>0,
                'error_message'=>$mail->ErrorInfo,
                'create_time'=>time()
            ];
            db('email_log')->insert($emailLogData);
            return ['code'=>0,'message'=>'邮件发送失败,错误信息：'.$mail->ErrorInfo];
        }
    }

    /**
     * 解析邮件模板
     * @param $subject
     * @param $message
     * @param $user_name
     * @param $form_username
     * @return array
     */
    public static function parseTemplate($subject,$message,$user_name,$form_username): array
    {
        $subject = self::replaceEmailTemplate($subject,$user_name,$form_username,'','');
        $message = self::replaceEmailTemplate($message,$user_name,$form_username,'','');
        $template = self::replaceEmailTemplate(self::getTemplate(root_path().'app/common/tpl/email.tpl'),$user_name,$form_username,$subject,$message);
        return ['subject'=>$subject,'message'=>$template];
    }

    /**
     * 替换模板内容
     * @param $template
     * @param $user_name
     * @param $form_username
     * @param $subject
     * @param $message
     * @return array|string|string[]
     */
    public static function replaceEmailTemplate($template,$user_name,$form_username, $subject, $message)
    {
        return str_replace(['[#site_name#]','[#subject#]','[#message#]','[#time#]','[#user_name#]','[#from_username#]','[#site_url]'],[get_setting('site_name'),strip_tags($subject),$message,formatTime(time()),$user_name,$form_username,request()->domain()],$template);
    }
}