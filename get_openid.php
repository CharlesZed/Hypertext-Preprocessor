<?php
/**
 * Created by PhpStorm.
 * User: CharlesZed
 * Date: 2017/6/13
 * Time: 10:41
 */

namespace Addons\Publics\Controller;
use Think\Controller;
/**
 *
 */
class WxgroupController extends Controller{

    function code_group(){
//        dump($_REQUEST);die;
//            getWeixinUserInfo()
//            $weixin = D ( 'Weixin' );
//            $data = $weixin->getData ();
//            $opid = get_openid();
//            dump($opid);die;
        //判断是在微信里面打开
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == true) {

            //配置参数的数组
            $CONF =  array(
                '__APPID__' =>'wx6a10b11b66fffa12',
                '__SERECT__' =>'28a8722329bc9851e934b2c422aa78b2',
                '__CALL_URL__' =>'http://hzzk.newltd.cn/index.php?s=/addon/Publics/Wxgroup/code_group.html' //当前页地址
            );

            //没有传递code的情况下，先登录一下
            if(!isset($_GET['code']) || empty($_GET['code'])){

                $getCodeUrl  =  "https://open.weixin.qq.com/connect/oauth2/authorize".
                    "?appid=" . $CONF['__APPID__'] .
                    "&redirect_uri=" . $CONF['__CALL_URL__']  .
                    "&response_type=code".
                    "&scope=snsapi_base". #!!!scope设置为snsapi_base !!!
                    "&state=1";

                //跳转微信获取code值,去登陆
                header('Location:' . $getCodeUrl);
                exit;
            }

            $code         =  trim($_GET['code']);
            //使用code，拼凑链接获取用户openid
            $getTokenUrl    =  "https://api.weixin.qq.com/sns/oauth2/access_token".
                "?appid={$CONF['__APPID__']}".
                "&secret={$CONF['__SERECT__']}".
                "&code={$code}".
                "&grant_type=authorization_code";

            $rs = curlGet($getTokenUrl);
        }
        $rs1 = json_decode($rs);
        $rs1 = $this->object2array($rs1);
        $opcheck = M('QrOpid')->where(array('wechat_openid' => $rs1['openid']))->find();
        if ($opcheck){
            redirect('http://hzzk.newltd.cn/index.php?s=/addon/Publics/Wxgroup/code_group_false.html');
            die;
        }

        session('opid',$rs1['openid']);

        $opidct=M('QrOpid')->order('id desc')->find() ;
        $opidct=(int)ceil($opidct['id']/100);
        $qrmd = M('QrGroup')->where(array('id'=>$opidct))->field('qr_name')->find();
//            $qrmd=$qrmd['name'];
//            dump($qrmd['name']);die;
        $this->assign('qrcodename',$qrmd['qr_name']);
        $this->display('code_group');
    }

    function code_group_false(){
        $this->display('code_group_false');
    }
    function object2array($object){
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }
    function save_opid(){
        $opid = session('opid');
        $m = array(
            'id' => '',
            'wechat_openid' => $opid
        );
        $result = M('QrOpid')->add($m);
    }

}