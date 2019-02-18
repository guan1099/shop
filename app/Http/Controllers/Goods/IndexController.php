<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;

class IndexController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }
//    //
    public function index($goods_id){
        $res=GoodsModel::where(['goods_id'=>$goods_id])->first();
        if(!$res){
            header('refresh:2;url=/goodslist');
            echo "没有此商品";
            exit;
        }
        $data=[
            'goods'=>$res
        ];
        return view('goods.detail',$data);
    }

}
