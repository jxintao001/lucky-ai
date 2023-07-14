<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UserIdentityRequest extends Request
{
    public function rules()
    {
        return [
            'real_name' => [
                'required',
                'regex:/^[A-Za-z\x{4e00}-\x{9fa5}]+$/u',
            ],
            'phone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',
            ],
            'idcard_no' => [
                    'required',
                    'regex:/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/',
                ]
        ];
    }

    public function attributes()
    {
        return [
            'real_name'     => '姓名',
            'phone'         => '手机号',
            'idcard_no'     => '身份证号',
        ];
    }

    /*public function messages()
    {
        return [
            'contact_phone.required' => '请填写电话'
        ];
    }*/
}
