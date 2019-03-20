<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use GuzzleHttp\Client;


class TestController extends Controller
{
    public function test(){
        //$arr=$_POST;
        //print_r($arr);
        $arr=[
            'error'=>0,
            'msg'=>'ok'
        ];
        echo json_encode($arr);
    }
}
