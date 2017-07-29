<?php

return [
    'validation.in_gender' => ':attribute 填写不正确, 仅允许"男"或者"女"',
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'validation.accepted'             => ':attribute 必须接受。',
    'validation.active_url'           => ':attribute 不是一个有效的网址。',
    'validation.after'                => ':attribute 必须是一个在 :date 之后的日期。',
    'validation.alpha'                => ':attribute 只能由字母组成。',
    'validation.alpha_dash'           => ':attribute 只能由字母、数字和斜杠组成。',
    'validation.alpha_num'            => ':attribute 只能由字母和数字组成。',
    'validation.array'                => ':attribute 必须是一个数组。',
    'validation.before'               => ':attribute 必须是一个在 :date 之前的日期。',
    'validation.between'              => [
        'numeric' => ':attribute 必须介于 :min - :max 之间。',
        'file'    => ':attribute 必须介于 :min - :max kb 之间。',
        'string'  => ':attribute 必须介于 :min - :max 个字符之间。',
        'array'   => ':attribute 必须只有 :min - :max 个单元。',
    ],
    'validation.boolean'              => ':attribute 必须为布尔值。',
    'validation.confirmed'            => ':attribute 两次输入不一致。',
    'validation.date'                 => ':attribute 不是一个有效的日期。',
    'validation.date_format'          => ':attribute 的格式必须为 :format。',
    'validation.different'            => ':attribute 和 :other 必须不同。',
    'validation.digits'               => ':attribute 必须是 :digits 位的数字。',
    'validation.digits_between'       => ':attribute 必须是介于 :min 和 :max 位的数字。',
    'validation.dimensions'           => ':attribute 图片尺寸不正确。',
    'validation.distinct'             => ':attribute 已经存在。',
    'validation.email'                => ':attribute 不是一个合法的邮箱。',
    'validation.exists'               => ':attribute 不存在。',
    'validation.file'                 => ':attribute 必须是文件。',
    'validation.filled'               => ':attribute 不能为空。',
    'validation.image'                => ':attribute 必须是图片。',
    'validation.in'                   => '已选的属性 :attribute 非法。',
    'validation.in_array'             => ':attribute 没有在 :other 中。',
    'validation.integer'              => ':attribute 必须是整数。',
    'validation.ip'                   => ':attribute 必须是有效的 IP 地址。',
    'validation.json'                 => ':attribute 必须是正确的 JSON 格式。',
    'validation.max'                  => [
        'numeric' => ':attribute 不能大于 :max。',
        'file'    => ':attribute 不能大于 :max kb。',
        'string'  => ':attribute 不能大于 :max 个字符。',
        'array'   => ':attribute 最多只有 :max 个单元。',
    ],
    'validation.mimes'                => ':attribute 必须是一个 :values 类型的文件。',
    'validation.min'                  => [
        'numeric' => ':attribute 必须大于等于 :min。',
        'file'    => ':attribute 大小不能小于 :min kb。',
        'string'  => ':attribute 至少为 :min 个字符。',
        'array'   => ':attribute 至少有 :min 个单元。',
    ],
    'validation.not_in'               => '已选的属性 :attribute 非法。',
    'validation.numeric'              => ':attribute 必须是一个数字。',
    'validation.present'              => ':attribute 必须存在。',
    'validation.regex'                => ':attribute 格式不正确。',
    'validation.required'             => ':attribute 不能为空。',
    'validation.required_if'          => '当 :other 为 :value 时 :attribute 不能为空。',
    'validation.required_unless'      => '当 :other 不为 :value 时 :attribute 不能为空。',
    'validation.required_with'        => '当 :values 存在时 :attribute 不能为空。',
    'validation.required_with_all'    => '当 :values 存在时 :attribute 不能为空。',
    'validation.required_without'     => '当 :values 不存在时 :attribute 不能为空。',
    'validation.required_without_all' => '当 :values 都不存在时 :attribute 不能为空。',
    'validation.same'                 => ':attribute 和 :other 必须相同。',
    'validation.size'                 => [
        'numeric' => ':attribute 大小必须为 :size。',
        'file'    => ':attribute 大小必须为 :size kb。',
        'string'  => ':attribute 必须是 :size 个字符。',
        'array'   => ':attribute 必须为 :size 个单元。',
    ],
    'validation.string'               => ':attribute 必须是一个字符串。',
    'validation.timezone'             => ':attribute 必须是一个合法的时区值。',
    'validation.unique'               => ':attribute 已经存在。',
    'validation.url'                  => ':attribute 格式不正确。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention 'attribute.rule' to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'validation.custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'validation.max_if'               => '当标识为 :fieldValue 时，:attribute 不能大于 :max 个字符',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of 'email'. This simply helps us make messages a little cleaner.
    |
    */

    'validation.attributes'           => [
        'name'                  => '名称',
        'username'              => '用户名',
        'email'                 => '邮箱',
        'first_name'            => '名',
        'last_name'             => '姓',
        'password'              => '密码',
        'password_confirmation' => '确认密码',
        'city'                  => '城市',
        'country'               => '国家',
        'address'               => '地址',
        'phone'                 => '电话',
        'mobile'                => '手机',
        'age'                   => '年龄',
        'sex'                   => '性别',
        'gender'                => '性别',
        'day'                   => '天',
        'month'                 => '月',
        'year'                  => '年',
        'hour'                  => '时',
        'minute'                => '分',
        'second'                => '秒',
        'title'                 => '标题',
        'content'               => '内容',
        'description'           => '描述',
        'excerpt'               => '摘要',
        'date'                  => '日期',
        'time'                  => '时间',
        'available'             => '可用的',
        'size'                  => '大小',
    ],

];
