<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'required_with' => 'حقل :attribute مطلوب عند تحديد :values.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'email' => 'يجب أن يكون :attribute بريدًا إلكترونيًا صالحًا.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'in' => ':attribute المحدد غير صالح.',
    'exists' => ':attribute المحدد غير صالح.',
    'max' => [
        'numeric' => 'يجب ألا يكون :attribute أكبر من :max.',
        'string' => 'يجب ألا يتجاوز :attribute :max حرفًا.',
    ],
    'min' => [
        'numeric' => 'يجب ألا يقل :attribute عن :min.',
        'string' => 'يجب ألا يقل :attribute عن :min أحرف.',
    ],
    'gt' => [
        'numeric' => 'يجب أن يكون :attribute أكبر من :value.',
    ],
    'lte' => [
        'numeric' => 'يجب أن يكون :attribute أقل من أو يساوي :value.',
    ],
    'attributes' => [],
];