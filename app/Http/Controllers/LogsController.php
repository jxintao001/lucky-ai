<?php

namespace App\Http\Controllers;

use App\Models\Bargain;
use app\models\User;
use App\Models\UserLogs;
use App\Transformers\BargainTransformer;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        $status = strval($request['status']);
        $bargains = Bargain::where('user_id', auth('api')->id())->where('shop_id', auth('api')->user()->shop_id);
        if ($status) {
            $bargains->where('status', $status);
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

    public function store()
    {
        $shop_id = request('shop_id');
        if (!$shop_id) {
            throw new ResourceException('店铺id不能为空');
        }
        // 记录log
        $item = new UserLogs([
            'action' => 'start',
            'user_id' => auth('api')->id(),
            'shop_id' => intval($shop_id),
        ]);
        $item->save();
        // 更新最后登录的shop_id
        $user = User::find(auth('api')->id());
        $user->update([
            'last_shop_id' => intval($shop_id)
        ]);
        return $this->response->created();
    }


}
