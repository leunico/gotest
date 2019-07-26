var VERIFY_CODE_ID = 0;
var BUTTON_REMAIN_TIME = 120;
$(function () {
    $('.getMusicMobileCode').on('click', function () {
        var $this = $(this);
        var mobile = $this.closest('form').find('input[name="mobile"]').val();

        if (!codepku.dict.validateMobileNumber(mobile)) {
            layer.msg('手机号码不正确');
            return false;
        }

        if ($this.hasClass('disabled')) {
            return false;
        }

        $this.addClass('disabled');


        var params = $(this).data('params');
        if (typeof params != "object") {
            params = {};
        }

        params.mobile = mobile;
        params.tpl = 'code.music'
        getMobileCode($this, params)

    })

    $('.getArtMobileCode').on('click', function () {
        var $this = $(this);
        var mobile = $this.closest('form').find('input[name="mobile"]').val();
        var params = $(this).data('params');


        if (!codepku.dict.validateMobileNumber(mobile)) {
            layer.msg('手机号码不正确');
            return false;
        }
        if ($this.hasClass('disabled')) {
            return false;
        }
        $this.addClass('disabled');

        if (typeof params != "object") {
            params = {};
        }
        params.mobile = mobile;
        params.tpl = 'code.art'

        console.log(params)
        getMobileCode($this, params)

    })

    var getMobileCode = function (el, params) {
        console.log(el);
        console.log(params)
        $.ajax({
            method:'POST',
            url:'/api/sms-send',
            data:params,
            dataType:'json',
            success: function (res) {
                el.removeClass('disabled')
                console.log(res)
                //todo
                VERIFY_CODE_ID = res.verification_code_id;
                time(el);
            },
            error:function (jqXHR, textStatus, errorThrown) {
                el.removeClass('disabled')
                var response = jqXHR.responseJSON;
                layer.msg(response.message);

                return false;
            }
        })
    }


    $(document).on("click",".information_dialog .close", function(e){
        $(".information_dialog").hide();
    })

});

function time(o) {
    if (BUTTON_REMAIN_TIME == 0) {
        o.removeClass("disabled");
        o.text("获取验证码");
        BUTTON_REMAIN_TIME = 120;
    } else {
        o.addClass("disabled");
        o.text("已发送(" + BUTTON_REMAIN_TIME + "s)");
        BUTTON_REMAIN_TIME--;
        setTimeout(function () {
                time(o)
            },
            1000)
    }
}
