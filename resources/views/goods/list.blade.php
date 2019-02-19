@extends('layouts.bst')

@section('title')
    {{$title}}
@endsection

@section('content')
    <input type="text" id="name"><input type="button" value="搜索" id="btn">
    <table>
        <tr>
            <td>ID</td>
            <td>商品名称</td>
            <td>商品库存</td>
            <td>操作</td>
        </tr>
        @foreach($list as $v)
            <tr>
                <td>{{$v->goods_id}}</td>
                <td>{{$v->goods_name}}</td>
                <td>{{$v->goods_store}}</td>
                <td><a href="/goodsdetail/{{$v->goods_id}}">详情</a></td>
            </tr>
        @endforeach
    </table>
        {{$list->links()}}
@endsection
@section('footer')
    @parent
    <script src="{{URL::asset('/js/goods/sousuo.js')}}"></script>
@endsection
