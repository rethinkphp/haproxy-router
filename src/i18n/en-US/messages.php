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
    'validation.in_gender'            => 'The :attribute is invalid, only 男 and 女 are allowed',
    'validation.accepted'             => 'The :attribute must be accepted.',
    'validation.active_url'           => 'The :attribute is not a valid URL.',
    'validation.after'                => 'The :attribute must be a date after :date.',
    'validation.alpha'                => 'The :attribute may only contain letters.',
    'validation.alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'validation.alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'validation.array'                => 'The :attribute must be an array.',
    'validation.before'               => 'The :attribute must be a date before :date.',
    'validation.between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'validation.boolean'              => 'The :attribute field must be true or false.',
    'validation.confirmed'            => 'The :attribute confirmation does not match.',
    'validation.date'                 => 'The :attribute is not a valid date.',
    'validation.date_format'          => 'The :attribute does not match the format :format.',
    'validation.different'            => 'The :attribute and :other must be different.',
    'validation.digits'               => 'The :attribute must be :digits digits.',
    'validation.digits_between'       => 'The :attribute must be between :min and :max digits.',
    'validation.email'                => 'The :attribute must be a valid email address.',
    'validation.exists'               => 'The selected :attribute is invalid.',
    'validation.filled'               => 'The :attribute field is required.',
    'validation.image'                => 'The :attribute must be an image.',
    'validation.in'                   => 'The selected :attribute is invalid.',
    'validation.in_array'             => 'The :attribute field does not exist in :other.',
    'validation.integer'              => 'The :attribute must be an integer.',
    'validation.ip'                   => 'The :attribute must be a valid IP address.',
    'validation.json'                 => 'The :attribute must be a valid JSON string.',
    'validation.max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'validation.mimes'                => 'The :attribute must be a file of type: :values.',
    'validation.min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'validation.not_in'               => 'The selected :attribute is invalid.',
    'validation.numeric'              => 'The :attribute must be a number.',
    'validation.regex'                => 'The :attribute format is invalid.',
    'validation.required'             => 'The :attribute field is required.',
    'validation.required_if'          => 'The :attribute field is required when :other is :value.',
    'validation.required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'validation.required_with'        => 'The :attribute field is required when :values is present.',
    'validation.required_with_all'    => 'The :attribute field is required when :values is present.',
    'validation.required_without'     => 'The :attribute field is required when :values is not present.',
    'validation.required_without_all' => 'The :attribute field is required when none of :values are present.',
    'validation.same'                 => 'The :attribute and :other must match.',
    'validation.size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'validation.string'               => 'The :attribute must be a string.',
    'validation.timezone'             => 'The :attribute must be a valid zone.',
    'validation.unique'               => 'The :attribute has already been taken.',
    'validation.url'                  => 'The :attribute format is invalid.',

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

    'validation.custom' => [
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

    'attributes' => [],

];
