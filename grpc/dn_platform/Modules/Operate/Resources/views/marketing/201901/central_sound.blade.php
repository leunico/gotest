@extends('operate::layouts.ad_layout')
@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/marketing/201901/central_sound.css') }}">
@stop
@section('title', '免费领取音乐考级包')
@section('content')
    <div class="music_develop">
        <img src=" {{ asset('img/marketing/201901/central_sound/header.png') }} " alt="">
        <div class="form_wrap">
            <h3><img src=" {{ asset('img/marketing/201901/central_sound/title_icon.png') }} ">免费领取音乐考级包</h3>
            <p class="small_title"><i>为您的孩子开启音乐梦想</i></p>
            <form class="marketingForm">
                {!! csrf_field() !!}
                <label for="age">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/age_icon.png') }} " alt="">
                    </div>
                    <select name="age" id="age">
                        <option disabled selected value="">请选择孩子的年龄</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                    </select>
                </label>
                <label for="name">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/sex_icon.png') }} " alt="">
                    </div>
                    <div class="input_wrap">
                        <input type="text" name="name" id="name" placeholder="请输入孩子姓名">
                    </div>
                </label>
                <label for="mobile">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/tel_icon.png') }} " alt="">
                    </div>
                    <div class="input_wrap">
                        <input type="text" name="mobile" id="mobile" placeholder="请输入家长手机号">
                        <div class="yanzheng_wrap getMusicMobileCode" data-params='{"leads":1}'>获取验证码</div>
                    </div>

                </label>
                <label for="verify-code1">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/yanzheng_icon.png') }} " alt="">
                    </div>
                    <input type="text" name="verify_code" id="verify-code1" placeholder="请输入验证码">
                </label>
                <a type="javascript:;" class="formSubmit" style="display: block; text-align: center;">
                    <img src=" {{ asset('img/marketing/201901/central_sound/get_now.png') }} " alt="" style="width: 83%;" class="">
                </a>
                {{--<p><span>一分钟前</span><span>156****22**</span><span>已成功领取试听课</span></p>--}}
            </form>
        </div>
        <img src=" {{ asset('img/marketing/201901/central_sound/content1.png') }} " alt="">
        <img src=" {{ asset('img/marketing/201901/central_sound/content2.png') }} " alt="">
        <img src=" {{ asset('img/marketing/201901/central_sound/content3.png') }} " alt="">
        <img src=" {{ asset('img/marketing/201901/central_sound/content4.png') }} " alt="">
        <img src=" {{ asset('img/marketing/201901/central_sound/content5.png') }} " alt="">
        <img src=" {{ asset('img/marketing/201901/central_sound/content6.png') }} " alt="">
        <img src=" {{ asset('img/marketing/201901/central_sound/content7.png') }} " alt="">
        <div class="form_wrap form_wrap2">
            <h3><img src=" {{ asset('img/marketing/201901/central_sound/bottom_icon.png') }} ">免费在线领取试听课</h3>
            <p class="small_title"><i>本月已有<span>1874</span>人领取</i></p>
            <form class="marketingForm">
                {!! csrf_field() !!}
                <label for="age2">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/age_icon.png') }} " alt="">
                    </div>
                    <select name="age" id="age2">
                        <option disabled selected value="">请选择孩子的年龄</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                    </select>
                </label>
                <label for="name2">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/sex_icon.png') }} " alt="">
                    </div>
                    <div class="input_wrap">
                        <input type="text" name="name" id="name2" placeholder="请输入孩子姓名">
                    </div>
                </label>
                <label for="mobile2">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/tel_icon.png') }} " alt="">
                    </div>
                    <div class="input_wrap">
                        <input type="text" name="mobile" id="mobile2" placeholder="请输入家长手机号">
                        <div class="yanzheng_wrap getMusicMobileCode" data-params='{"leads":1}'>获取验证码</div>
                    </div>
                </label>
                <label for="verify-code">
                    <div class="img_box">
                        <img src=" {{ asset('img/marketing/201901/central_sound/yanzheng_icon.png') }} " alt="">
                        {{--<button type="button" class="btn getMusicMobileCode" data-params='{"leads":1}'>获取验证码</button>--}}
                    </div>
                    <input type="text" id="verify-code" name="verify_code" placeholder="请输入验证码">
                </label>

                <a type="javascript:;" class="formSubmit" style="display: block; text-align: center;">
                    <img src=" {{ asset('img/marketing/201901/central_sound/get_now.png') }} " alt="" style="width: 83%;" class="">
                </a>
                {{--<p><span>一分钟前</span><span>156****22**</span><span>已成功领取试听课</span></p>--}}
            </form>
        </div>
        <div class="bottom_fox">
            <div>
                <img src=" {{ asset('img/marketing/201901/central_sound2/bottom_wawa.png') }} " alt="">
            </div>
            <!-- <img src=" {{ asset('img/marketing/201901/central_sound/bottom_fix.png') }} " alt=""> -->

            <img src=" {{ asset('img/marketing/201901/central_sound/bottom_btn.png') }} " alt="" class="btnBottom">
        </div>
    </div>

    @include('operate::marketing.success_tips')
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function(){
            $('.formSubmit').on('click', function (e) {
                e.preventDefault();
                formSubmit(function () {
                    $(".information_dialog").show();
                }, $(this))
            })


            $('.btnBottom').on('click', function (object) {
                $("html,body").animate({scrollTop: 300}, 500);
            });
        });
    </script>
@endsection

