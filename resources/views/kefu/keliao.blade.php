@extends('layouts.bst')
@section('content')
    <div style="width:800px;height:500px;border:solid 1px black;margin: 0 auto" align="center" id="chat_div">
        <tr>
            <td>{{$list['nickname']}}</td>
            <td><img src="{{$list['headimgurl']}}" alt=""></td>
        </tr>
    </div>
    <form action="">
        <input type="hidden" value="{{$list['openid']}}" id="hidden">
        <input type="hidden" value="1" id="msg_pos">
        <div style="width:800px;height:100px;border:solid 0px black;margin: 0 auto" align="right">
            <input type="text" style="width:700px;height:40px;" id="text">
            <input type="button" value="发送" class="btn btn-default" id="btn">
        </div>
    </form>
@endsection
@section('footer')
    @parent
    <script>
        var openid=$('#hidden').val();
        setInterval(function(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url     :   '/weixin/kefudo?openid=' + openid + '&pos=' +$("#msg_pos").val(),
                type    :   'get',
                dataType:   'json',
                success :   function(d){
                    if(d.errno==0){     //服务器响应正常
                        //数据填充
                        var msg_str = '<div><h5>'+d.data.openid+':'+d.data.local_file_name+'</h5></div></td>';
                        $("#chat_div").append(msg_str);
                        $("#msg_pos").val(d.data.id)
                    }else{

                    }
                    //console.log(d);
                }
            });
        },5000);
        $("#btn").click(function(e){
            e.preventDefault();
            var send_msg = $("#text").val().trim();
            var msg_str = '<p style="color: red"> 客服：'+send_msg+'</p>';
            $("#chat_div").append(msg_str);
            $("#text").val("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url     :   '/weixin/text',
                type    :   'get',
                data    :   {send_msg:send_msg,openid:openid},
                success :   function(d){
                    console.log(d);
                }
            });
        });
    </script>
@endsection
