@extends('layouts.bst')

@section('title')
    {{$title}}
@endsection

@section('content')
        <h1 align="center" style="color:blue;">欢迎UID:{{$_COOKIE['uid']}}登陆</h1>
        <table class="table table-bordered">
            <tr>
                <td>ID</td>
                <td>名称</td>
                <td>年龄</td>
                <td>邮箱</td>
                <td>添加时间</td>
            </tr>
            @foreach($list as $v)
                <tr>
                    <td>{{$v->uid}}</td>
                    <td>{{$v->username}}</td>
                    <td>{{$v->age}}</td>
                    <td>{{$v->email}}</td>
                    <td>{{date('Y-m-d H:i:s',$v->atime)}}</td>
                </tr>
            @endforeach
        </table>
@endsection

