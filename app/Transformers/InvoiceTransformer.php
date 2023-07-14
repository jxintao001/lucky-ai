<?php

namespace App\Transformers;

class InvoiceTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'                 => $model->id,
            'order_id'           => $model->order_id,
            'name'               => $model->name,
            'tax_no'             => $model->tax_no,
            'bank'               => $model->bank,
            'acount'             => $model->acount,
            'register_address'   => $model->register_address,
            'send_email'         => $model->send_email,
            'send_address'       => $model->send_address,
            'phone'              => $model->phone,
            'type'               => $model->type,
            'send_mode'          => $model->send_mode,
            'status'             => $model->status,
            'remark'             => $model->remark,
            'gift'               => $model->gift,
            'shop_id'            => $model->shop_id,
            'created_at'         => !empty($model->created_at) ? $model->created_at->diffForHumans() : '',
            'updated_at'         => !empty($model->updated_at) ? $model->updated_at->diffForHumans() : '',
        ];
    }


}