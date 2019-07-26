<style>
    .information_dialog {
        text-align: center;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.3);
        position: fixed;
        top: 0;
        touch-action: none;
        display: none;
        z-index: 10;
    }

    .information_dialog .info {
        top: 50%;
        transform:translateY(-50%);
        position: relative;
        padding:6vw 0;
        width: 80.5vw;
        margin: 0 auto 13.7vw;
        background:url({{ asset('img/marketing/success_tips/dialog_bg.png') }}) no-repeat;
        background-size: 100% 100%;
        border-radius: 10px;
    }

    .information_dialog .info .title {
        width: 38.5vw;
    }
    .information_dialog .info .title_text{
        color: #999;
        font-size: 2.93vw;
        margin: 1.3vw 0 4.67vw;
    }
    .information_dialog .info .text{
        color: #fff;
        font-size: 3.2vw;
        background: url({{ asset('img/marketing/success_tips/text_bg.png') }}) no-repeat;
        background-size: 100% 100%;
        width: 66.67vw;
        height: 29.2vw;
        line-height: 5vw;
        margin: 0 auto;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        padding: 0 5vw;

    }

    .information_dialog .info .qrcode{
        width:22.67vw;
        margin:3.73vw 0 2vw;
    }

    .information_dialog .info .end_text{
        color: #4abbe8;
        font-size: 4.8vw;
    }

    .information_dialog div.close {
        position: absolute;
        bottom: -13.7vw;
        width: 8.9vw;
        height: 8.9vw;
        border-radius: 50%;
        left: 0;
        right: 0;
        margin:auto;
    }

    .information_dialog div.close img {
        width: 100%;
    }
</style>

<div class="information_dialog">
    <div class="info">
        <img src="{{ asset('img/marketing/success_tips/dialog_title.png') }}" alt="" class="title">
        <p class="title_text">高效入门音乐创作，打造身边的音乐小明星</p>
        <div class="text">
            <p>
                养成未来核心竞争力，为成长赋能。零基础也可入门，在趣味教学中培养亲友之间的音乐小明星。
            </p>
        </div>
        <img src="{{ asset('img/marketing/success_tips/qrcode.png') }}" class="qrcode">
        <p class="end_text">扫码一下，<br>考级大礼包等你来拿！</p>
        <div class="close"><img src="{{ asset('img/marketing/success_tips/close.png') }}" alt=""></div>
    </div>
</div>


