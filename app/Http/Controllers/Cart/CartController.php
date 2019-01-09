<?php

namespace App\Http\Controllers\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use App\Model\CartModel;

class CartController extends Controller
{
    //
    public function __construct()
    {

    }
    public function addcart(Request $request){
        $goods_id=$request->input('goods_id');
        $num=$request->input('num');
        $res=GoodsModel::where(['goods_id'=>$goods_id])->value('goods_store');
        if($res<=0){
            $response = [
                'errno' => 5001,
                'msg'   => '库存不足'
            ];
            return $response;
        }
        $where=[
            'goods_id'=>$goods_id,
            'uid'=>session()->get('uid')
        ];
        $rel=CartModel::where($where)->first();
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
            'goods_id'=>$goods_id,
            'goods_num'=>$num,
            'uid'=>session()->get('uid'),
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
    public function add($goods_id){
        $cart_goods = session()->get('cart_goods');
        //检测库存
        $where = ['goods_id'=>$goods_id];
        $store = GoodsModel::where($where)->value('goods_store');

        if($store<=0){
            echo '库存不足';
            exit;
        }
        //是否已在购物车中
        if(!empty($cart_goods)){
            if(in_array($goods_id,$cart_goods)){
                header('refresh:2;url=/cart/list');
                echo '已存在购物车中';
                exit;
            }
        }

        session()->push('cart_goods',$goods_id);

        //减库存

        $rs = GoodsModel::where(['goods_id'=>$goods_id])->decrement('goods_store');

        if($rs){
            header('refresh:2;url=/cart/list');
            echo '添加成功';
        }

    }
    public function list(Request $request){
        $uid = session()->get('uid');
        $where=[
            'uid'=>$uid
        ];
        $res=CartModel::where($where)->get();
        if(!$res){
            header('refresh:2;,url=/goodslist');
            echo "购物车为空";exit;
        }
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
    public function quit(){
        Session()->flush();
    }
}
