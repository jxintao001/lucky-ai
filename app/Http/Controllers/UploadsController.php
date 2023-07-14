<?php

namespace App\Http\Controllers;

use App\Handlers\FileUploadHandler;
use App\Models\ProductSku;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;

class UploadsController extends Controller
{
    public function image(Request $request, FileUploadHandler $uploader)
    {
        // 判断是否有上传文件，并赋值给 $file
        $file = $request->file('file');
        if (!$file) {
            throw new ResourceException('文件不能为空');
        }
        $data['file_path'] = '';
        // 保存图片到本地
        $result = $uploader->saveImage($file);
        // 图片保存成功
        if ($result) {
            if ($request->input('item_type') == 'sku' && !empty($request->input('item_id')) && $sku = ProductSku::find($request->input('item_id'))) {
                $sku->share_image = $result;
                $sku->save();
            }
            $data['file_path'] = config('api.img_host').$result;
        }
        return $data;
    }

    public function audio(Request $request, FileUploadHandler $uploader)
    {
        // 判断是否有上传文件，并赋值给 $file
        $file = $request->file('file');
        if (!$file) {
            throw new ResourceException('文件不能为空');
        }
        $data['file_path'] = '';
        // 保存图片到本地
        $result = $uploader->saveAudio($file);
        // 图片保存成功
        if ($result) {
            $data['file_path'] = config('api.img_host').$result;
        }
        return $data;
    }

    public function video(Request $request, FileUploadHandler $uploader)
    {
        // 判断是否有上传文件，并赋值给 $file
        $file = $request->file('file');
        if (!$file) {
            throw new ResourceException('文件不能为空');
        }
        $data['file_path'] = '';
        // 保存图片到本地
        $result = $uploader->saveVideo($file);
        // 图片保存成功
        if ($result) {
            $data['file_path'] = config('api.img_host').$result;
        }
        return $data;
    }
}
