@extends('layouts.bst')

@section('content')
    <form action="/userregister" method="post">
        {{csrf_field()}}
        <div class="form-group">
            <label for="exampleInputUsername" >Acount</label>
            <input type="text" class="form-control" style="width:500px;" id="exampleInputPassword1" name="username" placeholder="账号">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail">Email</label>
            <input type="text" class="form-control"  style="width:500px;" id="exampleInputPassword1" name="email" placeholder="邮箱">
        </div>
        <div class="form-group">
            <label for="exampleInputAge">age</label>
            <input type="text" class="form-control"  style="width:500px;" id="exampleInputPassword1" name="age" placeholder="年龄">
        </div>
        <div class="form-group">
            <label for="exampleInputPwd">Password</label>
            <input type="password" class="form-control"  style="width:500px;" id="exampleInputPassword1" name="pwd" placeholder="密码">
        </div>
        <div class="form-group">
            <label for="exampleInputPwd1">确认密码</label>
            <input type="password" class="form-control"  style="width:500px;" id="exampleInputPassword2" name="pwd1" placeholder="密码">
        </div>
        <input class="btn btn-primary" type="submit" style="width:248px;" value="注册">
        <input class="btn btn-default" type="reset" style="width:248px;" value="重置">
    </form>
@endsection
