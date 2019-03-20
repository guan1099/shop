<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use GuzzleHttp\Client;


class TestController extends Controller
{
    public function test(Request $request){
        $arr=$_POST;
        print_r($arr);
        die;
        $arr=[
            'error'=>0,
            'msg'=>'ok'
        ];
        echo json_encode($arr);
    }
}
