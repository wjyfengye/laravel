<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="/admin/js/jquery-3.2.1.min.js"></script>
</head>
<body>
    <form action="" method='post'>
        <table >
                <h3>微信扫码</h3>
               
                <div id='img'>
                    <img src="{{$imgUrl}}" style="width:150px;" > 
                </div>    
        </table>
    </form>
</body>
</html>
<script>

    var t = setInterval("check()",2000);
    var name="{{$name}}";

    function check(){
        $.ajax({
            url:"{{url('checkWechatLogin')}}",
            data:{name:name},
            dataType:"json",
            success:function(res){
                
                if(res.msg==1){
                    //关闭定时器
                    clearInterval(t);
                    alert(res.font);
                    //跳转地址
                    // location.href = "{{url('list')}}";
                }
              
            }
        })
    }
   
    

</script>
