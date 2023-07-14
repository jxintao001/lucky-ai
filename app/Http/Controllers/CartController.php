<?php

namespace App\Http\Controllers;

use App\Transformers\CartItemTransformer;
use App\Transformers\ProductSkuTransformer;
use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\ProductSku;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    // 利用 Laravel 的自动解析功能注入 CartService 类
    public function __construct(CartService $cartService)
    {
        parent::__construct();
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cart_items = $this->cartService->get();
        return $this->response()->paginator($cart_items, new CartItemTransformer());

    }

    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'), $request->input('amount'));

        return $this->response->created();
    }

    public function dec(AddCartRequest $request)
    {
        $this->cartService->dec($request->input('sku_id'), $request->input('amount'));

        return $this->response->created();
    }

    public function input(AddCartRequest $request)
    {
        $this->cartService->input($request->input('sku_id'), $request->input('amount'));

        return $this->response->created();
    }

    public function remove($sku)
    {
        $this->cartService->remove($sku);
        //$this->cartService->remove($request['sku_ids']);

        return $this->response->noContent();
    }
    
    public function cart_delete(Request $request)
    {
        $sku = $request['sku'];
        $this->cartService->remove($sku);
        //$this->cartService->remove($request['sku_ids']);

        return $this->response->noContent();
    }

}
