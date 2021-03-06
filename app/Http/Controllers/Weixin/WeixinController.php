<?php

namespace App\Http\Controllers\Weixin;


use App\Model\UserModel;
use App\Model\WeixinMaterial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\WeixinUser;
use App\Model\WeixinMedia;
use GuzzleHttp;
use Illuminate\Support\Facades\Storage;
class WeixinController extends Controller
{
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    protected $redis_weixin_jsapi_ticket = 'str:weixin_access_ticket';     //微信 access_ticket
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
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
        $event = $xml->Event;                       //事件类型
        //var_dump($xml);echo '<hr>';
        $openid = $xml->FromUserName;               //用户openid
        $sub_time = $xml->CreateTime;               //扫码关注时间


        // 处理用户发送消息
        if(isset($xml->MsgType)){
            if($xml->MsgType=='text'){            //用户发送文本消息
                $msg = $xml->Content;
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'text',
                    'media_id'  => $xml->MediaId,
                    'format'    => $xml->Format,
                    'msg_id'    => $xml->MsgId,
                    'local_file_name'   => $msg
                ];
                $m_id = WeixinMedia::insertGetId($data);
            }elseif($xml->MsgType=='image'){       //用户发送图片信息
                //视业务需求是否需要下载保存图片
                if(1){  //下载图片素材
                    $file_name=$this->dlWxImg($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;
                    $data = [
                        'openid'    => $openid,
                        'add_time'  => time(),
                        'msg_type'  => 'image',
                        'media_id'  => $xml->MediaId,
                        'format'    => $xml->Format,
                        'msg_id'    => $xml->MsgId,
                        'local_file_name'   => $file_name
                    ];
                    $m_id = WeixinMedia::insertGetId($data);
                    echo $m_id;
                }

                exit;
            }elseif($xml->MsgType=='voice'){
                $file_name=$this->dlVoice($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'voice',
                    'media_id'  => $xml->MediaId,
                    'format'    => $xml->Format,
                    'msg_id'    => $xml->MsgId,
                    'local_file_name'   => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
                echo $m_id;
            }elseif($xml->MsgType=='video'){
                $file_name=$this->dlVideo($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'video',
                    'media_id'  => $xml->MediaId,
                    'format'    => $xml->Format,
                    'msg_id'    => $xml->MsgId,
                    'local_file_name'   => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
                echo $m_id;
            }elseif($xml->MsgType=='event') {
                if ($event == 'subscribe') {

                    echo 'openid: ' . $openid;
                    echo '</br>';
                    echo '$sub_time: ' . $sub_time;

                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);
                    echo '<pre>';
                    print_r($user_info);
                    echo '</pre>';

                    //保存用户信息
                    $u = WeixinUser::where(['openid' => $openid])->first();
                    //var_dump($u);die;
                    if ($u) {       //用户不存在
                        echo '用户已存在';
                    } else {
                        $user_data = [
                            'openid' => $openid,
                            'add_time' => time(),
                            'nickname' => $user_info['nickname'],
                            'sex' => $user_info['sex'],
                            'headimgurl' => $user_info['headimgurl'],
                            'subscribe_time' => $sub_time,
                        ];

                        $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                        var_dump($id);
                    }
                } else if ($event == 'CLICK') {
                    if ($xml->EventKey == 'kefu') {
//                $data=$this->getUserInfo($openid);
//                $nickname=$data['nickname'];
                        $this->keFu($openid, $xml->ToUserName);
                    }
                }
            }

        }

    }
    public function dlVoice($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        $h = $response->getHeaders();
        //print_r($h);die;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/voice/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            echo 1;
        }else{      //保存失败
            echo 2;
        }
        return $file_name;
    }
    public function dlVideo($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        $h = $response->getHeaders();
        //print_r($h);die;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/video/'.$file_name;
        //保存视频
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            echo 1;
        }else{      //保存失败
            echo 2;
        }
        return $file_name;
    }
    public function dlWxImg($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //echo $url;echo '</br>';

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
        $wx_image_path = 'wx/images/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            echo 1;
        }else{      //保存失败
            echo 2;
        }
        return $file_name;
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
    public function createMenu()
    {
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
                    "name"  => "项目",
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
    public function keFu($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. 'Hello , 现在时间'. date('Y-m-d H:i:s') .']]></Content></xml>';
        echo $xml_response;
    }
    public function refreshToken()
    {
        Redis::del($this->redis_weixin_access_token);
        echo $this->getWXAccessToken();
    }
    //群发
    public function textGroup(){
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->getWXAccessToken();
        //请求微信接口
        $client=new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'filter'=>[
                'is_to_all'=>true,
                'tag_id'=>2  //is_to_all为true可不填写
            ],
            'text'=>[
                'content'=>'你好！'
            ],
            'msgtype'=>'text'
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo "群发成功";
        }else{
            echo "群发失败，请重试";
            echo "<br/>";
        }
    }
    //文件素材列表
    public function textMaterial(){
        $url='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getWXAccessToken();
        $client=new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            "type"=>'image',
            "offset"=>0,
            "count"=>10
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
    }
    //添加永久 素材
    public function form(){
        return view('form.material');
    }
    public function material(Request $request)
    {
        //保存文件
        $img_file = $request->file('media');
        if(!empty($img_file)){
            //echo '<pre>';print_r($img_file);echo '</pre>';echo '<hr>';

            $img_origin_name = $img_file->getClientOriginalName();
            echo 'originName: '.$img_origin_name;echo '</br>';
            $file_ext = $img_file->getClientOriginalExtension();          //获取文件扩展名
            echo 'ext: '.$file_ext;echo '</br>';

            //重命名
            $new_file_name = str_random(15). '.'.$file_ext;
            echo 'new_file_name: '.$new_file_name;echo '</br>';

            //文件保存路径


            //保存文件
            $save_file_path = $request->media->storeAs('material',$new_file_name);       //返回保存成功之后的文件路径

            echo 'save_file_path: '.$save_file_path;echo '<hr>';

            //上传至微信永久素材
            $this->upMaterialTest($save_file_path);
        }

    }
    public function upMaterialTest($file_path)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($file_path, 'r')
                ],
            ]
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $d = json_decode($body,true);
        echo '<pre>';print_r($d);echo '</pre>';


    }
    public function keLiao(){
        $openid=$_GET['openid'];
        $arr=WeixinUser::where(['openid'=>$openid])->first()->toArray();
        $arr1=WeixinMedia::where(['openid'=>'$openid'])->OrderBy('add_time','des')->first()->toArray();
            $data=[
                'list'=>$arr,
                'list1'=>$arr1
            ];
            return view('kefu.keliao',$data);
    }
    public function keliaodo(){
        $openid=$_GET['openid'];
        $pos=$_GET['pos'];
        $msg=WeixinMedia::where(['openid'=>$openid])->where('id','>',$pos)->where(['msg_type'=>'text'])->OrderBy('add_time','des')->first();
        if($msg){
            $response = [
                'errno' => 0,
                'data'  => $msg->toArray()
            ];

        }else{
            $response = [
                'errno' => 50001,
                'msg'   => '服务器异常，请联系管理员'
            ];
        }

        die( json_encode($response));
    }
    public function text(){
        $text=$_GET['send_msg'];
        $openid=$_GET['openid'];
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();
        $client=new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            "touser"=>$openid,
            "msgtype"=>"text",
            "text"=>
                [
                    "content"=>$text
                ]
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo "发送成功";
        }else{
            echo "发送失败，请重试";
        }
    }
    public function login(){
        return view('kefu.login');
    }
    public function getCode(Request $request){
        $code=$_GET['code'];
        //2 用code换取access_token 请求接口
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe24f70961302b5a5&secret=0f121743ff20a3a454e4a12aeecef4be&code='.$code.'&grant_type=authorization_code';
        $token_json = file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
        echo '<hr>';
        echo '<pre>';print_r($token_arr);echo '</pre>';

        $access_token = $token_arr['access_token'];
        $openid = $token_arr['openid'];

        // 3 携带token  获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);
        $user_arr = json_decode($user_json,true);
        echo '<hr>';
        echo '<pre>';print_r($user_arr);echo '</pre>';
        $data=[
            'username'=>$user_arr['nickname']
        ];
        $res=UserModel::where($data)->first();
        if($res){
            $token = substr(md5(time().mt_rand(1,99999)),10,10);
            setcookie('uid',$res->uid,time()+86400,'/','',false,true);
            setcookie('token',$token,time()+86400,'/','',false,true);
            $request->session()->put('u_token',$token);
            $request->session()->put('uid',$res->uid);
            header('refresh:1;/goodslist');
            echo "登录成功,正在跳转";
        }else{
            $uid=UserModel::insertGetId($data);
            $data1=[
                'uid'=>$uid,
                'openid'=>$user_arr['openid'],
                'nickname'=>$user_arr['nickname'],
                'unionid'=>$user_arr['unionid'],
                'sex'=>$user_arr['sex'],
                'headimgurl'=>$user_arr['headimgurl'],
                'add_time'=>time()
            ];
            $rel=WeixinUser::insertGetId($data1);
            echo $rel;
            if(!empty($rel)){
                $token = substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$uid,time()+86400,'/','',false,true);
                setcookie('token',$token,time()+86400,'/','',false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put('uid',$uid);
                header('refresh:1;/goodslist');
                echo "登录成功,正在跳转";
            }
        }
    }
    public function wxJssdk(){
        $list=[
            'appid'     =>env('WEIXIN_APPID'),
            'timestamp' =>time(),
            'nonceStr'  =>str_random('10')
        ];
        $list['sign']=$this->getJssdkSign($list);
        $data=[
            'list'=>$list
        ];
        return view('kefu.jssdk',$data);
    }
    public function getJssdkSign($list){
        $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];     //当前调用 jsapi的 url
        $ticket=$this->getJsTicket();
        $str =  'jsapi_ticket='.$ticket.'&noncestr='.$list['nonceStr']. '&timestamp='. $list['timestamp']. '&url='.$current_url;
        $signature=sha1($str);
        //print_r($signature);die;
        return $signature;
    }
    public function getJsTicket(){
        $token=$this->getWXAccessToken();
        $ticket=Redis::get($this->redis_weixin_jsapi_ticket);
        if(!$ticket){
            $ticket_url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token."&type=jsapi";
            $ticket_info = file_get_contents($ticket_url);
            $ticket_arr = json_decode($ticket_info,true);
            if(isset($ticket_arr['ticket'])){
                $ticket = $ticket_arr['ticket'];
                Redis::set($this->redis_weixin_jsapi_ticket,$ticket);           //存
                Redis::setTimeout($this->redis_weixin_jsapi_ticket,3600);       //设置过期时间 3600s
            }
        }
        return $ticket;
    }
}
