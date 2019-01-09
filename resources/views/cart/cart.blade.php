@extends('layouts.bst')

@section('title')
    {{$title}}
@endsection

@section('content')
    <h1 align="center" style="color:blue;">欢迎UID:{{$_COOKIE['uid']}}登陆</h1>
    <table class="table table-bordered">
        <tr>
            <td>ID</td>
            <td>商品名称</td>
            <td>商品数量</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        @foreach($list as $v)
            <tr>
                <td>{{$v['goods_id']}}</td>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['num']}}</td>
                <td>{{date('Y-m-d H:i:s',$v['utime'])}}</td>
                <td><a href="/cart/del/{{$v['goods_id']}}">删除</a></td>
            </tr>
        @endforeach
    </table>
@endsection

