<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\Model\GoodsModel;
use App\Model\CartModel;
use App\Model\OrderDetailModel;
class OrderController extends Controller
{
    //订单生成
    public function addorder(Request $request){
        $res=CartModel::where(['uid'=>session()->get('uid')])->get();
        if(!$res){
            header('refresh:2;url=/goodslist');
            echo "购物车为空";die;
        }
        $order_number=OrderModel::generateOrderSN();
        $goods_amount=0;
        foreach($res as $k=>$v){
            $res1=GoodsModel::where(['goods_id'=>$v['goods_id']])->first();
            $goods_money=$res1['goods_price']*$v['goods_num'];
            $goods_amount+=$res1['goods_price']*$v['goods_num'];
            GoodsModel::where(['goods_id'=>$v['goods_id']])->update(['goods_store'=>$res1['goods_store']-$v['goods_num']]);
            $data=[
                'goods_num'=>$v['goods_num'],
                'goods_name'=>$v['goods_name'],
                'uid'=>session()->get('uid'),
                'order_amount'=>$goods_money,
                'order_number'=>$order_number,
                'atime'=>time()
            ];
            $res1=OrderDetailModel::insert($data);
        }
        $data2=[
            'order_amount'=>$goods_amount,
            'order_number'=>$order_number,
            'uid'=>session()->get('uid'),
            'atime'=>time()
        ];
        $res2=OrderModel::insert($data2);
        if($res2){
            header('refresh:2;url=/order/orderlist');
            echo "添加成功";
            CartModel::where(['uid'=>session()->get('uid')])->delete();
        }else{
            die("添加失败");
        }
    }
    //订单列表
    public function orderlist(){
        $res=OrderModel::where(['uid'=>session()->get('uid')])->get();
        $data=[
            'title'=>'list',
            'list'=>$res
        ];
        return view('order.list',$data);
    }
    //订单删除
    public function orderdel($order_number){
        $where=[
            'order_number'=>$order_number
        ];
        $arr=OrderDetailModel::where($where)->get()->toArray();
        if(!$arr){
            header('refresh:1;url=/order/orderlist');
            echo "订单不存在";exit;
        }
        foreach($arr as $k=>$v){
            $arr1=GoodsModel::where(['goods_name'=>$v['goods_name']])->first();
            $data=[
                'goods_store'=>$arr1['goods_store']+$v['goods_num']
            ];
            $res2=GoodsModel::where(['goods_name'=>$v['goods_name']])->update($data);
        }
        $res=OrderModel::where($where)->update(['order_status'=>2]);
        $res1=OrderDetailModel::where($where)->delete();
        if($res1){
            header('refresh:1;url=/order/orderlist');
            echo "删除成功";
        }else{
            header('refresh:1;url=/order/orderlist');
            echo "删除失败";exit;
        }
    }
    //订单详情
    public function order($order_number){
        if(empty($order_number)){
            header('refresh:1;url=/order/orderlist');
            echo "订单号不存在";exit;
        }

        $where=[
            'order_number'=>$order_number
        ];
        $order_id=OrderModel::where($where)->value('order_id');
        $res=OrderDetailModel::where($where)->get()->toArray();
        if(empty($res)){
            header('refresh:1;url=/order/orderlist');
            echo "订单不存在";exit;
        }
        $data=[
            'title'=>'详情',
            'list'=>$res,
            'order_id'=>$order_id
        ];
        return view('order.orderdetail',$data);
        print_r($res);
    }
    //订单支付
    public function orderpay($order_id){
        echo "ID:$order_id"."支付成功";
    }
}
