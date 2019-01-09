@extends('layouts.bst')
@section('content')
    <form action="/userlogin" method="post">
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
@endsection
