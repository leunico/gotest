@extends('operate::layouts.ad_layout')
@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/marketing/201902/art_code.css') }}">
    <link rel="stylesheet" href="{{ asset('css/marketing/201902/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/marketing/201902/style.css') }}">
    <style>
        .side_bottom{
            width: 100%;
            height: 80px;
            background-color: #fa9572;
            position: fixed;
            bottom: 0;
            transition: all 0.5s;
            z-index: 99;
            transform: translateY(240px);
        }
        .show_up{
            transform: translateY(0);
        }
        .side_bottom .side_tab_cont{
            width: 1200px;
            height: 100%;
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
            padding-left: 150px;

        }
        .side_bottom .side_tab_cont img.year_wz{
            position: absolute;
            bottom: 0px;
            left: 0;
        }
        .side_bottom .side_tab_cont .wz_tips{
            font-size: 37px;
            font-weight: bold;
            color: #fefad1;
            line-height: 1;
        }
        .side_bottom .side_tab_cont .wz_tips span{
            font-size: 53px;
        }
        .side_bottom .side_tab_cont form{
            margin-left: 37px;
            flex: 1;
        }
        .side_bottom .side_tab_cont form .form_item{
            flex: 1;
            margin: 0 auto 5px;
            border-radius: 10px;
            height: 54px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;

        }
        .side_bottom .side_tab_cont form .row_style{
            background-color: #fff;
            padding: 10px 20px;
            margin-right: 6px;
        }
        .side_bottom .side_tab_cont form .form_item input{
            outline: none;
            border: none;
            margin-left: 5px;
            flex: 1;
            background: none;
            font-size: 18px;
        }

        .side_bottom .side_tab_cont form .form_item .verify_left{
            flex: 1;
            margin-right: 5px;
            height: 54px;
            border-radius: 10px;
            box-sizing: border-box;
            padding: 0 18px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .side_bottom .side_tab_cont form .form_item .verify_left input{
            outline: none;
            border: none;
            background: none;
            width: 100%;
            font-size: 18px;
        }
        .side_bottom .side_tab_cont button{
            margin-left: auto;
            outline: none;
            border: none;
            background: none;
            padding: 0;
        }
        .side_bottom .side_tab_cont form .has_btn{
            width: 306px;
            height: 56px;
            display: block;
            text-align: center;
            background-color: #fff6ee;
            border-radius: 28px;
            font-size: 30px;
            color: #fa9572;
            font-weight: bold;
        }

        .appointment_center {
            width: 600px !important;
            margin: 0 !important;
            position: absolute !important;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%) !important;
        }


        .top_tab{
            width: 100%;
            height: 80px;
            background-color: #ffa27f;
            position: fixed;
            top: 0;
            z-index: 99;
        }
        .top_tab .has_sngin{
            border: none;
            outline: none;
            padding: 10px 20px;
            height: 40px;
            display: block;
            text-align: center;
            background-color: #fff;
            border-radius: 28px;
            font-size: 16px;
            color: #fa9572;
            font-weight: bold;
            font-family: "微软雅黑";
        }

        /*调整swiper匀速播放*/
        .bx-wrapper{
            overflow: hidden;
        }
        .uniform_aplay{
            width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }
    </style>
@stop
@section('title', '免费领取艺术编程直播课')
@section('content')
    <div class="whole_mian bg_lattice_repeat">
        <div style="height: 80px;">
            <div class="top_tab">
                <div style="display: flex;align-items: center;justify-content: space-between;width: 1200px;margin: 0 auto;height: 100%;">
                    <a href=""><img src="{{asset('img/marketing/201902/art_code/dean_white_logo.png')}}"/></a>
                    <div style="display: flex; height: 100%;align-items: center;">
                        <div style="color: #fefad1; margin-right: 10px;font-size: 16px;font-weight: bold;">
                            <i class="fa fa-phone" style="margin-right: 5px;"></i><span>400-636-1878</span>
                        </div>
                        <button class="has_sngin" type="button" data-toggle="modal" data-target="#promote_pop">申请免费试听</button>
                    </div>

                </div>

            </div>
        </div>

        <div class="banner_1v1">
            <div class="plate_mian flex flex_justify-content_end">
                <div class="right_form">
                    <h2><span>免费领取</span><br />迪恩艺术编程直播课</h2>
                    <p>让你家萌娃爱上编程</p>
                    <form action="" method="post">
                        {!! csrf_field() !!}
                        <div class="form_item row_style">
                            <img src="{{asset('img/marketing/201902/art_code/name_icon.png')}}"/>
                            <input type="text" name="name" id="" value="" placeholder="请输入孩子姓名"/>
                        </div>
                        <div class="form_item row_style">
                            <img src="{{asset('img/marketing/201902/art_code/level_icon.png')}}"/>
                            <select name="grade" class="text_select_age">
                                <option value="">请选择年级</option>
                                <option value="20">一年级</option>
                                <option value="30">二年级</option>
                                <option value="40">三年级</option>
                                <option value="50">四年级</option>
                                <option value="60">五年级</option>
                                <option value="70">六年级</option>
                                <option value="80">初一</option>
                                <option value="90">初二</option>
                                <option value="100">初三</option>
                            </select>
                        </div>
                        <div class="form_item row_style">
                            <img src="{{asset('img/marketing/201902/art_code/phone_icon.png')}}"/>
                            <input type="text" name="mobile" id="" value="" placeholder="请输入您的手机号"/>
                        </div>
                        <div class="form_item">
                            <div class="verify_left">
                                <input type="text" name="verify_code" id="" value="" placeholder="请输入验证码"/>
                            </div>
                            <button class="phone_btn yanzheng_wrap getArtMobileCode" data-params='{"leads":1}' type="button">获取验证码</button>
                        </div>
                        <button type="button" class="formSubmit">
                            <img src="{{asset('img/marketing/201902/art_code/form_btn01.png')}}"/>
                        </button>

                    </form>
                </div>

            </div>
        </div>

        <!--编程女孩最好的选择-->
        <div class="plate_mian" style="padding: 55px 0 48px;">
            <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle01.png')}}"/>
            <div class="flex flex_justify-content_between">
                <img src="{{asset('img/marketing/201902/art_code/art_s01.png')}}"/>
                <img src="{{asset('img/marketing/201902/art_code/art_s02.png')}}"/>
                <img src="{{asset('img/marketing/201902/art_code/art_s03.png')}}"/>
            </div>
        </div>
        <!--作品展示-->
        <div class="plate_bg01">

            <div class="plate_mian" style="padding: 55px 0 48px;">
                <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle02.png')}}"/>

                <div class="uniform_aplay">
                    <img src="{{asset('img/marketing/201902/art_code/work_s01.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/work_s02.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/work_s03.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/work_s04.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/work_s05.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/work_s06.png')}}"/>
                </div>

                <h1 class="course_title">一年48节课  完成24个作品</h1>

                <img src="{{asset('img/marketing/201902/art_code/tj_list.png')}}"/>

            </div>


        </div>

        <!--我们的优势-->
        <div class="plate_mian" style="padding: 55px 0 48px;">
            <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle03.png')}}"/>
            <div class="flex flex_justify-content_between">
                <div>
                    <img src="{{asset('img/marketing/201902/art_code/strength_s01.png')}}"/>
                    <div>
                        <h3 class="small_title03">真人直播教学模式</h3>
                        <p>
                            老师面对面教学<br />答疑解惑更及时
                        </p>
                    </div>
                </div>

                <div>
                    <img src="{{asset('img/marketing/201902/art_code/strength_s02.png')}}"/>
                    <div>
                        <h3 class="small_title03">体系课程零基础学习</h3>
                        <p>
                            国际标准课程体系<br />零基础进阶学习
                        </p>
                    </div>
                </div>

                <div>
                    <img src="{{asset('img/marketing/201902/art_code/strength_s03.png')}}"/>
                    <div>
                        <h3 class="small_title03">动手创作更有趣</h3>
                        <p>
                            理论与实践相结合<br />亲子一起创作更好玩
                        </p>
                    </div>
                </div>

                <div>
                    <img src="{{asset('img/marketing/201902/art_code/strength_s04.png')}}"/>
                    <div>
                        <h3 class="small_title03">五星级教学服务</h3>
                        <p>
                            专属学习服务平台<br />双师标配学习无忧
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!--国际标准-->
        <div class="plate_bg02">

            <div class="plate_mian" style="padding: 80px 0 48px;">
                <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle04.png')}}"/>
                <div style="margin-bottom: 43px;">
                    <h2 class="green_title">基于美国《国家核心艺术标准——视觉艺术》</h2>
                    <p class="green_text">对标ISTE标准，参考美国、荷兰等国家课程内容，精心研发符合孩子不同发展阶段的体系课程</p>
                </div>

                <img src="{{asset('img/marketing/201902/art_code/global_course.png')}}"/>

            </div>
        </div>

        <!--学习闭环-->
        <div class="plate_mian" style="padding: 80px 0 48px;">
            <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle05.png')}}"/>

            <img src="{{asset('img/marketing/201902/art_code/alist.png')}}"/>

        </div>

        <!--学习收获-->
        <div class="plate_bg03">

            <div class="plate_mian" style="padding: 55px 0 48px;">
                <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle06.png')}}"/>
                <div class="flex flex_justify-content_between">
                    <img src="{{asset('img/marketing/201902/art_code/learn_s01.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/learn_s02.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/learn_s03.png')}}"/>
                    <img src="{{asset('img/marketing/201902/art_code/learn_s04.png')}}"/>
                </div>
            </div>
        </div>

        <!--家长说-->
        <div class="plate_mian" style="padding: 55px 0 48px;">
            <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle07.png')}}"/>
            <!--<div class="">
                <img src="{{asset('img/marketing/201902/art_code/speak_s01.png')}}"/>
                <img src="{{asset('img/marketing/201902/art_code/speak_s02.png')}}"/>
                <img src="{{asset('img/marketing/201902/art_code/speak_s03.png')}}"/>
            </div>-->
            <img src="{{asset('img/marketing/201902/art_code/parent_img01.png')}}"/>
            <div id="wechart_banner">
                <div class="banner_bg"></div>
                <div id="banner_list">
                    <ul>

                        <li style="width: 344px; height: 440px; top: 0px; left: 352px; z-index: 10; " class="hove">
                            <img src="{{asset('img/marketing/201902/art_code/parents_eval01.png')}}">
                            <div style="opacity: 0; "></div>
                        </li>
                        <li style="width: 260px; height: 332px; top: 56px; left: 148px; z-index: 8; " class="">
                            <img src="{{asset('img/marketing/201902/art_code/parents_eval02.png')}}">
                            <div style="opacity: 0.71; "></div>
                        </li>
                        <li style="width: 204px; height: 260px; top: 92px; left: 0px; z-index: 6; " class="">
                            <img src="{{asset('img/marketing/201902/art_code/parents_eval03.png')}}">
                            <div style="opacity: 0.71; "></div>
                        </li>
                        <li style="width: 100px; height: 200px; top: 92px; left: 50px; z-index: 4; " class="">
                            <img src="{{asset('img/marketing/201902/art_code/parents_eval04.png')}}">
                            <div style="opacity: 0.71; "></div>
                        </li>

                    </ul>
                    <!--<a href="javascript:;" class="banner_prev"></a>
                    <a href="javascript:;" class="banner_next"></a>-->
                </div>
            </div>
        </div>

        <div class="plate_bg04">

            <div class="plate_mian" style="padding: 55px 0 48px;">
                <img class="title_bottom" src="{{asset('img/marketing/201902/art_code/stitle08.png')}}"/>
                <p style="font-size: 18px;color: #424242;text-align: left;">深圳市编玩边学教育科技有限公司，是早期进入少儿编程行业的互联网教育公司之一，目前已获得来自君联资本、科大讯飞、泰亚等知名投资机构累计超1亿元融资。旗下“迪恩艺术编程”专为6岁以上的女孩提供系列编程课程，同步提升孩子艺术、科技素养，培养未来核心竞争力。</p>
            </div>
        </div>


    </div>

    <div class="side_bottom">
        <div class="side_tab_cont">
            <img class="year_wz" src="{{asset('img/marketing/201902/art_code/xiaodi.png')}}"/>
            <div style="display: flex; align-items: center; padding: 13px 0;height: 100%;">
                <div class="wz_tips">价值<span>299元</span>的迪恩艺术编程直播课</div>
                <form action="" method="post">
                    <div style="display: flex;">
                        <!--<div class="form_item row_style">
                            <img src="{{asset('img/marketing/201902/art_code/B5.png')}}"/>
                            <input type="text" name="" id="b_phone" value="" placeholder="请输入您的手机号"/>
                        </div>-->

                        <button type="button" class="has_btn" data-toggle="modal" data-target="#promote_pop">一键预约</button>
                    </div>


                </form>
            </div>

        </div>

    </div>

    <div class="modal fade" id="promote_pop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog appointment_center" role="document">
            <div class="modal-content" style="background: none;box-shadow: none;border: none;padding: 0;">
                <div class="modal-body" style="padding: 0; position: relative; ">
                    <img style="position: absolute; top: -5px; right: -40px;" data-dismiss="modal" src="{{asset('img/marketing/201902/art_code/shut_down.png')}}"/>
                    <div class="banner_form_card">
                        <h2><span>免费预约</span></h2>
                        <p>迪恩艺术编程直播课</p>
                        <img style="display: block;margin: 0 auto 20px;" src="{{asset('img/marketing/201902/art_code/bottom_img.png')}}"/>
                        <form action="" method="post">
                            {!! csrf_field() !!}
                            <div class="form_item row_style">
                                <img src="{{asset('img/marketing/201902/art_code/name_icon.png')}}"/>
                                <input type="text" name="name" id="" value="" placeholder="请输入孩子姓名"/>
                            </div>
                            <div class="form_item row_style">
                                <img src="{{asset('img/marketing/201902/art_code/level_icon.png')}}"/>
                                <select name="grade" class="text_select_age">
                                    <option value="">请选择孩子年级</option>
                                    <option value="20">一年级</option>
                                    <option value="30">二年级</option>
                                    <option value="40">三年级</option>
                                    <option value="50">四年级</option>
                                    <option value="60">五年级</option>
                                    <option value="70">六年级</option>
                                    <option value="80">初一</option>
                                    <option value="90">初二</option>
                                    <option value="100">初三</option>
                                </select>
                            </div>
                            <div class="form_item row_style">
                                <img src="{{asset('img/marketing/201902/art_code/phone_icon.png')}}"/>
                                <input type="text" name="mobile" id="" value="" placeholder="请输入您的手机号"/>
                            </div>
                            <div class="form_item">
                                <div class="verify_left">
                                    <input type="text" name="verify_code" id="" value="" placeholder="请输入验证码"/>
                                </div>
                                <button class="phone_btn yanzheng_wrap getArtMobileCode" type="button">获取验证码</button>

                            </div>
                            <button type="button" class="btn_form_b formSubmit">立即预约</button>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{asset('js/jquery.bxslider.js')}}"></script>
    <script src="{{asset('js/blur_play.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            //最后加载的js文件，写在页面之下
            $('.uniform_aplay').bxSlider({
                //单独一个li或者img的宽度
                slideWidth: 288,
                //最少显示li或者img的个数
                minSlides: 1,
                //最多显示li或者img的个数
                maxSlides: 5,
                //是否自动播放循环，控制启动和暂停
                ticker: true,
                //滚动顺序？--prev向右，next向左
                autoDirection: 'prev',
                //滚动速度
                speed: 24000,
                //可能是每个元素的暂停时间
                startSlides: 0,
                //控制有缝或者无缝轮播的，参数为缝的间距
                slideMargin: 0
            });

            $(window).bind("scroll", function () {
                console.log(getDocumentTop());

                if(getDocumentTop() >= 500 ){
                    //当滚动条到底时,这里是触发内容
                    //异步请求数据,局部刷新dom
                    //ajax_function()
                    console.log("滚动高度大于500");

                    $(".side_bottom").addClass("show_up");

                }else{
                    $(".side_bottom").removeClass("show_up");
                }
            });
            //文档高度
            function getDocumentTop() {
                var scrollTop =  0, bodyScrollTop = 0, documentScrollTop = 0;
                if (document.body) {
                    bodyScrollTop = document.body.scrollTop;
                }
                if (document.documentElement) {
                    documentScrollTop = document.documentElement.scrollTop;
                }
                scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
                //console.log("scrollTop:"+scrollTop);
                return scrollTop;
            }

            //可视窗口高度
            function getWindowHeight() {
                var windowHeight = 0;
                if (document.compatMode == "CSS1Compat") {
                    windowHeight = document.documentElement.clientHeight;
                } else {
                    windowHeight = document.body.clientHeight;
                }
//              console.log("windowHeight:"+windowHeight);
                return windowHeight;
            }

            //滚动条滚动高度
            function getScrollHeight() {
                var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
                if (document.body) {
                    bodyScrollHeight = document.body.scrollHeight;
                }
                if (document.documentElement) {
                    documentScrollHeight = document.documentElement.scrollHeight;
                }
                scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
                console.log("scrollHeight:"+scrollHeight);
                return scrollHeight;
            }
            // 提交表单
            $('.formSubmit').on('click', function (e) {
                e.preventDefault();
                formSubmit(function () {
                    $("#promote_pop").modal("hide");
                }, $(this))
            })

        });
    </script>
@endsection

