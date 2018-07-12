<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Something goes wrong</title>
    <style>
        .notfound{margin: 100px 0 80px;width: 100%;text-align: center;}
        .notfound img{width: 400px;}
        #time{font-size: 18px;margin-right: 4px;}
        .text-wrapper{text-align: center;}
        .btn-return{display:inline-block;width: 140px;height: 40px;line-height: 40px; color: #00A2FF;border: 1px solid #9c9c9c;border-radius: 5px;background: #ededed;color: #9c9c9c;text-decoration: none;}
        .moblie #time{font-size: 34px;margin-right: 10px;}
        .moblie p{font-size: 30px;}
        .moblie .btn-return{width: 300px;height: 100px;line-height: 100px;font-size: 30px;}
    </style>
</head>
<body>
<div class="notfound">
    <img src="/image/error.png"/>
    <p><i id="time"></i>秒后自动返回</p>
    <p>{{print_r($notice)}}</p>
</div>
<div class="text-wrapper">
        @if(preg_match("/^\d*$/",$url))
            <a class="btn-return"  onclick="javascript:history.go(-{{$url}});">返回</a>
        @else
            <a class="btn-return"  href="{{$url}}">返回</a>
        @endif
</div>
</body>
<script src="https://cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script>
    var re = /^[0-9]+.?[0-9]*$/; //判断字符串是否为数字 //判断正整数 /^[1-9]+[0-9]*]*$/
    var url = "{{$url}}";//跳转路由
    console.log(url);
    var time =  "{{$sec}}"; //倒计时时间
    $('#time').text(time);
    setInterval(function(){
        if(time>1){
            time = time - 1;
            $('#time').text(time);
        }else{
            if (re.test(url)) {
                window.history.go(-url)
            }else{
                location.href = url;
            }
        }
    },1000);
    function IsPC() {
        var userAgentInfo = navigator.userAgent;
        var Agents = ["Android", "iPhone",
            "SymbianOS", "Windows Phone",
            "iPad", "iPod"];
        var flag = true;
        for (var v = 0; v < Agents.length; v++) {
            if (userAgentInfo.indexOf(Agents[v]) > 0) {
                flag = false;
                break;
            }
        }
        return flag;
    }
    if(!IsPC()){
        $('body').addClass('moblie');
    }
</script>
</html>