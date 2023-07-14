<?php

namespace App\Http\Requests;

class OrderRechargeRequest extends Request
{
    public function rules()
    {
        return [
            'money' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'money' => '金额',
        ];
    }
}
