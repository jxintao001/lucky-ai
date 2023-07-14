<?php

namespace App\Transformers;

class ProductSkuTransformer extends BaseTransformer
{
    protected $availableIncludes = ['product', 'group', 'bargain'];

    public function transformData($model)
    {
        return [
            'id'             => $model->id,
            'no'             => $model->no,
            'title'          => $model->title,
            'description'    => $model->description,
            'original_price' => foodStampValue($model->original_price),
            'tax_price'      => foodStampValue($model->tax_price),
            'price'          => foodStampValue($model->tax_price),
            'weight'         => $model->weight,
//            'tax_price' => $model->tax_price,
//            'price' => $model->price,
//            'tax_rate' => $model->tax_rate,
//            'tax' => sprintf("%.2f", $model->tax_price - $model->price), //$model->getCalculateTax($model->price),
//            'club_tax_price' => $model->club_tax_price,
//            'club_price' => $model->club_price,
//            'club_tax_rate' => $model->club_tax_rate,
//            'club_tax' => sprintf("%.2f", $model->club_tax_price - $model->club_price), //$model->getCalculateTax($model->club_price),
            'limit_buy'      => $model->limit_buy,
            'limit_num'      => $model->limit_num,
            'stock'          => $model->stock,
            'is_presale'     => $model->is_presale,
            'presale'        => $model->presale,
            "deliver_at"     => !empty($model->deliver_at) ? $model->deliver_at->toDateTimeString() : '',
            'product_id'     => $model->product_id,
            'share_image'    => $model->share_image,
        ];
    }

    public function includeProduct($model)
    {
        return $this->item($model->product, new ProductTransformer());
    }

    public function includeGroup($model)
    {
        return $this->item($model->group, new GroupProductTransformer());
    }

    public function includeBargain($model)
    {
        return $this->item($model->bargain, new BargainProductTransformer());
    }

    public function includeSkuSub($model)
    {

        return $this->item($model->productSkuSub, new ProductSkuSubTransformer());
    }
}