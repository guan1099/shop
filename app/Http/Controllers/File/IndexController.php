<?php

namespace App\Http\Controllers\File;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    //
    public function Index(){
        return view('file.file');
    }
    public function upload(Request $request){
        $res=$request->file('pdf');
        $rel=$res->extension();
        if($rel!='pdf'){
            die('请上传PDF格式文件');
        }else{
            $res1=$res->storeAs(date('Ymd'),str_random('3').'pdf');
            if($res1){
                echo "上传成功";
            }else{
                die('请重新上传');
            }
        }
    }
}
