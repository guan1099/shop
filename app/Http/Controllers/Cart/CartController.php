<?php

namespace App\Http\Controllers\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use App\Model\CartModel;

class CartController extends Controller
{
    public $uid;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->uid = session()->get('uid');
            return $next($request);
        });
    }
    //添加购物车
    public function addcart(Request $request){
        $goods_id=$request->input('goods_id');
        $num=$request->input('num');
        $res=GoodsModel::where(['goods_id'=>$goods_id])->value('goods_store');
        $re=GoodsModel::where(['goods_id'=>$goods_id])->value('goods_name');
        if($res<=0){
            $response = [
                'error' => 5001,
                'msg'   => '库存不足'
            ];
            return $response;
        }else if($res<$num){
            $response = [
                'error' => 5003,
                'msg'   => '购买数量超过库存数量'
            ];
            return $response;
        }
        $where=[
            'goods_id'=>$goods_id,
            'uid'=>$this->uid
        ];
        $rel=CartModel::where($where)->first();
        if(($rel['goods_num']+$num)>$res){
            $response = [
                'error' => 5004,
                'msg'   => '购物车数量大于库存数量'
            ];
            return $response;
        }
        if($rel){
            $update=[
                'goods_num'=>$rel['goods_num']+$num,
                'atime'=>time()
            ];
            $rel1=CartModel::where($where)->update($update);
            if($rel1){
                $response = [
                    'error' => 0,
                    'msg'   => '已存在购物车，添加成功'
                ];
                return $response;
            }
        }
        $data=[
            'goods_name'=>$re,
            'goods_id'=>$goods_id,
            'goods_num'=>$num,
            'uid'=>$this->uid,
            'atime'=>time()
        ];
        $cartId=CartModel::insertGetId($data);
        if($cartId){
            $response = [
                'error' => 0,
                'msg'   => '添加成功'
            ];
            return $response;
        }else{
            $response = [
                'error' => 5002,
                'msg'   => '添加失败，请重新添加'
            ];
            return $response;
        }
    }
    //购物车列表
    public function list(Request $request){
        $uid = $this->uid;
        $where=[
            'uid'=>$uid
        ];
        $res=CartModel::where($where)->get()->toArray();
        if(empty($res)){
            header('refresh:2;url=/goodslist');
            echo "购物车为空";exit;
        }
        $list=[];
        foreach($res as $k=>$v){
            $cartInfo=GoodsModel::where(['goods_id'=>$v['goods_id']])->first();
            $cartInfo['num']=$v['goods_num'];
            $cartInfo['utime']=$v['atime'];
            $list[]=$cartInfo;
        }
        $data=[
            'title'=>'lening',
            'list'=>$list
        ];
        return view('cart.cart',$data);
    }
    //购物车商品删除
    public function del($goods_id){
        if(empty($goods_id)){
            header('refresh:2;url=/cart/list');
            echo "请选择要删除的商品";exit;
        }
        $where=[
            'goods_id'=>$goods_id
        ];
        $res=CartModel::where($where)->first();
        if(!$res){
            echo "要删除的商品不存在";exit;
        }
        $upwhere=[
            'goods_id'=>$goods_id
        ];
        $rel=CartModel::where($upwhere)->delete();
        if($rel){
            header('refresh:2;url=/cart/list');
            echo "删除成功";
        }else{
            header('refresh:2;url=/cart/list');
            echo "删除失败";
        }
    }
}
