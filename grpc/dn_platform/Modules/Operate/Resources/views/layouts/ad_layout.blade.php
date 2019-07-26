<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="@yield('meta-desc', '迪恩学院,即Digital Native Academy,是为数字化时代的孩子打造的在线国际学校。推出艺术编程、数字音乐课程,课程融合AI与编程、科技、艺术等多领域知识,有数字化教学与创意体验,体系课程让孩子进阶成长,国际化视野培养多元人才')"/>
    <meta name="keywords" content="@yield('meta-keyword', '迪恩学院,迪恩课程')"/>
    <title>迪恩学院 - @yield('title','资讯中心')</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/seo_mobile.css')}}"/>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome-4.7.0/css/font-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('js/layer/mobile/need/layer.css') }}">
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    @yield('stylesheets')
</head>
<body>


@yield('content')

<div class="dean_mobile_footer">
    {{--<p class="copyright_statement_word">Copyright © 2017 CodePKu Tech. <a href="http://www.miitbeian.gov.cn">粤ICP备15056056号</a><br />深圳市编玩边学教育科技有限公司</p>--}}
</div>

@if ($isMobile)
{{--    <script src="{{ asset('js/layer/mobile/layer.js') }}"></script>--}}
@else

@endif
<script src="{{ asset('js/layer/layer.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/codepku.js') }}"></script>
<script src="{{ asset('js/basic.js') }}"></script>

<script type="text/javascript">
    // $(function() {
        var formSubmit = function(callback, $this) {
            // $('.formSubmit').on('click', function (e) {
            //     e.preventDefault();
            //     var $this = $(this);
                var $form = $this.closest('form')
                if ($this.hasClass('disabled')) {
                    return false;
                }

                $this.addClass('disabled');
                var data = $form.serialize() + '&' + $.param({'verify_code_id': VERIFY_CODE_ID});
                console.log(data);
                layer.load(1, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
                $.ajax({
                    method:'POST',
                    url:'/form/save',
                    data:data,
                    dataType:'json',
                    success:function (res) {
                        layer.closeAll();
                        $this.removeClass('disabled');
                        console.log(res);
                        $form[0].reset();
                        BUTTON_REMAIN_TIME = 0;
                        layer.msg('填写成功');

                        callback()
                        //$(".information_dialog").show();
                    },
                    error:function (jqXHR) {
                        layer.closeAll();
                        $this.removeClass('disabled');
                        var response = jqXHR.responseJSON;
                        layer.msg(response.message);
                    }
                })
            // })
        };

    // });
</script>

@yield('scripts')
</body>

</html>
