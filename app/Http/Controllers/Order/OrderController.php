<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\Model\GoodsModel;
use App\Model\CartModel;
class OrderController extends Controller
{
    //
    public function addorder(Request $request){
        $res=CartModel::where(['uid'=>session()->get('uid')])->get();
        if(!$res){
            header('refresh:2;url=/goodslist');
            echo "购物车为空";die;
        }
        $goods_amount=0;
        foreach($res as $k=>$v){
            $res1=GoodsModel::where(['goods_id'=>$v['goods_id']])->first();
            $goods_amount+=$res1['goods_price']*$v['goods_num'];
            GoodsModel::where(['goods_id'=>$v['goods_id']])->update(['goods_store'=>$res1['goods_store']-$v['goods_num']]);
        }
        $order_number=OrderModel::generateOrderSN();
        $data=[
            'uid'=>session()->get('uid'),
            'order_amount'=>$goods_amount,
            'order_number'=>$order_number,
            'atime'=>time()
        ];
        $res2=OrderModel::insert($data);
        if($res2){
            echo "添加成功";
        }else{
            die("添加失败");
        }
    }
}
