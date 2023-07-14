<?php

namespace App\Transformers;

class SupplierStoreTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'             => $model->id,
            'type'           => $model->type,
            'name'           => $model->name,
            'contact_name'   => $model->contact_name,
            'contact_mobile' => $model->contact_mobile,
            'province'       => $model->province,
            'city'           => $model->city,
            'district'       => $model->district,
            'address'        => $model->address,
            'info_remark'    => $model->info_remark,
            'created_at'     => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'     => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }

}