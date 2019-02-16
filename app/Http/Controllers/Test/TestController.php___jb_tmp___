<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use DB;

class TestController extends Controller
{
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
