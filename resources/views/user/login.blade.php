@extends('layouts.mama')
@section('content')
    <form action="/test/userlogini" method="post">
        {{csrf_field()}}
        <font size="24px">登录</font>
        <div class="form-group">
            <label for="exampleInputUsername" >Acount</label>
            <input type="text" class="form-control" style="width:500px;" name="username" placeholder="账号">
        </div>
        <div class="form-group">
            <label for="exampleInputPwd">Password</label>
            <input type="password" class="form-control" style="width:500px;" name="pwd" placeholder="密码">
        </div>
        <button type="text" class="btn btn-primary" style="width:500px;">登录</button>
    </form>
    <h1><a style="color:red" href="/weixin/login">微信扫码登录</a></h1>
@endsection
