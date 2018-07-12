{{--<!doctype html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
    {{--<meta charset="UTF-8">--}}
    {{--<meta name="viewport"--}}
          {{--content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">--}}
    {{--<meta http-equiv="X-UA-Compatible" content="ie=edge">--}}
    {{--<title>Something goes wrong</title>--}}
{{--</head>--}}
{{--<body>--}}
{{--<a href="{{ url('/') }}">--}}
    {{--<img src="{{ asset('common/404.png') }}">--}}
{{--</a>--}}
{{--</body>--}}
{{--</html>--}}

        <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        .text-wrapper{text-align: center;}
        .notfound{margin: 250px 0 80px;width: 100%;text-align: center;}
        .btn-return{display:inline-block;width: 140px;height: 40px;line-height: 40px; color: #00A2FF;border: 1px solid #9c9c9c;border-radius: 5px;background: #ededed;color: #9c9c9c;text-decoration: none;}
        .moblie .notfound{margin-top: 500px;}
        .moblie .btn-return{width: 300px;height: 100px;line-height: 100px;font-size: 30px;}
    </style>
</head>
<body>
<div class="notfound">
    <img src="/image/404.png"/>
</div>
<div class="text-wrapper">
    <a class="btn-return" href="/">返回首页</a>
</div>
</body>
<script src="https://cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script>
    function IsPC(){
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