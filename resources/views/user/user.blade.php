@extends('layouts.mama')

@section('title')
    {{$title}}
@endsection

@section('header')
    @parent
    <p style="color: red;">This is Child header.</p>
@endsection



@section('content')
    <form action="">
        <table border="1px">
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
                    <td>{{$v->name}}</td>
                    <td>{{$v->age}}</td>
                    <td>{{$v->email}}</td>
                    <td>{{date('Y-m-d H:i:s',$v->atime)}}</td>
                </tr>
            @endforeach
        </table>
    </form>
@endsection


@section('footer')
    @parent
    <p style="color: red;">This is Child footer .</p>
@endsection