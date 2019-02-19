$("#btn").click(function(e){
    e.preventDefault();
    var name = $("#name").val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/goodslist1',
        type    :   'get',
        data    :   {name:name},
        dataType:   'json',
        success :   function(d){
            if(d.error==5001){
                window.location.href="/goodslist?goods_name="+name;
            }
        }
    });
});