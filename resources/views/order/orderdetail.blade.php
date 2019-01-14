@extends('layouts.bst')

@section('title')
    {{$title}}
@endsection

@section('content')
    <h1 align="center" style="color:blue;">订单详情列表</h1>

        @foreach($list as $v)
            <table class="table table-bordered">
                <tr>
                    <td>订单编号</td>
                    <td>商品名称</td>
                    <td>商品数量</td>
                    <td>商品金额</td>
                </tr>
                <tr>
                    <td>{{$v['order_number']}}</td>
                    <td>{{$v['goods_name']}}</td>
                    <td>{{$v['goods_num']}}</td>
                    <td><font color="red">￥{{$v['order_amount']}}</font></td>
                </tr>
            </table>
        @endforeach

    <a class="btn btn-default" href="/pay/alipay/test" role="button">支付</a>
@endsection

