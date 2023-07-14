<?php

namespace App\Transformers;

class ProductTransformer extends BaseTransformer
{
    protected $availableIncludes = ['warehouse', 'brand', 'act', 'category', 'tags', 'images', 'skus', 'properties', 'detailImages', 'group', 'bargain', 'userBargain', 'reports'];

    public function transformData($model)
    {
        return [
            'id'                    => $model['id'],
            'type'                  => $model['type'],
            'category_id'           => $model['category_id'],
            'warehouse_id'          => $model['warehouse_id'],
            'title'                 => $model['title'],
            'long_title'            => $model['long_title'],
            //'detail' => $model['detail'],
            'cover'                 => !empty($model->cover) ? config('api.img_host') . $model->cover : '',
            'sold_count'            => $model['sold_count'],
            'select_target_count'   => $model['select_target_count'],
            'select_virtual_count'  => $model['select_virtual_count'],
            'select_countdown_days' => $model['select_countdown_days'],
            'select_end_at'         => !empty($model->select_end_at) ? $model->select_end_at->toDateTimeString() : '',
            'stock_count'           => $model['stock_count'],
            'freight'               => $model['freight'],
            'tax_rate'              => $model['tax_rate'],
            'package_limit'         => $model['package_limit'],
            'original_price'        => foodStampValue($model['original_price']),
            'price'                 => foodStampValue($model['price']),
            'on_sale'               => $model['on_sale'],
        ];
    }

    public function includeWarehouse($model)
    {
        return $this->item($model->warehouse, new WarehouseTransformer());
    }

    public function includeBrand($model)
    {
        return $this->item($model->brand, new BrandTransformer());
    }

    public function includeAct($model)
    {
        return $this->item($model->act, new ActTransformer());
    }

    public function includeCategory($model)
    {
        return $this->item($model->category, new CategoryTransformer());
    }

    public function includeImages($model)
    {
        return $this->collection($model->images, new ProductImageTransformer());
    }

    public function includeDetailImages($model)
    {
        return $this->collection($model->detailImages, new ProductDetailImageTransformer());
    }

    public function includeSkus($model)
    {
        return $this->collection($model->skus, new ProductSkuTransformer());
    }


    public function includeProperties($model)
    {
        return $this->collection($model->properties, new ProductPropertyTransformer());
    }

    public function includeTags($model)
    {
        return $this->collection($model->tags, new TagTransformer());
    }

    public function includeGroup($model)
    {
        return $this->item($model->group, new GroupProductTransformer());
    }

    public function includeBargain($model)
    {
        return $this->item($model->bargain, new BargainProductTransformer());
    }

    public function includeUserBargain($model)
    {

        return $this->item($model->userBargain, new BargainTransformer());
    }

    public function includeProductSub($model)
    {
        return $this->item($model->productSub, new ProductSubTransformer());
    }

    public function includeReports($model)
    {
        return $this->collection($model->reports, new ProductReportTransformer());
    }


}
