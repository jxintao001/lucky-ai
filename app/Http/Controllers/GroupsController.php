<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use App\Models\GroupItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Transformers\GroupItemTransformer;
use App\Transformers\GroupTransformer;
use App\Transformers\ProductTransformer;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    public function index(Request $request)
    {
        $sku_id = intval($request['sku_id']);
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $groups = Group::where('sku_id', $sku_id)->where('shop_id', $shop_id)->where('status',Group::STATUS_WAITING)->paginate(per_page());
        return $this->response()->paginator($groups, new GroupTransformer());
    }

    public function show($id)
    {
        $group = Group::findOrFail($id);
        return $this->response()->item($group, new GroupTransformer());
    }

    public function userIndex(Request $request)
    {

        $status = strval($request['status']);
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $group_items = GroupItem::where('user_id', auth('api')->id())
            ->where('status','<>',GroupItem::STATUS_PENDING)
            ->where('shop_id',auth('api')->user()->shop_id)
            ->whereNotNull('paid_at')
            ->has('product')
            ->has('productSku')
            ->has('groupProduct')
            ->has('order');
        if ($status){
            $group_items->where('status',$status);
        }
        $group_items = $group_items
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
        //print_r(DB::getQueryLog());exit();
        return $this->response()->paginator($group_items, new GroupItemTransformer());
    }

    public function closed($id, OrderService $orderService)
    {
        // 团购信息
        $group_item = GroupItem::findOrFail($id);
        // 校验权限
        $this->authorize('own', $group_item);
        // 判断团购状态是否未等待中
        if ($group_item->status != GroupItem::STATUS_WAITING) {
            throw new ResourceException('团购状态为等待中，才能取消');
        }
        // 关闭
        $group_item->update(['status' => GroupItem::STATUS_FAIL,'closed' => true]);
        // 退款
        $order = Order::findOrFail($group_item->order_id);
        $orderService->refundOrder($order);
        // 如果该团购已经无人等待，关闭主团信息
        $group = Group::where('id', $group_item->group_id)->first();
        $group_items = GroupItem::where('group_id', $group->id)
            ->whereNotNull('paid_at')
            ->where('closed',false)
            ->get();
        $data = [];
        $data['user_count'] = count($group_items);
        if (count($group_items) <= 0){
            $data['status'] = Group::STATUS_FAIL;
            $data['closed'] = true;
        }
        $group->update($data);
        return $this->response()->noContent();
    }

    public function destroy($id)
    {
        // 团购信息
        $group_item = GroupItem::findOrFail($id);
        // 校验权限
        $this->authorize('own', $group_item);
        // 判断团购状态是否为失败
        if ($group_item->status != GroupItem::STATUS_FAIL) {
            throw new ResourceException('团购状态为失败，才能删除');
        }
        $group_item->delete();
        return $this->response->noContent();
    }

}