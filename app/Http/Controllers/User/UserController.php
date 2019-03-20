<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Model\UserModel;
use App\Model\GoodsModel;

class UserController extends Controller
{
    //use Searchable;
    public function goodslist(Request $request){
        $name=$request->input('goods_name');
        if(empty($name)){
            $res=GoodsModel::paginate(3);
        }else{
            $res=DB::table('p_goods')->where('goods_name','like','%'.$name.'%')->paginate(3);
        }
        //print_r($res);die;
        $data=[
            'title'=>'lening',
            'list'=>$res
        ];
        return view('goods.list',$data);
    }
    public function goodslist1(Request $request){
        $name=$request->input('name');
        $response = [
            'error' => 5001,
            'msg'   => $name
        ];
        return $response;
    }
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
            setcookie('uid',$uid,time()+86400,'/','',false,true);
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
                setcookie('uid',$res->uid,time()+86400,'/','',false,true);
                setcookie('token',$token,time()+86400,'/','',false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put('uid',$res->uid);
                header('refresh:1;/goodslist');
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
    public function userLogin(Request $request){
        $username=$_POST['username'];
        $pwd=$_POST['pwd'];
        $where=[
            'username'=>$username
        ];
        $res=UserModel::where($where)->first();
        if($res){
            if(password_verify($pwd,$res->pwd)){
                $token = substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$res->uid,time()+86400,'/','',false,true);
                setcookie('token',$token,time()+86400,'/','',false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put('uid',$res->uid);
                header('refresh:1;/goodslist');
                $arr=[
                    'error'=>0,
                    'msg'=>'ok'
                ];
                echo json_encode($arr);
            }else{
                $arr=[
                    'error'=>40003,
                    'msg'=>'no'
                ];
                echo json_encode($arr);
            }
        }else{
            $arr=[
                'error'=>50000,
                'msg'=>'no no'
            ];
            echo json_encode($arr);
            header('refresh:1;/userlogin');
        }
    }
}
