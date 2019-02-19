<?php

namespace App\Http\Controllers\Weixin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\WeixinUser;
use GuzzleHttp;

class WeixinController extends Controller
{
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    //
    public function validToken1()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        echo $_GET['echostr'];
    }

    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");
        //解析XML
        $xml = simplexml_load_string($data);        //将 xml字符串 转换成对象

        $event = $xml->Event;                       //事件类型
        //var_dump($xml);echo '<hr>';
        $openid = $xml->FromUserName;               //用户openid
        $sub_time = $xml->CreateTime;               //扫码关注时间
        if($event=='subscribe'){

            echo 'openid: '.$openid;echo '</br>';
            echo '$sub_time: ' . $sub_time;

            //获取用户信息
            $user_info = $this->getUserInfo($openid);
            echo '<pre>';print_r($user_info);echo '</pre>';

            //保存用户信息
            $u = WeixinUser::where(['openid'=>$openid])->first();
            //var_dump($u);die;
            if($u){       //用户不存在
                echo '用户已存在';
            }else{
                $user_data = [
                    'openid'            => $openid,
                    'add_time'          => time(),
                    'nickname'          => $user_info['nickname'],
                    'sex'               => $user_info['sex'],
                    'headimgurl'        => $user_info['headimgurl'],
                    'subscribe_time'    => $sub_time,
                ];

                $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                var_dump($id);
            }
        }else if($event=='CLICK'){
            if($xml->EventKey=='kefu'){
//                $data=$this->getUserInfo($openid);
//                $nickname=$data['nickname'];
                $this->kefu($openid,$xml->ToUserName);
            }
        }
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }
    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
        //$openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }
    public function createMenu(){
        //获取token 拼接接口
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getWXAccessToken();
        //2 请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data = [
            "button"    => [
                [
                    "type"  => "click",      // view类型 跳转指定 URL
                    "name"  => "客服",
                    "key"   => "kefu"
                ],
                [
                    "name"=>"菜单",
                    "sub_button"=>[
                       [
                           "type"=>"view",
                           "name"=>"搜索",
                           "url"=>"http://www.soso.com/"
                        ],
                        [
                           "type"=>"view",
                            "name"=>"QQ音乐",
                            "url"=>"http://www.qqmusic.com/"
                        ]
                    ],

                ],
                [
                    "type"  => "view",      // view类型 跳转指定 URL
                    "name"  => "我的项目",
                    "url"   => "https://qi.tactshan.com"
                ]

            ]
        ];

        $r=$client->request('POST', $url, [
            'body' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);

        // 3 解析微信接口返回信息

        $response_arr = json_decode($r->getBody(),true);
        //echo '<pre>';print_r($response_arr);echo '</pre>';

        if($response_arr['errcode'] == 0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";echo '</br>';
            echo $response_arr['errmsg'];

        }
    }
    public function kefu($openid,$from,$nickname)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. 'Hello , 现在时间'. date('Y-m-d H:i:s') .']]></Content></xml>';
        echo $xml_response;
    }
}
