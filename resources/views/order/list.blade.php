@extends('layouts.bst')

@section('title')
    {{$title}}
@endsection

@section('content')
    <h1 align="center" style="color:blue;">订单列表</h1>
    <table class="table table-bordered">
        <tr>
            <td>ID</td>
            <td>订单编号</td>
            <td>添加时间</td>
            <td>订单金额</td>
            <td>订单状态</td>
            <td>操作</td>
        </tr>
        @foreach($list as $v)
            <tr>
                <td>{{$v['order_id']}}</td>
                <td>{{$v['order_number']}}</td>
                <td>{{date('Y-m-d H:i:s',$v['atime'])}}</td>
                <td><font color="red">￥{{$v['order_amount']}}</font></td>
                <td>
                    <?php if($v['order_status']==1){ ?>
                        {{$v['order_status']="未支付,未确认,未发货"}}
                    <?php }else if($v['order_status']==2){ ?>
                        {{$v['order_status']="订单已取消"}}
                    <?php } ?>
                </td>
                <td><a href="/order/order/{{$v['order_number']}}"> 订单详情 </a>||<a href="/order/orderdel/{{$v['order_number']}}"> 删除 </a></td>
            </tr>
        @endforeach
    </table>
@endsection

