<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>迪恩学院 - @yield('title','资讯中心')</title>
    <meta name="description" content="@yield('meta-desc', '迪恩学院,即Digital Native Academy,是为数字化时代的孩子打造的在线国际学校。推出艺术编程、数字音乐课程,课程融合AI与编程、科技、艺术等多领域知识,有数字化教学与创意体验,体系课程让孩子进阶成长,国际化视野培养多元人才')"/>
    <meta name="keywords" content="@yield('meta-keyword', '迪恩学院,迪恩课程')"/>
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('css/seo_pc.css?v=20190131')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/quill.bubble.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/quill.core.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/quill.snow.css')}}"/>

    <script src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
    <style type="text/css">
        .dean_nav{
            width: 100%;
            height: 80px;
            min-width: 1300px;
            background-color: #2284f1;
        }
        .dean_nav .dean_nav_cont{
            width: 1200px;
            margin: 0 auto;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dean_nav .dean_nav_cont .loge_box{
            display: flex;
            align-items: center;
        }


        .dean_nav .dean_nav_cont .dean_nav_list{
            display: flex;
            margin: 0 20px;
            padding: 0;
            width: 500px;
            height: 100%;
        }
        .dean_nav .dean_nav_cont .dean_nav_list li{
            padding: 0;
            list-style: none;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .dean_nav .dean_nav_cont .dean_nav_list li:hover{
            background-color: #0079FF;
        }
        .dean_nav .dean_nav_cont .dean_nav_list li.dean_nav_active{
            background-color: #0079FF;
        }

        .dean_nav .dean_nav_cont .dean_nav_list li a{
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dean_nav .dean_nav_cont .dean_search{
            flex: 1;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .dean_nav .dean_nav_cont .dean_search p{
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            margin-right: 20px;
            font-family: "微软雅黑";
        }
        .dean_nav .dean_nav_cont .dean_search p img{
            height: 18px;
            margin-right: 5px;
            position: relative;
            top: -2px;
        }

        .dean_nav .dean_nav_cont .dean_search a{
            display: flex;
            width: 115px;
            height: 30px;
            border-radius: 15px;
            text-decoration: none;
            color: #2284f1;
            font-size: 14px;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }
        /*公共尾部*/
        .dean_footer {
            width: 100%;
            min-width: 1300px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #2284F1;
        }

        .dean_footer .copyright_statement_word {
            width: 1200px;
            font-size: 14px;
            color: #fff;
            text-align: center;
            margin: 0;
            font-family: "微软雅黑";
        }

        .dean_footer .copyright_statement_word a {
            color: #fff;
            text-decoration: none;
            font-family: "微软雅黑";
        }

    </style>
</head>
<body>
<div class="deam_seo_pc">
    <div class="dean_nav">
            <div class="dean_nav_cont">
                <div class="loge_box">
                    <a href="{{config('operate.www_domain')}}index.html"><img src="{{asset('img/news/dean_logo.png')}}"/></a>
                </div>
                <ul class="dean_nav_list">
                    <li data-path="index"><a href="{{config('operate.www_domain')}}index.html">首页</a></li>
                    <li data-path="music"><a href="{{config('operate.www_domain')}}music.html">数字音乐</a></li>
                    <li data-path="art_program"><a href="{{config('operate.www_domain')}}art_program.html">艺术编程</a></li>
                    <li data-path="" class="dean_nav_active"><a href="{{route('article.index')}}">新闻资讯</a></li>
                    <li data-path="about"><a href="{{config('operate.www_domain')}}about.html">关于我们</a></li>
                </ul>
                <div class="dean_search">
                    <p><img src="{{asset('img/news/phone_icon.png')}}"/><span class="bold">400-636-1878</span></p>
                    <a class="system_btn" href="{{config('operate.study_url')}}">迪恩学习系统</a>
                </div>
            </div>

        </div>

    <div class="seo_container">
    @yield('content')
    </div>
</div>
</body>
<div class="dean_footer">

    <p class="copyright_statement_word">Copyright © 2017 CodePKu Tech. <a href="http://www.miitbeian.gov.cn">粤ICP备15056056号</a>  深圳市编玩边学教育科技有限公司</p>

</div>
@yield('scriptsAfterJs')
<script>
    $(function () {
        {{--$(".system_btn").attr("href","{{config('operate.www_domain')}}index.html");--}}
    })
</script>

</html>
