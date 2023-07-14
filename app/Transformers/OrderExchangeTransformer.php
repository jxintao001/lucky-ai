<?php

namespace App\Transformers;

class OrderExchangeTransformer extends BaseTransformer
{
    protected $availableIncludes = ['items', 'cards', 'stores'];

    public function transformData($model)
    {
        return [
            "id"              => $model->id,
            "uuid"            => $model->uuid,
            "type"            => $model->type,
            "no"              => $model->no,
            "user_id"         => $model->user_id,
            "address"         => $model->address,
            "freight"         => foodStampValue($model->freight),
            "product_amount"  => foodStampValue($model->product_amount),
            "discount_amount" => foodStampValue($model->discount_amount),
            "total_amount"    => foodStampValue($model->total_amount),
            "is_virtual"      => $model->is_virtual,
            "delivery_method" => $model->delivery_method,
            "remark"          => $model->remark,
            "seller_remark"   => $model->seller_remark,
            "reviewed"        => $model->reviewed,
            "ship_status"     => $model->ship_status,
            "ship_data"       => $model->ship_data,
            "qr_code"         => !empty($model->qr_code) ? config('api.img_host') . $model->qr_code : '',
            "bar_code"        => !empty($model->bar_code) ? config('api.img_host') . $model->bar_code : '',
            "closed"          => $model->closed,
            "created_at"      => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            "updated_at"      => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',

        ];
    }

    public function includeItems($model)
    {
        return $this->collection($model->items, new OrderExchangeItemTransformer());
    }

    public function includeCards($model)
    {
        return $this->collection($model->cards, new OrderItemCardTransformer());
    }

    public function includeStores($model)
    {
        return $this->collection($model->stores, new SupplierStoreTransformer());
    }

}
