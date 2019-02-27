@extends('layouts.bst')

@section('content')
        <input id="text" type="hidden" value="{{$list}}" /><br />
        <input type="hidden" value="{{$number}}" id="number">
        <h2 align="center">扫码支付：</h2>
        <div id="qrcode" align="center"></div>
@endsection

@section('footer')
    @parent
    <script src="{{URL::asset('js/qrcode.js')}}"></script>
    <script>
        var number=$('#number').val();
        setInterval(function(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url     :   '/weixin/pay/order',
                type    :   'get',
                data    :   {number:number},
                success :   function(d){
                    if(d==1){
                        alert('支付成功');
                        location.href='/weixin/pay/pay';
                    }
                }
            });
        },1000);
        var qrcode = new QRCode("qrcode");

        function makeCode () {
            var elText = document.getElementById("text");
            console.log(elText);
            if (!elText.value) {
                alert("Input a text");
                elText.focus();
                return;
            }

            qrcode.makeCode(elText.value);
        }
        //qrcode.clear(); // 清除代码
        makeCode();
    </script>
@endsection

