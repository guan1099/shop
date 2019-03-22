<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    //
    public function index(Request $request){
        $url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'';
        $data=[
            'is_login'=>$request->get('is_login'),
            'url'=>$url
        ];
        return view('index.index',$data);
    }
    public function curl(){
        //print_r($data);
        $url='http://zi.tactshan.com/user/login';
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);          //post传输数据
        curl_setopt($ch, CURLOPT_URL, $url);              //设置抓取的url
        curl_setopt($ch, CURLOPT_POSTFIELDS,['username'=>$_POST['username'],'pwd'=>$_POST['pwd']]);       //用post方法传送参数
        curl_setopt($ch, CURLOPT_HEADER,0);         //设置头文件的信息作为数据了输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置获取的信息以文件流的形式返回，而不是直接输出
        $re=curl_exec($ch);
        $a=json_decode($re,true);
        return $a;
    }
}
