<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use DB;

class TestController extends Controller
{
    //注册
    public function register(){
        return view('user.register');
    }
    public function registerdo(Request $request){
        if(empty($request->input('username'))){
            echo "账号不能为空";die;
        }
        $where=[
            'username'=>$request->input('username')
        ];
        $rel=UserModel::where($where)->first();
        if($rel){
            echo "账号已注册";die;
        }
        if(empty($request->input('pwd'))){
            echo "密码不能为空";die;
        }
        if($request->input('pwd')!==$request->input('pwd1')){
            echo "密码不一致";die;
        }
        $data=[
            'username'=>$request->input('username'),
            'pwd'=>password_hash($request->input('pwd'),PASSWORD_BCRYPT),
            'age'=>$request->input('age'),
            'email'=>$request->input('email'),
            'atime'=>time()
        ];
        $uid=UserModel::insertGetId($data);
        if($uid){
            setcookie('uid',$uid,time()+86400,'/','leningshop.com',false,true);
            header("refresh:2;/test/list");
            echo "注册成功,正在跳转";
        }else{
            header('Location:/userregister');
            echo "注册失败";
        }
    }
    public function login(){
        return view('user.login');
    }
    public function logindo(Request $request){
        $username=$request->input('username');
        $pwd=$request->input('pwd');
        $where=[
            'username'=>$username
        ];
        $res=UserModel::where($where)->first();
        if($res){
            if(password_verify($pwd,$res->pwd)){
                $token = substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$res->uid,time()+86400,'/','leningshop.com',false,true);
                setcookie('token',$token,time()+86400,'/','leningshop.com',false,true);
                $request->session()->put('u_token',$token);
                header('refresh:1;/test/list');
                echo "登录成功,正在跳转";
            }else{
                header('refresh:1;/userlogin');
                echo "账号或密码错误";
            }
        }else{
            echo "账号不存在";
            header('refresh:1;/userlogin');
        }
    }
    //
    public function add(){
        $data=[
            'name'=>str_random(5),
            'age'=>20,
            'email'=>str_random(4)."@qq.com",
            'atime'=>time()
        ];
        $res=UserModel::insert($data);
        dump($res);
    }

    public function list(Request $request){
        if(empty($_COOKIE['uid'])){
            echo "请先登录";
            header('refresh:1;url=/userlogin');
            die;
        }else if($_COOKIE['token']!=$request->session()->get('u_token')){
            die('非法登录');
        }
        $res=UserModel::all();
        //dump($data);die;
        $data=[
            'title'=>'lening_shop',
            'list'=>$res
        ];
        return view('user.user',$data);
    }

    public function delete($id){
        $where=[
            'uid'=>$id
        ];
        $res=UserModel::where($where)->delete();
    }

	public function world1()
	{
		echo __METHOD__;
	}

	public function abc(){
	    dump($_POST);
	    dump($_GET);
    }

	public function hello2()
	{
		echo __METHOD__;
		header('Location:/world2');
	}

	public function world2()
	{
		echo __METHOD__;
	}

	public function md($m,$d)
	{
		echo 'm: '.$m;echo '<br>';
		echo 'd: '.$d;echo '<br>';
	}

	public function showName($name=null)
	{
		var_dump($name);
	}

	public function query1()
	{
		$list = DB::table('p_users')->get()->toArray();
		echo '<pre>';print_r($list);echo '</pre>';
	}

	public function query2()
	{
		$user = DB::table('p_users')->where('uid', 3)->first();
		echo '<pre>';print_r($user);echo '</pre>';echo '<hr>';
		$email = DB::table('p_users')->where('uid', 4)->value('email');
		var_dump($email);echo '<hr>';
		$info = DB::table('p_users')->pluck('age', 'name')->toArray();
		echo '<pre>';print_r($info);echo '</pre>';
	}
}
