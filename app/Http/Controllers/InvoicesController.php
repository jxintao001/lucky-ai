<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserFollow;
use App\Transformers\InvoiceTransformer;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->input('ua')){
            return $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        $type = $request->input('type', 'hot');
        $order_id = $request->input('order_id');
        $sku_id = $request->input('sku_id');
        if (!in_array($type, ['hot', 'follow', 'comment'])) {
            throw new ResourceException('type参数错误');
        }

        if ($type == 'hot') {
            $query = Post::query()->with('reviewComments')->where('is_blocked', 0)->where('review', 1);
            // 更新用户动态浏览时间
            if (auth('api')->id()) {
                $user = User::query()->find(auth('api')->id());
                $user->last_post_read_at = Carbon::now();
                $user->save();
            }
        } elseif ($type == 'follow') {
            if (!auth('api')->id()) {
                throw new ResourceException('用户未授权登录');
            }
            $query = Post::query()->with('reviewComments')->where('is_blocked', 0)->where('review', 1);
            $user_follows = UserFollow::query()->where('user_id', auth('api')->id())->pluck('item_id')->toArray();
            $query->whereIn('user_id', $user_follows);
            // 更新用户动态浏览时间
            if (auth('api')->id()) {
                $user = User::query()->find(auth('api')->id());
                $user->last_post_read_at = Carbon::now();
                $user->save();
            }
        } elseif ($type == 'comment') {
            $query = Post::query()->with('reviewComments')->where('is_blocked', 0)->where('review', 1);
            if ($order_id) {
                $query->where('order_id', $order_id);
            }
            if ($sku_id) {
                $query->where('product_sku_ids', 'like', '%-' . $sku_id . '-%');
            }
        }

        $posts = $query->orderByDesc('id')->paginate(per_page());
        return $this->response()->paginator($posts, new PostTransformer());

    }

    public function store(Request $request)
    {
        $order_id = $request->input('order_id');
        $name = $request->input('name');
        $tax_no = $request->input('tax_no');
        $bank = !empty($request['bank']) ? $request['bank'] : '';
        $acount = !empty($request['acount']) ? $request['acount'] : '';
        $register_address = !empty($request['register_address']) ? $request['register_address'] : '';
        $send_email= !empty($request['send_email']) ? $request['send_email'] : '';
        $send_address = !empty($request['send_address']) ? $request['send_address'] : '';
        $phone = !empty($request['phone']) ? $request['phone'] : '';
        $type = $request->input('type');
        $send_mode = $request->input('send_mode');
        $remark = !empty($request['remark']) ? $request['remark'] : '';
       
        if (!$order_id) {
            throw new ResourceException('订单不能为空');
        }
        if (!$name) {
            throw new ResourceException('抬头不能为空');
        }
        if (!$tax_no) {
            throw new ResourceException('税号不能为空');
        }

//        $order = Order::query()->find($order_id);
//        if (!$order) {
//            throw new ResourceException('无效的order_id');
//        }

        $invoice = new Invoice([
            'order_id'          => $order_id,
            'user_id'           => auth('api')->id(),
            'name'              => $name,
            'tax_no'            => $tax_no,
            'bank'              => $bank,
            'register_address'  => $register_address,
            'send_email'        => $send_email,
            'send_address'      => $send_address,
            'phone'             => $phone,
            'type'              => $type,
            'send_address'      => $send_address,
            'send_mode'         => $send_mode,
            'remark'            => $remark,
        ]);
        // 写入数据库
        $invoice->save();
        // 更新订单评论状态
        $order = Order::query()->find($order_id);
        if ($order){
            $order->is_invoice = 1;
            $order->save();
        }
        $invoice = Invoice::query()->find($invoice->id);
        return $this->response()->item($invoice, new InvoiceTransformer());
    }

    public function show($order_id)
    {
//        $invoice = Invoice::findOrFail($id);
        $invoice = Invoice::where('order_id', $order_id)->first();
        return $this->response()->item($invoice, new InvoiceTransformer());
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice->user_id != auth('api')->id()) {
            throw new AccessDeniedHttpException('只能删除自己的开票');
        }
        $invoice->delete();
        return $this->response->noContent();
    }

}