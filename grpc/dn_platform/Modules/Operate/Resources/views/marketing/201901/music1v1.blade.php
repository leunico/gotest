@extends('operate::layouts.ad_layout')
@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/marketing/201901/music1v1.css') }}">
@stop
@section('title', '免费领取音乐考级礼包')
@section('content')
    <div class="music_develop">
        <img src="{{ asset('img/marketing/201901/m1v1/content1.png') }}" alt="">
        <img src="{{ asset('img/marketing/201901/m1v1/content2.png') }}" alt="">
        <img src="{{ asset('img/marketing/201901/m1v1/content3.png') }}" alt="">
        <img src="{{ asset('img/marketing/201901/m1v1/content4.png') }}" alt="">
        <img src="{{ asset('img/marketing/201901/m1v1/content5.png') }}" alt="">
        <img src="{{ asset('img/marketing/201901/m1v1/content6.png') }}" alt="">
        <img src="{{ asset('img/marketing/201901/m1v1/content7.png') }}" alt="" style="margin-bottom:15.6vw">
        <div class="bottom_fox">
            <div>
                <img src="{{ asset('img/marketing/201901/m1v1/bottom_wawa.png') }}" alt="">
            </div>
            <img src="{{ asset('img/marketing/201901/m1v1/bottom_btn.png') }}" alt="" class="get_class">
        </div>
        <div class="dialog">
            <div class="form_wrap">
                <h3><span>迪恩1对1音乐考级试听课</span></h3>
                <form>
                    <input type="hidden" name="need_grade" value="1">
                    <input type="hidden" name="tag" value="{{ \Modules\Operate\Entities\Lead::TAG_MUSIC_CONTEST }}">
                    <input type="hidden" name="operational_affair" value="{{ $affair }}">
                    <input type="hidden" name="oauth_category" value="music">
                    {!! csrf_field() !!}
                    <label for="name">
                        <div class="img_box">
                            <img src="{{ asset('img/marketing/201901/m1v1/sex_icon.png') }}" alt="">
                        </div>
                        <div class="input_wrap">
                            <input type="text" placeholder="请输入孩子姓名" name="name" id="name">
                        </div>
                    </label>
                    <label for="grade">
                        <div class="img_box">
                            <img src="{{ asset('img/marketing/201901/m1v1/age_icon.png') }}" alt="">
                        </div>
                        <select name="grade" id="grade">
                            <option disabled selected value="">请选择孩子年级</option>
                            @foreach($gradeMap as $key => $map)
                                <option value="{{ $key }}">{{ $map }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <div class="img_box">
                            <img src="{{ asset('img/marketing/201901/m1v1/tel_icon.png') }}" alt="">
                        </div>
                        <div class="input_wrap">
                            <input type="number" placeholder="请输入家长手机号" name="mobile">
                            <div class="yanzheng_wrap getMusicMobileCode" data-params='{"tag":"music_contest"}'>获取验证码</div>
                        </div>
                    </label>
                    <label>
                        <div class="img_box">
                            <img src="{{ asset('img/marketing/201901/m1v1/yanzheng_icon.png') }}" alt="">
                        </div>
                        <input type="text" placeholder="请输入验证码" name="verify_code">
                    </label>
                    <div class="apply_btn formSubmit">限时免费报名</div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function(){
            // $('.btnBottom').on('click', function (object) {
            //     $("html,body").animate({scrollTop: 300}, 500);
            // });
            $('.formSubmit').on('click', function (e) {
                e.preventDefault();
                formSubmit(function () {
                    //todo
                    location.href = '/operational-affair?affair=' + "{{ $affair }}"
                }, $(this))
            })

            $(".get_class").click(function(){
                $(".dialog").show();
            });
            $(".dialog").click(function(e){
                $(".dialog").hide();
            });
            //阻止事件冒泡
            $(".form_wrap").click(function(e){
                e.preventDefault();
                e.cancelBubble = true;
                e.stopPropagation();
            });
        });
    </script>
@endsection

