@extends('operate::layouts.ad_layout')
@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/marketing/201901/music1v1_success.css') }}">
@stop
@section('title', '领取成功')
@section('content')
    <div class="apply_success">
        <div class="wrap">
            <div class="content">
                <p class="font-30">还有疑问？需要咨询老师</p>
                <p class="font-bold font-36"><span class="font-48 color-primary">扫码</span>详询@if($affair == 1)【小乐老师】@else【小恩老师】@endif了解更多</p>
                <div class="img_wrap">
                    @if ($affair == 1)
                        <img src="{{ asset('img/marketing/201901/m1v1/teacher_le.png') }}" alt="">
                    @else
                        <img src="{{ asset('img/marketing/201901/m1v1/teacher_en.png') }}" alt="">
                    @endif
                </div>
                <p class="font-30 color-bottom">*更有丰富优惠课程不定期推出</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function(){

        });
    </script>
@endsection

