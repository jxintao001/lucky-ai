<?php

namespace App\Http\Controllers;

use App\Models\GiftPackage;
use App\Models\GiftPackageReceive;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\UserGift;
use App\Services\UserGiftService;
use App\Transformers\GiftPackageReceiveTransformer;
use App\Transformers\GiftPackageTransformer;
use App\Transformers\OrderTransformer;
use App\Transformers\UserAddressTransformer;
use App\Transformers\UserGiftsTransformer;

class UserGiftsController extends Controller
{
    public function index()
    {
        $query = UserGift::where('user_id', auth('api')->id())
            ->where('count', '>', 0)
            ->with('productSku');
        $query->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc');
        $user_gifts = $query->paginate(per_page());
        return $this->response()->paginator($user_gifts, new UserGiftsTransformer());
    }

    public function receive()
    {
        $receives = GiftPackageReceive::query()->where('user_id', auth('api')->id())
            ->where('shop_id', auth('api')->user()->shop_id)
            ->has('gifts')
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
        $transformer = new GiftPackageReceiveTransformer();
        $transformer->setDefaultIncludes(['gifts']);
        return $this->response()->paginator($receives, $transformer);
    }

    public function buy()
    {
        $orders = Order::query()->where('user_id', auth('api')->id())
            ->where('shop_id', auth('api')->user()->shop_id)
            ->whereNotNull('paid_at')->where('closed', '0')
            ->where('split', 0)
            ->has('gifts')
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
        $transformer = new OrderTransformer();
        $transformer->setDefaultIncludes(['gifts']);
        return $this->response()->paginator($orders, $transformer);
    }

    public function give()
    {
        $packages = GiftPackage::query()->where('user_id', auth('api')->id())
            ->where('shop_id', auth('api')->user()->shop_id)
            ->has('gifts')
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
        $transformer = new GiftPackageTransformer();
        $transformer->setDefaultIncludes(['items']);
        return $this->response()->paginator($packages, $transformer);
    }

    public function show($id, UserGiftService $userGiftService)
    {
        dd($userGiftService->orderToGift($id));
        $user_address = UserAddress::findOrFail($id);
        $this->authorize('own', $user_address);
        return $this->response()->item($user_address, new UserAddressTransformer());
    }


}
