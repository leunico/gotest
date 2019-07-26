@extends('operate::layouts.ad_layout')
@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/marketing/201902/art_code_mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/swiper.min.css') }}">
    <style type="text/css">
        .swiper-pagination-bullet-active{
            opacity: 1;
            background: #95542f;
        }

        .footer_box{
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            height: 16vw;
            background-color: #ffe583;
            position: fixed;
            bottom: 0;
            left: 0;
            z-index: 9999;
            transform: translateY(25vw);
            transition: all 0.5s;

        }
        .letop{
            transform: translateY(0);
        }
        .footer_box .btm_left_img{
            position: absolute;
            left: 4vw;
            bottom: 2.6vw;
            width: 17vw;
            transform: rotateY(180deg);
        }
        .footer_box .btm_tipst{
            color: #f97f62;
            font-family: "微软雅黑";
            flex: 1;
            font-size: 4vw;
            padding: 0 0 0 22vw;
            margin: 0;

        }
        .footer_box .btm_btn{
            width: 37.3336vw;
            height: 13.3334vw;
            display: flex;
            align-items: center;
        }
        .footer_box .btm_btn img{
            width: 100%;
        }
    </style>
@stop
@section('title', '免费领取艺术编程直播课')
@section('content')
    <div class="art_mobile_market">
        <img src="{{ asset('img/marketing/201902/art_code_mobile/1banner.png') }}"/>
        <div class="art_form">
            <div class="form_bg">
                <h2><span>免费预约</span>迪恩艺术编程直播课</h2>
                <form action="" method="post">
                    {!! csrf_field() !!}
                    <div class="form_item row_style">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/shuben.png') }}"/>
                        <input type="text" name="name" id="" value="" placeholder="请输入孩子姓名"/>
                    </div>
                    <div class="form_item row_style">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/leval_icon.png') }}"/>
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
                        <img style="width: 1.333vw;" src="{{ asset('img/marketing/201902/art_code_mobile/right_icon.png') }}"/>
                    </div>
                    <div class="form_item row_style">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/phone_icon.png') }}"/>
                        <input type="text" name="mobile" id="" value="" placeholder="请输入您的手机号"/>
                    </div>
                    <div class="form_item row_style">
                        <div class="verify_left">
                            <img src="{{ asset('img/marketing/201902/art_code_mobile/yanzhengma.png') }}"/>
                            <input type="text" name="verify_code" id="" value="" placeholder="请输入验证码"/>
                        </div>
                        <button class="phone_btn yanzheng_wrap getArtMobileCode" data-params='{"leads":1}' type="button">获取验证码</button>
                    </div>
                    <button type="button" class="yellow_btn formSubmit">立即预约</button>

                </form>
            </div>

        </div>
        <img src="{{ asset('img/marketing/201902/art_code_mobile/2.png') }}"/>
        <img src="{{ asset('img/marketing/201902/art_code_mobile/3.png') }}"/>
        <div class="mobile_bg04">
            <img src="{{ asset('img/marketing/201902/art_code_mobile/4-1.png') }}"/>
        </div>

        <img src="{{ asset('img/marketing/201902/art_code_mobile/5.png') }}"/>
        <div class="mobile_bg06">
            <div class="reaps_wiper">
                <div class="swiper-container reap">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="{{ asset('img/marketing/201902/art_code_mobile/6-1.png') }}"/>
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ asset('img/marketing/201902/art_code_mobile/6-2.png') }}"/>
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ asset('img/marketing/201902/art_code_mobile/6-3.png') }}"/>
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ asset('img/marketing/201902/art_code_mobile/6-4.png') }}"/>
                        </div>
                    </div>
                    <div class="swiper-pagination reap_pagination"></div>
                </div>
            </div>
        </div>
        <div class="mobile_bg07">
            <div class="swiper-container work_wiper" dir="rtl">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/7-1.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/7-2.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/7-3.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/7-4.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/7-5.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/7-6.png') }}"/>
                    </div>
                </div>
                <div class="swiper-pagination work_pagination"></div>
            </div>
        </div>
        <div class="mobile_bg08">
            <div class="swiper-container speak_wiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/A.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/B.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/C.png') }}"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('img/marketing/201902/art_code_mobile/D.png') }}"/>
                    </div>
                </div>
                <div class="swiper-pagination speak_pagination"></div>
            </div>
        </div>
        <img src="{{ asset('img/marketing/201902/art_code_mobile/9.png') }}"/>

    </div>

    <div class="footer_box">
        <img class="btm_left_img" src="{{ asset('img/marketing/201902/art_code/xiaodi.png') }}">
        <p class="btm_tipst">
            价值<strong>299元</strong>的<br />
            迪恩艺术编程直播课
        </p>
        <span class="btm_btn"><img src="{{ asset('img/marketing/201902/art_code_mobile/bottom_btn.png') }}"/></span>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('js/swiper.min.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            //最后加载的js文件，写在页面之下

            var reapSwiper = new Swiper('.reap',{
                pagination : '.reap_pagination',
                autoplay: 3000,
                loop: true,
                autoplayDisableOnInteraction : false,
                //pagination : '#swiper-pagination1',
            })

            var workSwiper = new Swiper('.work_wiper',{
                pagination : '.work_pagination',
                autoplay: 3000,
                loop: true,
                autoplayDisableOnInteraction : false,
                //pagination : '#swiper-pagination1',
            })
            var speakSwiper = new Swiper('.speak_wiper',{
                pagination : '.speak_pagination',
                autoplay: 3000,
                loop: true,
                autoplayDisableOnInteraction : false,
                //pagination : '#swiper-pagination1',
            })

            $(window).scroll(function(evt){
                var winPos = $(window).scrollTop();
//               console.log(winPos);
//               alert($(window).height());
//               $(".banner_ad_form").animate({"height":$(window).height()},0);
                if( winPos > 300){
                    $(".footer_box").addClass("letop");
                }else{
                    $(".footer_box").removeClass("letop");
                }
            });


            $('.btm_btn').on('click', function () {
                $("html,body").animate({scrollTop: 300}, 500);
            });
            // 提交表单
            $('.formSubmit').on('click', function (e) {
                e.preventDefault();
                formSubmit(function () {

                }, $(this))
            })

        });
    </script>
@endsection

