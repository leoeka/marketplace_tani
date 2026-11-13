<?php

return [
    'required' => ':attribute 为必填项。',
    'required_with' => '填写 :values 时，:attribute 为必填项。',
    'string' => ':attribute 必须是文本。',
    'email' => ':attribute 必须是有效的电子邮件地址。',
    'integer' => ':attribute 必须是整数。',
    'numeric' => ':attribute 必须是数字。',
    'in' => '所选的 :attribute 无效。',
    'exists' => '所选的 :attribute 无效。',
    'max' => [
        'numeric' => ':attribute 不得大于 :max。',
        'string' => ':attribute 不得超过 :max 个字符。',
    ],
    'min' => [
        'numeric' => ':attribute 不得小于 :min。',
        'string' => ':attribute 至少需要 :min 个字符。',
    ],
    'gt' => [
        'numeric' => ':attribute 必须大于 :value。',
    ],
    'lte' => [
        'numeric' => ':attribute 必须小于或等于 :value。',
    ],
    'attributes' => [],
];