<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSecondaryCard;
use App\Services\CardService;
use App\Transformers\CardInfoTransformer;
use App\Transformers\MasterCardTransformer;
use App\Transformers\SecondaryCardTransformer;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;

class CardsController extends Controller
{
    protected $cardService;

    public function __construct(CardService $cardService)
    {
        parent::__construct();
        $this->cardService = $cardService;
    }

    public function index(Request $request)
    {
        $type = $request->input('type');
        if (!$type) {
            throw new ResourceException('type 参数不能为空');
        }
        if (!in_array($type, ['secondary', 'master'])) {
            throw new ResourceException('type 参数错误');
        }
        $card_items = $this->cardService->get(auth('api')->user(), $type);
        if ($type == 'secondary') {
            $cardTransformer = new SecondaryCardTransformer();
        } elseif ($type == 'master') {
            $cardTransformer = new MasterCardTransformer();
        }
        return $this->response()->paginator($card_items, $cardTransformer);

    }

    public function store(Request $request)
    {

        $user = User::findOrFail(auth('api')->id());
        $card = $this->cardService->store($user);
        $card = UserSecondaryCard::query()->find($card->id);
        return $this->response()->item($card, new SecondaryCardTransformer());

    }

    public function bind(Request $request)
    {
        $user = User::findOrFail(auth('api')->id());
        if (!$request->input('card_uuid')) {
            throw new ResourceException('card_uuid不能为空');
        }
        $card = $this->cardService->bind($request->input('card_uuid'), $user);

        return $this->response()->item($card, new MasterCardTransformer());
    }

    public function unbind(Request $request)
    {
        $user = User::findOrFail(auth('api')->id());
        if (!$request->input('card_uuid')) {
            throw new ResourceException('card_uuid不能为空');
        }
        $this->cardService->unbind($request->input('card_uuid'), $user);

        return $this->response->created();
    }

    public function default(Request $request)
    {
        $user = User::findOrFail(auth('api')->id());
        if (!$request->input('card_uuid')) {
            throw new ResourceException('card_uuid不能为空');
        }
        $this->cardService->default($request->input('card_uuid'), $user);

        return $this->response->created();
    }

    public function undefault(Request $request)
    {
        $user = User::findOrFail(auth('api')->id());
        if (!$request->input('card_uuid')) {
            throw new ResourceException('card_uuid不能为空');
        }
        $this->cardService->undefault($request->input('card_uuid'), $user);

        return $this->response->created();
    }

    public function open(Request $request)
    {
        $user = User::findOrFail(auth('api')->id());
        if (!$request->input('card_uuid')) {
            throw new ResourceException('card_uuid不能为空');
        }
        $this->cardService->open($request->input('card_uuid'), $user);

        return $this->response->created();
    }

    public function close(Request $request)
    {
        $user = User::findOrFail(auth('api')->id());
        if (!$request->input('card_uuid')) {
            throw new ResourceException('card_uuid不能为空');
        }
        $this->cardService->close($request->input('card_uuid'), $user);

        return $this->response->created();
    }

    public function destroy($card_uuid)
    {
        $user = User::findOrFail(auth('api')->id());
        if (!$card_uuid) {
            throw new ResourceException('card_uuid不能为空');
        }
        $this->cardService->destroy($card_uuid, $user);

        return $this->response->noContent();
    }

    public function show($card_uuid)
    {
        if (!$card_uuid) {
            throw new ResourceException('card_uuid不能为空');
        }
        $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->first();
        if (!$card) {
            throw new ResourceException('无效的card_uuid');
        }
        $card->other_bound_status = 0;
        if (auth('api')->id() && UserSecondaryCard::query()
                ->where('uuid','<>', $card_uuid)
                ->where('master_user_id', $card->master_user_id)
                ->where('user_id', auth('api')->id())
                ->exists()){
            $card->other_bound_status = 1;
        }
        return $this->response()->item($card, new CardInfoTransformer());
    }

}
