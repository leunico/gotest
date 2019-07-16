<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => '您必须接受 :attribute',
    'active_url' => ':attribute 必须是有效链接',
    'after' => ':attribute 必须是:date之后的日期',
    'alpha' => ':attribute 只能包含字母',
    'alpha_dash' => ':attribute 只能包含字母、数字、短横线',
    'alpha_num' => ':attribute 只能包含字母和数字',
    'array' => ':attribute 必须是一个数组',
    'before' => ':attribute 必须是 :date 之前的日期',
    'between' => [
        'numeric' => ':attribute 必须在 :min 与 :max 之间',
        'file' => ':attribute 的大小必须在 :min KB与 :max KB之间.',
        'string' => ':attribute 的长度需要在 :min 与 :max 之间',
        'array' => ':attribute 必须在 :min 到 :max 项之间',
    ],

    'boolean' => ':attribute 字符必须是 true 或 false',
    'confirmed' => ':attribute 二次确认不匹配',
    'date' => ':attribute 必须是一个合法的日期',
    'date_format' => ':attribute 与给定的格式 :format 不符合',
    'different' => ':attribute 必须不同于:other',
    'digits' => ':attribute 必须是 :digits 位',
    'digits_between' => ':attribute 必须在 :min and :max 位之间',
    'email' => ':attribute 必须是一个合法的电子邮件地址。',
    'filled' => ':attribute 的字段是必填的',
    'exists' => '选定的 :attribute 是无效的',
    'image' => ':attribute 必须是一个图片 (jpeg, png, bmp 或者 gif)',
    'in' => '选定的 :attribute 是无效的',
    'integer' => ':attribute 必须是个整数',
    'ip' => ':attribute 必须是一个合法的 IP 地址。',
    'max' => [
        'numeric' => ':attribute 的最大长度为 :max 位',
        'file' => ':attribute 的最大为 :max',
        'string' => ':attribute 的最大长度为 :max 字符',
        'array' => ':attribute 的最大个数为 :max 个',
    ],
    'mimes' => ':attribute 的文件类型必须是:values',
    'min' => [
        'numeric' => ':attribute 的最小长度为 :min 位',
        'string' => ':attribute 的最小长度为 :min 字符',
        'file' => ':attribute 大小至少为:min KB',
        'array' => ':attribute 至少有 :min 项',
    ],
    'not_in' => '选定的 :attribute 是无效的',
    'numeric' => ':attribute 必须是数字',
    'regex' => ':attribute 格式是无效的',
    'required' => ':attribute必须填写',
    'required_if' => ':attribute 字段是必须的当 :other 是 :value',
    'required_with' => ':attribute 字段是必须的当 :values 是存在的',
    'required_with_all' => ':attribute 字段是必须的当 :values 是存在的',
    'required_without' => ':attribute 字段是必须的当 :values 是不存在的',
    'required_without_all' => ':attribute 字段是必须的当 没有一个 :values 是存在的',
    'same' => ':attribute 和 :other 必须匹配',
    'size' => [
        'numeric' => ':attribute 必须是 :size 位',
        'file' => ':attribute 必须是 :size KB',
        'string' => ':attribute 必须是 :size 个字符',
        'array' => ':attribute 必须包括 :size 项',
    ],
    'string' => ':attribute 必须是字符串',
    'unique' => ':attribute 已经被使用',
    'url' => ':attribute 格式不正确',
    'timezone' => ':attribute 必须是有效的时区',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => '用户名',
        'email' => '邮箱',
        'password' => '密码',
        'upload_file' => '上传文件',
        'account' => '账号',
        'captcha' => '验证码',
        'mobile' => '手机号',
        'content' => '内容',
        'note' => '笔记',
        'avatar' => '头像',
        'sex' => '性别',
        'phone' => '手机号',
        'telephone' => '联系方式',
        'contact' => 'QQ/微信',
        'nature' => '性质',
        'title' => '标题',
        'description' => '说明',
        'work_id' => '作品',
        'point_price' => '积分',
        'age' => '年龄',
        'question' => '问题',
        'suggest' => '建议',
        'address' => '地址',
        'academy' => '学院',
        'class' => '班级',
        'parents_name' => '父母姓名',
        'child_name' => '子女姓名',
        'child_age' => '子女年龄',
        'major' => '专业',
        'identify_code' => '验证码',
        'course' => '课程',
        'verify_code' => '验证码',
        'date' => '日期',
        'time' => '时间',
        'new_date' => '日期',
        'new_time' => '时间',
        'course_id' => '课程id',
        'permission_name' => '权限名',
        'link' => '链接',
        'lab_name' => '标签名',
	    'screen' => '场次',
        'reg_name' => '用户名',
        'study_date' => '学习日期',
        'detail_time' => '具体时间',
        'live_id' => '直播',
        'room_num' => '教室编号',
        'course_ids' => '课程',
        'sn' => '编号',
        'signup_num' => '报名人数',
        'website_name' => '网站用户名',
        'courses' => '课程',
        'coupon_num' => '优惠码',
        'oldpass' => '原始密码',
        'newpass' => '新密码',
        'newpass_confirmation' => '确认密码',
        'mobile2' => '手机号码',
        'mobile3' => '手机号码',
        'name2' => '姓名',
        'name3' => '姓名',
        'verify-code' => '验证码'
    ],

];
