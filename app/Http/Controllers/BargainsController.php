<?php

namespace App\Http\Controllers;

use App\Http\Requests\BargainRequest;
use App\Http\Requests\UserAddressRequest;
use App\Models\Bargain;
use App\Models\BargainItem;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Services\BargainService;
use App\Transformers\BargainItemTransformer;
use App\Transformers\BargainTransformer;
use App\Transformers\UserAddressTransformer;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;
use App\Models\User;

class BargainsController extends Controller
{
    public function index(Request $request)
    {
        $status = strval($request['status']);
        $bargains = Bargain::where('user_id', auth('api')->id())->where('shop_id',auth('api')->user()->shop_id);
        if ($status){
            $bargains->where('status',$status);
        }
        $bargains = $bargains
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
        return $this->response()->paginator($bargains, new BargainTransformer());
    }
    public function show($id)
    {
        $bargain = Bargain::findOrFail($id);
        //$this->authorize('own', $bargain);
        $transformer = new BargainTransformer();
        $transformer->setDefaultIncludes(['items']);
        return $this->response()->item($bargain, $transformer);
    }

    public function store(BargainRequest $request, BargainService $bargainService)
    {
        $user    = User::find(auth('api')->id());
        // 判断砍价是否已经被创建
        $bargain = Bargain::withTrashed()
                    ->where('user_id', $user->id)
                    ->where('sku_id', $request->input('sku_id'))
                    ->where('status', Bargain::STATUS_WAITING)
                    ->first();
        if ($bargain && $bargain->trashed()){
            throw new ResourceException('该商品已经创建过砍价，并且砍价记录已经被你删除');
        }
        $status_code = 200;
        if (!$bargain){
            $status_code = 201;
            $sku     = ProductSku::find($request->input('sku_id'));
            $bargain = $bargainService->add($user, $sku);
            $bargain = Bargain::findOrFail($bargain->id);
            $bargainService->cut($user, $bargain);

        }
        $transformer = new BargainTransformer();
        $transformer->setDefaultIncludes(['items']);
        return $this->response()->item($bargain, $transformer)->setStatusCode($status_code);
    }

    public function items(Request $request)
    {
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $bargain_items = BargainItem::where('bargain_id',$request->input('bargain_id'))
            ->where('shop_id', $shop_id)
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());;

        return $this->response()->paginator($bargain_items, new BargainItemTransformer());
    }

    public function cut(Request $request, BargainService $bargainService)
    {
        $user    = User::find(auth('api')->id());
        $bargain = Bargain::find($request->input('bargain_id'));
        if (!$bargain){
            throw new ResourceException('找不到该砍价或者已被删除');
        }
        $bargain_item = $bargainService->cut($user, $bargain);
        $bargain_item = BargainItem::findOrFail($bargain_item->id);
        return $this->response()->item($bargain_item, new BargainItemTransformer());
    }

    public function closed($id)
    {
        // 砍价信息
        $bargain = Bargain::findOrFail($id);
        // 校验权限
        $this->authorize('own', $bargain);
        // 判断砍价状态是否未等待中
        if ($bargain->status != Bargain::STATUS_WAITING) {
            throw new ResourceException('砍价状态为等待中，才能取消砍价');
        }
        // 关闭订单
        $bargain->update(['status' => Bargain::STATUS_FAIL,'closed' => true]);
        return $this->response()->noContent();
    }

    public function destroy($id)
    {
        $bargain = Bargain::findOrFail($id);
        $this->authorize('own', $bargain);
        $bargain->delete();
        return $this->response->noContent();
    }

    public function userIndex(Request $request)
    {
        $status = strval($request['status']);
        $bargains = Bargain::where('user_id', auth('api')->id())->where('shop_id',auth('api')->user()->shop_id)->has('product')->has('productSku')->has('bargainProduct');
        if ($status){
            $bargains->where('status',$status);
        }
        $bargains = $bargains
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
        return $this->response()->paginator($bargains, new BargainTransformer());
    }


}
