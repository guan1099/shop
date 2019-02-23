@extends('layouts.bst')
@section('content')
    <form action="">
        <div style="width:800px;height:500px;border:solid 1px black;margin: 0 auto" align="center">
            <tr>
                <td>{{$list['nickname']}}</td>
                <td><img src="{{$list['headimgurl']}}" alt=""></td>
            </tr>
            <input type="submit" value="发送" class="btn btn-default">
        </div>
    </form>
@endsection