<?php
// +----------------------------------------------------------------------
// | 短信服务
// +----------------------------------------------------------------------
// | 2016-12-02
// +----------------------------------------------------------------------
// | Author: lb
// +----------------------------------------------------------------------
namespace Tool\Utils;

use common\utils\UtilHttp;

class UtilSms
{

    /**
     * @param $url          string  请求地址
     * @param $userid       string  用户id
     * @param $account      string  账号
     * @param $password     string  密码
     * @param $mobile       string  电话号码
     * @param $content      string  短信内容
     * @return array
     */
    public static function send($url, $userid, $account, $password, $mobile, $content)
    {
        $res = ['status' => 'faild', 'msg' => '', 'success_count' => 0];

        if (!empty($url) && !empty($userid) && !empty($account) && !empty($password) && !empty($mobile) && !empty($content)) {
            $parm = ['action' => 'send', 'userid' => $userid, 'account' => $account, 'password' => $password, 'mobile' => $mobile, 'content' => $content, 'sendTime' => '', 'extno' => ''];

            $resdata = UtilHttp::curl_http_post($url, $parm);
            $resdata = simplexml_load_string($resdata);

            $res['status'] = strtolower($resdata->returnstatus);
            $res['msg'] = strtolower($resdata->message);
            $res['success_count'] = intval($resdata->successCounts);
        }
        return $res;
    }
}

?>