<?php

namespace App\Transformers;

class OrderTransformer extends BaseTransformer
{
    protected $availableIncludes = ['items', 'gifts'];

    public function transformData($model)
    {
        return [
            "id"                     => $model->id,
            "uuid"                   => $model->uuid,
            "type"                   => $model->type,
            "no"                     => $model->no,
            "user_id"                => $model->user_id,
            "address"                => $model->address,
            //"identity"               => $model->identity,
            "freight"                => foodStampValue($model->freight),
            "product_amount"         => foodStampValue($model->product_amount),
            "tax_amount"             => foodStampValue($model->tax_amount),
            "coupon_amount"          => foodStampValue($model->coupon_amount),
            "discount_amount"        => foodStampValue($model->discount_amount),
            "integral_amount"        => foodStampValue($model->integral_amount),
            "total_amount"           => foodStampValue($model->total_amount),
            "remark"                 => $model->remark,
            "seller_remark"          => $model->seller_remark,
            "paid_at"                => !empty($model->paid_at) ? $model->paid_at->toDateTimeString() : '',
            "coupon_code_id"         => $model->coupon_code_id,
            "payment_method"         => $model->payment_method,
            "payment_no"             => $model->payment_no,
            "status"                 => $model->status,
            "refund_status"          => $model->refund_status,
            "refund_no"              => $model->refund_no,
            "reviewed"               => $model->reviewed,
            "ship_status"            => $model->ship_status,
            "ship_data"              => $model->ship_data,
            "gift_package_no"        => $model->gift_package_no,
            "gift_package_code"      => $model->gift_package_code,
            "closed"                 => $model->closed,
            "is_comment"             => $model->is_comment,
            "is_invoice"             => $model->is_invoice,
            "payment_count_down"     => $model->paymentCountDown($model->created_at),
            "payment_count_down_str" => '',
            "delivered_at"           => !empty($model->delivered_at) ? $model->delivered_at->toDateTimeString() : '',
            "received_at"            => !empty($model->received_at) ? $model->received_at->toDateTimeString() : '',
            "finished_at"            => !empty($model->finished_at) ? $model->finished_at->toDateTimeString() : '',
            "created_at"             => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            "updated_at"             => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',

        ];
    }

    public function includeItems($model)
    {
        return $this->collection($model->items, new OrderItemTransformer());
    }

    public function includeGifts($model)
    {
        return $this->collection($model->gifts, new UserGiftItemsTransformer());
    }


}
