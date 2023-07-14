<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadSftpHandler;
use App\Http\Requests\UserAddressRequest;
use App\Http\Requests\UserIdentityRequest;
use App\Models\UserAddress;
use App\Models\UserIdentity;
use App\Transformers\UserAddressTransformer;
use App\Transformers\UserIdentityTransformer;
use Illuminate\Http\Request;
use App\Models\User;

class UserIdentitiesController extends Controller
{
    public function index(Request $request)
    {
        $identities = UserIdentity::where('user_id', auth('api')->id());
        if ($request['is_default'] == 1){
            $identities->where('is_default',1);
        }
        $identities->orderBy('is_default', 'desc')
            ->orderBy('last_used_at', 'desc')
            ->orderBy('created_at', 'desc');
        $identities = $identities->paginate(per_page());
        return $this->response()->paginator($identities, new UserIdentityTransformer());
    }
    public function show($id)
    {
        $identity = UserIdentity::findOrFail($id);
        $this->authorize('own', $identity);
        return $this->response()->item($identity, new UserIdentityTransformer());
    }

    public function store(UserIdentityRequest $request, ImageUploadSftpHandler $uploader)
    {

        $data = $request->only([
            'real_name',
            'phone',
            'idcard_no',
            'is_default',
        ]);
        // 判断是否上传图片
        if ($request->file('idcard_front') && $result = $uploader->save($request->file('idcard_front'))){
            $data['idcard_front'] = $result;
        }
        if ($request->file('idcard_back') && $result = $uploader->save($request->file('idcard_front'))){
            $data['idcard_back'] = $result;
        }
        $identity = $request->user()->identities()->create($data);
        if ($request['is_default'] == 1 && $identity){
            $request->user()->identities()->where('id','!=',$identity->id)->where('is_default',1)->update(
                ['is_default' => 0]
            );
        }
        $identity = UserIdentity::findOrFail($identity->id);
        return $this->response()->item($identity, new UserIdentityTransformer());
    }

    public function update($id, UserIdentityRequest $request, ImageUploadSftpHandler $uploader)
    {
        $identity = UserIdentity::findOrFail($id);
        $this->authorize('own', $identity);
        $data = $request->only([
            'real_name',
            'phone',
            'idcard_no',
            'is_default',
        ]);
        // 判断是否上传图片
        if ($request->file('idcard_front') && $result = $uploader->save($request->file('idcard_front'))){
            $data['idcard_front'] = $result;
        }
        if ($request->file('idcard_back') && $result = $uploader->save($request->file('idcard_front'))){
            $data['idcard_back'] = $result;
        }
        $identity->update($data);
        if ($request['is_default'] == 1 && $identity){
            $request->user()->identities()->where('id','!=',$identity->id)->where('is_default',1)->update(
                ['is_default' => 0]
            );
        }
        return $this->response()->item($identity, new UserIdentityTransformer());
    }

    public function destroy($id)
    {
        $identity = UserIdentity::findOrFail($id);
        $this->authorize('own', $identity);
        $identity->delete();
        return $this->response->noContent();
    }


}
