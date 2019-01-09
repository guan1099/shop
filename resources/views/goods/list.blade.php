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
            <td>商品库存</td>
            <td>添加时间</td>
        </tr>
        @foreach($list as $v)
            <tr>
                <td>{{$v->goods_id}}</td>
                <td>{{$v->goods_name}}</td>
                <td>{{$v->goods_store}}</td>
                <td>{{date('Y-m-d H:i:s',$v->atime)}}</td>
            </tr>
        @endforeach
    </table>
@endsection

