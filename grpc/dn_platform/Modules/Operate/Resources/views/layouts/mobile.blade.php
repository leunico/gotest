<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="@yield('meta-desc', '迪恩学院,即Digital Native Academy,是为数字化时代的孩子打造的在线国际学校。推出艺术编程、数字音乐课程,课程融合AI与编程、科技、艺术等多领域知识,有数字化教学与创意体验,体系课程让孩子进阶成长,国际化视野培养多元人才')"/>
    <meta name="keywords" content="@yield('meta-keyword', '迪恩学院,迪恩课程')"/>
    <title>迪恩学院 - @yield('title','资讯中心')</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/seo_mobile.css')}}"/>
    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{asset('css/quill.bubble.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/quill.core.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/quill.snow.css')}}"/>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <style type="text/css">
        /*这是移动端导航栏样式*/
        .dean_mobile_nav {
            width: 100%;
            height: 13.33333vw;
            background-color: #2284f1;
            display: flex;
            align-items: center;
            padding: 0 2vw;
            justify-content: space-between;
            position: relative;
        }

        .dean_mobile_nav .mobile_logo {
            display: flex;
            align-items: center;
        }

        .dean_mobile_nav .mobile_logo img {
            height: 10.46667vw;
            margin-right: 1vw;
        }

        .dean_mobile_nav .mobile_logo .logo_word {
            color: #fff;
        }

        .dean_mobile_nav .mobile_logo .logo_word h3 {
            font-size: 3.73333vw;
            margin: 0 0 1vw;
            font-weight: bold;
            line-height: 1;
        }

        .dean_mobile_nav .mobile_logo .logo_word p {
            font-size: 2.13333vw;
            line-height: 1;
            margin: 0;
        }

        .dean_mobile_nav .dean_search {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            z-index: 99;
        }

        .dean_mobile_nav .dean_search p {
            color: #fff;
            font-size: 3.2vw;
            margin: 0 2vw 0 0;
        }

        .dean_mobile_nav .dean_search p .fa-phone {
            margin-right: 1vw;
        }

        .dean_mobile_nav .dean_search p .bold {
            color: #fff;
        }

        .dean_mobile_nav .dean_search .burger {
            width: 4.4vw;
            height: 4vw;
            display: flex;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dean_mobile_nav .dean_search .burger .line_i {
            width: 100%;
            height: 0.53333vw;
            background-color: #fff;
        }

        .dean_mobile_nav .mobile_navlist {
            width: 36vw;
            background-color: #2284f1;
            padding: 2vw 0;
            position: absolute;
            top: -45vw;
            right: 2vw;
            opacity: 0;
            transition: all 0.5s;
            z-index: 9;
        }

        .dean_mobile_nav .mobile_navlist ul {
            width: 100%;
            padding: 0;
        }

        .dean_mobile_nav .mobile_navlist ul li {
            width: 100%;
            list-style: none;
            height: 9.33333vw;
        }

        .dean_mobile_nav .mobile_navlist ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 4.2vw;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }

        .dean_mobile_nav .mobile_navlist ul .mobile_dean_active {
            background-color: #0079FF;
        }

        .dean_mobile_nav .mobile_navlist:before {
            content: "";
            width: 0;
            height: 0;
            display: block;
            border-top: 2vw solid rgba(255, 255, 255, 0);
            border-right: 2vw solid rgba(255, 255, 255, 0);
            border-bottom: 2vw solid #2284F1;
            border-left: 2vw solid rgba(255, 255, 255, 0);
            position: absolute;
            top: -3.8vw;
            right: 3vw;
        }

        .dean_mobile_nav .expansion {
            opacity: 1;
            top: 16vw;
        }

        .dean_mobile_footer {
            width: 100%;
            background-color: #2284F1;
            padding: 4vw 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dean_mobile_footer .copyright_statement_word {
            color: #fff;
            font-size: 3.46667vw;
            text-align: center;
            margin: 0;
        }

        .dean_mobile_footer .copyright_statement_word a {
            text-decoration: none;
            color: #fff;
        }
        /*loading*/
        .loading_box{
            display: none;
            padding: 2vw;
            color: #333;
        }
        .loading_box p{
            text-align: center;
            margin: 0;
        }
    </style>
</head>
<body>
<div class="dean_mobile_nav">
    <div class="mobile_logo">

        <a href="{{config('operate.www_domain')}}index.html"><img src="{{asset('img/news/dean_logo.png')}}"/></a>
    </div>
    <div class="dean_search">
        <p><i class="fa fa-phone"></i><span class="bold">400-636-1878</span></p>
        <div class="burger">
            <div class="line_i"></div>
            <div class="line_i"></div>
            <div class="line_i"></div>
        </div>
    </div>

    <div class="mobile_navlist">
        <ul>
            <li data-path="index"><a href="{{config('operate.www_domain')}}index.html">首页</a></li>
            <li data-path="music"><a href="{{config('operate.www_domain')}}music.html">数字音乐</a></li>
            <li data-path="art_program"><a href="{{config('operate.www_domain')}}art_program.html">艺术编程</a></li>
            <li data-path="" class="dean_nav_active"><a href="{{route('mobile-article.index')}}">新闻资讯</a></li>
            <li data-path="about"><a href="{{config('operate.www_domain')}}about.html">关于我们</a></li>

        </ul>
    </div>
</div>
@yield('content')

@yield('scriptsAfterJs')
<script type="text/javascript">
    $(function(){
        //最后加载的js文件，写在页面之下
        console.log(location.pathname.split('/')[location.pathname.split('/').length-1].split('.')[0]);
        var path = location.pathname.split('/')[location.pathname.split('/').length-1].split('.')[0];
//		    var path = location.pathname.substring(1).split('.')[0];

        if (!path) {
            path = 'index';
        }

        $('li[data-path=' + path + ']').addClass('mobile_dean_active');

        $(".burger").on("click",function(){
            $(".mobile_navlist").toggleClass("expansion");
        });

        $(document).bind("click",function(e){
            var target = $(e.target);
            if(target.closest(".burger").length == 0 && target.closest(".mobile_navlist").length == 0){
                $(".mobile_navlist").removeClass("expansion");
            }
        });
    });
</script>
</body>
<div class="dean_mobile_footer">
    <p class="copyright_statement_word">Copyright © 2017 CodePKu Tech. <a href="http://www.miitbeian.gov.cn">粤ICP备15056056号</a><br />深圳市编玩边学教育科技有限公司</p>
</div>
</html>
